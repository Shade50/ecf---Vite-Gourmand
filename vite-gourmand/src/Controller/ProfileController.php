<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderStatusHistory;
use App\Entity\User;
use App\Form\ProfileType;
use App\Service\DeliveryFeeCalculator;
use App\Service\MailService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;




final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPassword
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('plainPassword')->getData();

            if ($plainPassword) {
                $user->setPassword(
                    $userPassword->hashPassword($user, $plainPassword)
                );
            }

            $entityManager->flush();
            $this->addFlash(
                'success',
                'Vos informations ont était modifiées.'
            );

            return $this->redirectToRoute('app_profile');
        }
        return $this->render('profile/index.html.twig', [
            'form' => $form,
            'orders' => $user->getOrders(),

        ]);
    }
    #[route(
        '/profil/commande/{id}',
        name: 'app_profile_order_detail',
        requirements: ['id' => '\d+'],
        methods: ['get']
    )]
    public function orderDetail(order $order): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        if ($order->getUser() != $user) {
            throw $this->createAccessDeniedException(
                'Vous ne pouvez pas consulter cette commande.'
            );
        }

        return $this->render('profile/order_detail.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route(
        '/profile/commande/{id}/modifier',
        name: 'app_profile_order_edit',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    public function editOrder(
        Order $order,
        Request $request,
        EntityManagerInterface $entityManager,
        DeliveryFeeCalculator $deliveryFeeCalculator,
        MailService $mailService
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        if ($order->getUser() !== $user) {
            throw $this->createAccessDeniedException(
                'Vous ne pouvez pas modifier cette commande.'
            );
        }

        if (!$this->isCsrfTokenValid(
            'update-order-' . $order->getId(),
            (string) $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton de sécurité invalide.'
            );
        }

        /*
     * Une commande qui n’est plus en attente
     * ne peut plus être modifiée.
     */
        if ($order->getStatus() !== 'En attente') {
            $this->addFlash(
                'danger',
                'Cette commande ne peut plus être modifiée.'
            );

            return $this->redirectToRoute(
                'app_profile_order_detail',
                ['id' => $order->getId()]
            );
        }

        $deliveryDateValue = trim(
            (string) $request->request->get('deliveryDate')
        );

        $deliveryAddress = trim(
            (string) $request->request->get('deliveryAdresse')
        );

        $numberOfPeople = (int) $request->request->get(
            'numberOfPeople'
        );

        $deliveryDate = DateTimeImmutable::createFromFormat(
            'Y-m-d\TH:i',
            $deliveryDateValue
        );

        if ($deliveryDate === false) {
            $this->addFlash(
                'danger',
                'La date de livraison est invalide.'
            );

            return $this->redirectToRoute(
                'app_profile_order_detail',
                ['id' => $order->getId()]
            );
        }

        if ($deliveryAddress === '') {
            $this->addFlash(
                'danger',
                'L’adresse de livraison est obligatoire.'
            );

            return $this->redirectToRoute(
                'app_profile_order_detail',
                ['id' => $order->getId()]
            );
        }

        $menu = $order->getMenu();

        $minimumPerson = max(
            1,
            (int) $menu->getMinimumPerson()
        );

        if ($numberOfPeople < $minimumPerson) {
            $this->addFlash(
                'danger',
                sprintf(
                    'Ce menu nécessite au minimum %d personnes.',
                    $minimumPerson
                )
            );

            return $this->redirectToRoute(
                'app_profile_order_detail',
                ['id' => $order->getId()]
            );
        }

        /*
     * Calcul de la quantité de stock utilisée avant
     * et après la modification.
     */
        $oldStockQuantity = (int) ceil(
            $order->getNumberOfPeople() / $minimumPerson
        );

        $newStockQuantity = (int) ceil(
            $numberOfPeople / $minimumPerson
        );

        $stockDifference = $newStockQuantity - $oldStockQuantity;

        if (
            $stockDifference > 0
            && $menu->getStock() < $stockDifference
        ) {
            $this->addFlash(
                'danger',
                'Le stock disponible est insuffisant pour cette modification.'
            );

            return $this->redirectToRoute(
                'app_profile_order_detail',
                ['id' => $order->getId()]
            );
        }

        /*
     * Nouveau calcul des frais de livraison.
     */
        $coordinates = $deliveryFeeCalculator->geocodeAddress(
            $deliveryAddress
        );

        if ($coordinates === null) {
            $this->addFlash(
                'danger',
                'L’adresse de livraison est introuvable.'
            );

            return $this->redirectToRoute(
                'app_profile_order_detail',
                ['id' => $order->getId()]
            );
        }

        $distance = $deliveryFeeCalculator->calculateDistance(
            $coordinates
        );

        if ($distance === null) {
            $this->addFlash(
                'danger',
                'Impossible de calculer l’itinéraire de livraison.'
            );

            return $this->redirectToRoute(
                'app_profile_order_detail',
                ['id' => $order->getId()]
            );
        }

        $deliveryFee = $deliveryFeeCalculator->calculateDeliveryFee(
            $distance
        );

        /*
     * Nouveau calcul du prix.
     */
        $menuPrice = (float) $menu->getPrice();
        $pricePerPerson = $menuPrice / $minimumPerson;
        $mealPrice = $pricePerPerson * $numberOfPeople;

        if ($numberOfPeople >= $minimumPerson + 5) {
            $mealPrice *= 0.90;
        }

        $totalPrice = $mealPrice + $deliveryFee;

        /*
     * Enregistrement de la modification.
     */
        $menu->setStock(
            $menu->getStock() - $stockDifference
        );

        $order->setDeliveryDate($deliveryDate);
        $order->setNumberOfPeople($numberOfPeople);
        $order->setDeliveryAdresse($deliveryAddress);
        $order->setTotalPrice(
            number_format($totalPrice, 2, '.', '')
        );

        $entityManager->flush();
        $mailService->sendOrderUpdated($order);

        $this->addFlash(
            'success',
            'Votre commande a bien été modifiée.'
        );

        return $this->redirectToRoute(
            'app_profile_order_detail',
            ['id' => $order->getId()]
        );
    }

    #[Route(
        '/profile/commande/{id}/annuler',
        name: 'app_profile_order_cancel',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    public function cancelOrder(
        Order $order,
        Request $request,
        EntityManagerInterface $entityManager,
        MailService $mailService
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        if ($order->getUser() !== $user) {
            throw $this->createAccessDeniedException(
                'Vous ne pouvez pas annuler cette commande.'
            );
        }

        if (!$this->isCsrfTokenValid(
            'cancel-order-' . $order->getId(),
            (string) $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton de sécurité invalide.'
            );
        }

        /*
     * Une commande qui n’est plus en attente
     * ne peut plus être annulée.
     */
        if ($order->getStatus() !== 'En attente') {
            $this->addFlash(
                'danger',
                'Cette commande ne peut plus être annulée.'
            );

            return $this->redirectToRoute(
                'app_profile_order_detail',
                ['id' => $order->getId()]
            );
        }

        $oldStatus = $order->getStatus();
        $menu = $order->getMenu();

        $minimumPerson = max(
            1,
            (int) $menu->getMinimumPerson()
        );

        /*
     * Remise en stock de la quantité réservée.
     */
        $stockToRestore = (int) ceil(
            $order->getNumberOfPeople() / $minimumPerson
        );

        $menu->setStock(
            $menu->getStock() + $stockToRestore
        );

        $order->setStatus('Annulée');

        /*
     * Historique du changement de statut.
     */
        $history = new OrderStatusHistory();

        $history->setCommande($order);
        $history->setOldStatus($oldStatus);
        $history->setNewStatus('Annulée');
        $history->setChangedAt(new DateTimeImmutable());
        $history->setChangedBy($user);

        $entityManager->persist($history);
        $entityManager->flush();
        $mailService->sendOrderCancelled($order);

        $this->addFlash(
            'success',
            'Votre commande a bien été annulée.'
        );

        return $this->redirectToRoute('app_profile');
    }
}
