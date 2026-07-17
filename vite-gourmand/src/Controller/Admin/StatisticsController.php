<?php

namespace App\Controller\Admin;

use App\Repository\MenuRepository;
use App\Service\StatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/statistics')]
class StatisticsController extends AbstractController
{
    #[Route('', name: 'app_admin_statistics')]
    public function index(
        Request $request,
        StatisticsService $statisticsService,
        MenuRepository $menuRepository,
    ): Response {
        $statisticsService->synchronize();

        $menuValue = $request->query->get('menu');

        $menuId = is_string($menuValue) && ctype_digit($menuValue)
            ? (int) $menuValue
            : null;

        $startDate = $this->createDate(
            $request->query->get('startDate')
        );

        $endDate = $this->createDate(
            $request->query->get('endDate'),
            true
        );

        $ordersByMenu = $statisticsService->getOrdersByMenu(
            $menuId,
            $startDate,
            $endDate
        );

        $revenueByMenu = $statisticsService->getRevenueByMenu(
            $menuId,
            $startDate,
            $endDate
        );

        $totalRevenue = array_sum(
            array_column($revenueByMenu, 'revenue')
        );

        return $this->render('admin/statistics/index.html.twig', [
            'menus' => $menuRepository->findBy([], ['title' => 'ASC']),
            'ordersByMenu' => $ordersByMenu,
            'revenueByMenu' => $revenueByMenu,
            'totalRevenue' => $totalRevenue,
            'selectedMenuId' => $menuId,
            'startDate' => $request->query->get('startDate'),
            'endDate' => $request->query->get('endDate'),
        ]);
    }

    private function createDate(
        ?string $value,
        bool $endOfDay = false
    ): ?\DateTimeImmutable {
        if (!$value) {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $value);

        if (!$date) {
            return null;
        }

        return $endOfDay
            ? $date->setTime(23, 59, 59)
            : $date->setTime(0, 0);
    }
}
