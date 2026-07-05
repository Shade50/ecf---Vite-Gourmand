<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [

    'menus' => [

        [
            'titre' => 'Menu Mariage',
            'description' => 'Cocktail + repas complet',
            'prix' => 45
        ],

        [
            'titre' => 'Menu Entreprise',
            'description' => 'Buffet froid',
            'prix' => 25
        ],

        [
            'titre' => 'Menu Anniversaire',
            'description' => 'Repas convivial',
            'prix' => 30
        ],

        [
            'titre' => 'Menu pâque',
            'description' => 'Repas convivial très complet',
            'prix' => 45
        ],

    ]

]);
    }

    #[Route('/test2', name: 'app_test2')]
    public function test2(): Response
    {
        return new Response('<h1>Bienvenue sur la page de test numero2 !</h1>');
    }
}

