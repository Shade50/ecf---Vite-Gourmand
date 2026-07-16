<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderStatusHistory;
use App\Repository\OrderRepository;
use App\Service\DeliveryFeeCalculator;
use App\Service\MailService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;







final class EmployeeController extends AbstractController
{
    #[Route('/employee/orders', name: 'app_employee_orders', methods: ['GET'])]
    public function index(
        Request $request,
        OrderRepository $orderRepository,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

        $search = trim((string) $request->query->get('search', ''));
        $status = trim((string) $request->query->get('status', ''));

        $orders = $orderRepository->findForEmployee(
            $search,
            $status
        );


        return $this->render('employee/show_commande.html.twig', [
            'orders' => $orders,
            'search' => $search,
            'currentStatus' => $status,
            'statuses' => [
                'En attente',
                'Acceptée',
                'en préparation',
                'En cours de livraison',
                'En attente du retour de matériel',
                'Terminée',
                'Annulée'
            ],
        ]);
    }

    // #[Route('/employee', name: 'app_employee_index', methods: ['GET'])]
    // public function index(
    //     Request $request,
    //     OrderRepository $orderRepository,
    // ): Response {
    //     $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

    //     $search = trim((string) $request->query->get('search', ''));
    //     $status = trim((string) $request->query->get('status', ''));

    //     $orders = $orderRepository->findForEmployee(
    //         $search,
    //         $status
    //     );


    //     return $this->render('employee/index.html.twig', [
    //         'orders' => $orders,
    //         'search' => $search,
    //         'currentStatus' => $status,
    //         'statuses' => [
    //             'En attente',
    //             'Acceptée',
    //             'en préparation',
    //             'En cours de livraison',
    //             'En attente du retour de matériel',
    //             'Terminée',
    //             'Annulée'
    //         ],
    //     ]);
    // }

    #[Route('/employee', name: 'app_employee_index')]
    public function orders(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');
        return $this->render('employee/index.html.twig');
    }

    #[Route(
        '/employee/commande/{id}',
        name: 'app_employee_order_show',
        methods: ['GET']
    )]
    public function show(Order $order): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

        return $this->render('employee/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route(
        '/employee/commande/{id}/statut',
        name: 'app_employee_order_status',
        methods: ['POST']
    )]
    public function updateStatus(
        Order $order,
        Request $request,
        EntityManagerInterface $entityManager,
        MailService $mailService,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');
        $oldStatus = $order->getStatus();

        // dd($request->request->all());

        if (!$this->isCsrfTokenValid(
            'update-status-' . $order->getId(),
            (string) $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton de sécurité invalide.'
            );
        }

        $availableStatuses = [
            'En attente',
            'Acceptée',
            'En préparation',
            'En cours de livraison',
            'Livrée',
            'En attente du retour de matériel',
            'Terminée',
            'Annulée',
        ];

        $newStatus = trim(
            (string) $request->request->get('status')
        );

        if (!in_array($newStatus, $availableStatuses, true)) {
            $this->addFlash(
                'danger',
                'Le statut sélectionné est invalide.'
            );

            return $this->redirectToRoute(
                'app_employee_order_show',
                ['id' => $order->getId()]
            );
        }

        $order->setStatus($newStatus);

        $newStatus = $order->getStatus();

        if ($oldStatus !== $newStatus) {
            $history = new OrderStatusHistory();

            $history->setCommande($order);
            $history->setOldStatus($oldStatus);
            $history->setNewStatus($newStatus);
            $history->setChangedAt(new \DateTimeImmutable());
            $history->setChangedBy($this->getUser());

            $entityManager->persist($history);
        }

        $entityManager->flush();

        if ($newStatus === 'Acceptée') {
            $mailService->sendOrderAccepted($order);
        }

        if ($newStatus === 'Terminée') {
            $mailService->sendOrderFinished($order);
            $mailService->sendReviewRequest($order);
        }

        $this->addFlash(
            'success',
            'Le statut de la commande a bien été modifié.'
        );

        return $this->redirectToRoute(
            'app_employee_order_show',
            ['id' => $order->getId()]
        );
    }

    #[Route(
        '/employee/commande/{id}/modifier',
        name: 'app_employee_order_update',
        methods: ['POST']
    )]
    public function updateOrder(
        Order $order,
        Request $request,
        EntityManagerInterface $entityManager,
        DeliveryFeeCalculator $deliveryFeeCalculator,
        MailService $mailService,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');


        if (!$this->isCsrfTokenValid(
            'update-order-' . $order->getId(),
            (string) $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton de sécurité invalide.'
            );
        }

        /*
     * Une commande acceptée ne peut plus être
     * modifiée ou annulée.
     */
        if ($order->getStatus() !== 'En attente') {
            $this->addFlash(
                'danger',
                'Cette commande ne peut plus être modifiée ou annulée.'
            );

            return $this->redirectToRoute(
                'app_employee_order_show',
                ['id' => $order->getId()]
            );
        }

        $action = (string) $request->request->get('action');
        $contactMode = trim(
            (string) $request->request->get('contactMode')
        );
        $contactReason = trim(
            (string) $request->request->get('contactReason')
        );

        if (!in_array($contactMode, ['Téléphone', 'E-mail'], true)) {
            $this->addFlash(
                'danger',
                'Veuillez sélectionner le mode de contact utilisé.'
            );

            return $this->redirectToRoute(
                'app_employee_order_show',
                ['id' => $order->getId()]
            );
        }

        if ($contactReason === '') {
            $this->addFlash(
                'danger',
                'Le motif de la modification ou de l’annulation est obligatoire.'
            );

            return $this->redirectToRoute(
                'app_employee_order_show',
                ['id' => $order->getId()]
            );
        }

        /*
     * ANNULATION
     */
        if ($action === 'cancel') {
            $menu = $order->getMenu();

            $minimumPerson = max(
                1,
                (int) $menu->getMinimumPerson()
            );

            $stockToRestore = (int) ceil(
                $order->getNumberOfPeople() / $minimumPerson
            );

            $menu->setStock(
                $menu->getStock() + $stockToRestore
            );

            $order->setStatus('Annulée');

            $entityManager->flush();
            $mailService->sendOrderCancelled($order);

            $this->addFlash(
                'success',
                sprintf(
                    'La commande a été annulée après contact par %s. Motif : %s',
                    $contactMode,
                    $contactReason
                )
            );

            return $this->redirectToRoute('app_employee_index');
        }

        /*
     * MODIFICATION
     */
        if ($action !== 'update') {
            $this->addFlash(
                'danger',
                'Action inconnue.'
            );

            return $this->redirectToRoute(
                'app_employee_order_show',
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
                'app_employee_order_show',
                ['id' => $order->getId()]
            );
        }

        if ($deliveryAddress === '') {
            $this->addFlash(
                'danger',
                'L’adresse de livraison est obligatoire.'
            );

            return $this->redirectToRoute(
                'app_employee_order_show',
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
                'app_employee_order_show',
                ['id' => $order->getId()]
            );
        }

        /*
     * Ajustement du stock selon l’ancien et le nouveau
     * nombre de personnes.
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
                'app_employee_order_show',
                ['id' => $order->getId()]
            );
        }

        /*
     * Nouveau calcul de livraison.
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
                'app_employee_order_show',
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
                'app_employee_order_show',
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
     * Enregistrement.
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
            sprintf(
                'La commande a été modifiée après contact par %s. Motif : %s',
                $contactMode,
                $contactReason
            )
        );

        return $this->redirectToRoute(
            'app_employee_order_show',
            ['id' => $order->getId()]
        );
    }
}
