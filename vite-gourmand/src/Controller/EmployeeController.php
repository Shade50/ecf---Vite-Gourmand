<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;







final class EmployeeController extends AbstractController
{
    #[Route('/employee', name: 'app_employee_index', methods: ['GET'])]
    public function index(
        Request $request,
        OrderRepository $orderRepository,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYER');

        $search = trim((string) $request->query->get('search', ''));
        $status = trim((string) $request->query->get('status', ''));

        $orders = $orderRepository->findForEmployee(
            $search,
            $status
        );


        return $this->render('employee/index.html.twig', [
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

    #[Route(
        '/employee/commande/{id}',
        name: 'app_employee_order_show',
        methods: ['GET']
    )]
    public function show(Order $order): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYER');

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
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYER');

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

        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le statut de la commande a bien été modifié.'
        );

        return $this->redirectToRoute(
            'app_employee_order_show',
            ['id' => $order->getId()]
        );
    }
}
