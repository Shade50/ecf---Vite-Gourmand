<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Order;
use App\Entity\User;
use App\Form\OrderType;
use App\Service\DeliveryFeeCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order/menu/{id}', name: 'app_order_create', methods: ['GET', 'POST'])]
    public function create(
        Menu $menu,
        Request $request,
        EntityManagerInterface $entityManager,
        DeliveryFeeCalculator $deliveryFeeCalculator,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /*
         * Vérification du stock
         */
        if ($menu->getStock() <= 0) {
            $this->addFlash(
                'danger',
                'Ce menu est actuellement en rupture de stock.'
            );

            return $this->redirectToRoute('app_menu_public');
        }

        /** @var User $user */
        $user = $this->getUser();

        $order = new Order();
        $order->setUser($user);
        $order->setMenu($menu);
        $order->setStatus('En attente');
        $order->setCreateAt(new \DateTimeImmutable());

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $numberOfPeople = $order->getNumberOfPeople();
            $minimumPerson = $menu->getMinimumPerson();
            $menuPrice = (float) $menu->getPrice();

            /*
             * Vérification du nombre minimum de personnes
             */
            if ($numberOfPeople < $minimumPerson) {
                $this->addFlash(
                    'danger',
                    sprintf(
                        'Ce menu nécessite un minimum de %d personnes.',
                        $minimumPerson
                    )
                );

                return $this->redirectToRoute('app_order_create', [
                    'id' => $menu->getId(),
                ]);
            }

            /*
             * Transformation de l’adresse de livraison
             * en coordonnées GPS avec GeoPF
             */
            $coordinates = $deliveryFeeCalculator->geocodeAddress(
                $order->getDeliveryAdresse()
            );

            if ($coordinates === null) {
                $this->addFlash(
                    'danger',
                    'L’adresse de livraison est introuvable. Vérifiez-la puis réessayez.'
                );

                return $this->redirectToRoute('app_order_create', [
                    'id' => $menu->getId(),
                ]);
            }

            /*
             * Calcul de la distance routière avec OSRM
             */
            $distance = $deliveryFeeCalculator->calculateDistance(
                $coordinates
            );

            if ($distance === null) {
                $this->addFlash(
                    'danger',
                    'Impossible de calculer l’itinéraire de livraison.'
                );

                return $this->redirectToRoute('app_order_create', [
                    'id' => $menu->getId(),
                ]);
            }

            /*
             * Calcul des frais de livraison
             */
            $deliveryFee = $deliveryFeeCalculator->calculateDeliveryFee(
                $distance
            );

            /*
             * Calcul du prix du menu
             */
            $pricePerPerson = $menuPrice / $minimumPerson;
            $totalPrice = $pricePerPerson * $numberOfPeople;

            /*
             * Réduction de 10 % à partir de
             * 5 personnes supplémentaires
             */
            if ($numberOfPeople >= $minimumPerson + 5) {
                $totalPrice *= 0.90;
            }

            /*
             * Ajout systématique des frais de livraison
             */
            $totalPrice += $deliveryFee;

            $order->setTotalPrice(
                number_format($totalPrice, 2, '.', '')
            );

            /*
             * Une commande validée consomme une unité de stock
             */
            $menu->setStock($menu->getStock() - 1);

            $entityManager->persist($order);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre commande a bien été enregistrée.'
            );

            return $this->redirectToRoute('app_order_history');
        }

        return $this->render('order/create.html.twig', [
            'form' => $form,
            'menu' => $menu,
        ]);
    }

    #[Route(
        '/order/menu/{id}/delivery-preview',
        name: 'app_order_delivery_preview',
        methods: ['POST']
    )]
    public function deliveryPreview(
        Menu $menu,
        Request $request,
        DeliveryFeeCalculator $deliveryFeeCalculator,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $data = json_decode($request->getContent(), true);

        $address = trim($data['address'] ?? '');
        $numberOfPeople = (int) ($data['numberOfPeople'] ?? 0);

        if ($address === '') {
            return $this->json([
                'success' => false,
                'message' => 'Veuillez saisir une adresse de livraison.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $minimumPerson = $menu->getMinimumPerson();

        if ($numberOfPeople < $minimumPerson) {
            return $this->json([
                'success' => false,
                'message' => sprintf(
                    'Ce menu nécessite un minimum de %d personnes.',
                    $minimumPerson
                ),
            ], Response::HTTP_BAD_REQUEST);
        }

        $coordinates = $deliveryFeeCalculator->geocodeAddress($address);

        if ($coordinates === null) {
            return $this->json([
                'success' => false,
                'message' => 'Adresse de livraison introuvable.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $distance = $deliveryFeeCalculator->calculateDistance($coordinates);

        if ($distance === null) {
            return $this->json([
                'success' => false,
                'message' => 'Impossible de calculer l’itinéraire.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $deliveryFee = $deliveryFeeCalculator->calculateDeliveryFee($distance);

        $menuPrice = (float) $menu->getPrice();
        $pricePerPerson = $menuPrice / $minimumPerson;
        $mealPrice = $pricePerPerson * $numberOfPeople;

        $discountAmount = 0.0;

        if ($numberOfPeople >= $minimumPerson + 5) {
            $discountAmount = $mealPrice * 0.10;
            $mealPrice -= $discountAmount;
        }

        $totalPrice = $mealPrice + $deliveryFee;

        return $this->json([
            'success' => true,
            'distance' => round($distance, 2),
            'fixedFee' => 5.00,
            'kilometerRate' => 0.59,
            'deliveryFee' => round($deliveryFee, 2),
            'mealPrice' => round($mealPrice, 2),
            'discountAmount' => round($discountAmount, 2),
            'totalPrice' => round($totalPrice, 2),
        ]);
    }

    #[Route('/mes-commandes', name: 'app_order_history', methods: ['GET'])]
    public function history(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        return $this->render('order/history.html.twig', [
            'orders' => $user->getOrders(),
        ]);
    }
}
