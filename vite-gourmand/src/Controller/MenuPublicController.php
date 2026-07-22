<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use App\Repository\PlatRepository;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class MenuPublicController extends AbstractController
{
    #[Route('/menus', name: 'app_menu_public', methods: ['GET'])]
    public function index(
        MenuRepository $menuRepository,
        ThemeRepository $themeRepository,
        PlatRepository $platRepository
    ): Response {
        $regimes = $platRepository
            ->createQueryBuilder('plat')
            ->select('DISTINCT plat.regime')
            ->where('plat.regime IS NOT NULL')
            ->andWhere('plat.regime != :empty')
            ->setParameter('empty', '')
            ->orderBy('plat.regime', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();

        return $this->render('menu_public/index.html.twig', [
            'menus' => $menuRepository->findAll(),
            'themes' => $themeRepository->findBy([], ['label' => 'ASC']),
            'regimes' => $regimes,
        ]);
    }

    #[Route('/menus/filter', name: 'app_menu_public_filter', methods: ['GET'])]
    public function filter(
        Request $request,
        MenuRepository $menuRepository
    ): Response {
        $priceMin = $request->query->get('priceMin');
        $priceMax = $request->query->get('priceMax');
        $themeId = $request->query->get('theme');
        $regime = $request->query->get('regime');
        $numberOfPeople = $request->query->get('numberOfPeople');

        $menus = $menuRepository->findByFilters(
            $priceMin !== null && $priceMin !== ''
                ? (float) $priceMin
                : null,

            $priceMax !== null && $priceMax !== ''
                ? (float) $priceMax
                : null,

            $themeId !== null && $themeId !== ''
                ? (int) $themeId
                : null,

            $regime !== null && $regime !== ''
                ? $regime
                : null,

            $numberOfPeople !== null && $numberOfPeople !== ''
                ? (int) $numberOfPeople
                : null,
        );

        return $this->render('menu_public/_menu_cards.html.twig', [
            'menus' => $menus,
        ]);
    }

    #[Route('/menus/{id}', name: 'app_menu_public_show', methods: ['GET'])]
    public function show(Menu $menu): Response
    {
        return $this->render('menu_public/show.html.twig', [
            'menu' => $menu,
        ]);
    }
}
