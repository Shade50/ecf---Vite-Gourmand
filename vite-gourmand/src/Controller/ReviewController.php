<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{
    #[Route('/review/new', name: 'app_review_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $review = new Review();

        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $review->setUser($this->getUser());
            $review->setStatut('pending');

            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre avis a bien été envoyé et sera publié après validation.'
            );

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('review/new.html.twig', [
            'reviewForm' => $form,
        ]);
    }
}