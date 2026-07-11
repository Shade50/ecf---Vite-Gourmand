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

            return $this->redirectToRoute('app_home');
        }

        return $this->render('order/create.html.twig', [
            'form' => $form,
            'menu' => $menu,
        ]);
    }
}
