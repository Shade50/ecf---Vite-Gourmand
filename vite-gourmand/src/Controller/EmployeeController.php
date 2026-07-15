<?php

namespace App\Controller;

use App\Repository\OrderRepository;
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
}
