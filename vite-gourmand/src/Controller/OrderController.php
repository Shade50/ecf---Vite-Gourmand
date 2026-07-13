<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Order;
use App\Entity\User;
use App\Form\OrderType;
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
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** verification du stock */
        if ($menu->getStock() <=0){
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

            $pricePerPerson = $menuPrice / $minimumPerson;
            $totalPrice = $pricePerPerson * $numberOfPeople;

            // Réduction de 10 % à partir de 5 personnes supplémentaires
            if ($numberOfPeople >= $minimumPerson + 5) {
                $totalPrice *= 0.90;
            }

            $order->setTotalPrice(
                number_format($totalPrice, 2, '.', '')
            );

            // Une commande validé consomme une unité de stock
            $menu->setStock($menu->getStock()-1);

            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('app_order_history');
        }

        return $this->render('order/create.html.twig', [
            'form' => $form,
            'menu' => $menu,
        ]);
    }

    #[Route('/mes-commandes', name: 'app_order_history',methods:['get'])]
    public function history(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        return $this->render('order/history.html.twig',[
            'orders' => $user->getOrders(),
        ]);
    }

}
