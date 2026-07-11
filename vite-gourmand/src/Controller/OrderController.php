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

    if ($numberOfPeople < $minimumPerson) {
        $this->addFlash(
            'danger',
            sprintf(
                'Ce menu nécessite un minimum de %d personnes.',
                $minimumPerson
            )
        );

        return $this->render('order/create.html.twig', [
            'form' => $form,
            'menu' => $menu,
        ]);
    }

    $totalPrice = (float) $menu->getPrice() * $numberOfPeople;

    $order->setTotalPrice(
        number_format($totalPrice, 2, '.', '')
    );

    $entityManager->persist($order);
    $entityManager->flush();

    $this->addFlash(
        'success',
        'Votre commande a bien été enregistrée.'
    );

    return $this->redirectToRoute('app_home');
}

        return $this->render('order/create.html.twig', [
            'form' => $form,
            'menu' => $menu,
        ]);
    }
}