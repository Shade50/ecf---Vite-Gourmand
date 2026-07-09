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

    ],

    'avis' => [

        [
            'nom' => 'Jean Dupont',
            'note' => 5,
            'commentaire' => 'Excellent service et plats délicieux !'
        ],

        [
            'nom' => 'Marie Martin',
            'note' => 4,
            'commentaire' => 'Très bon rapport qualité/prix.'
        ],

        [
            'nom' => 'Pierre Durand',
            'note' => 3,
            'commentaire' => 'Plats corrects mais service un peu lent.'
        ],

    ]

        ]);

    }
}