<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user=$this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
            {
                $entityManager->flush();
                $this->addFlash(
                    'success',
                    'Vos informations ont était modifiées.'
                );

                return $this->redirectToRoute('app_profile');
            }
        return $this->render('profile/index.html.twig',[
            'form'=> $form,
        ]);
    }
}
