<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MenuPublicController extends AbstractController
{
    #[Route('/menu/public', name: 'app_menu_public')]
    public function index(): Response
    {
        return $this->render('menu_public/index.html.twig', [
            'controller_name' => 'MenuPublicController',
        ]);
    }
}
