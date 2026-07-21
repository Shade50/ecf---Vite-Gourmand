<?php

namespace App\Controller;

use App\Service\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function contact(
        Request $request,
        MailService $mailService
    ): Response {

        if ($request->request->get('website')) {
            throw $this->createNotFoundException();
        }
        if ($request->isMethod('POST')) {
            $name = trim($request->request->getString('name'));
            $email = trim($request->request->getString('email'));
            $subject = trim($request->request->getString('subject'));
            $message = trim($request->request->getString('message'));

            if (
                $name === ''
                || $email === ''
                || $subject === ''
                || $message === ''
                || !filter_var($email, FILTER_VALIDATE_EMAIL)
            ) {
                $this->addFlash(
                    'danger',
                    'Veuillez remplir correctement tous les champs.'
                );

                return $this->redirectToRoute('app_contact');
            }

            try {
                $mailService->sendContactMessage(
                    $name,
                    $email,
                    $subject,
                    $message
                );

                $this->addFlash(
                    'success',
                    'Votre message a bien été envoyé.'
                );
            } catch (\Throwable $exception) {
                $this->addFlash(
                    'danger',
                    'Une erreur est survenue pendant l’envoi du message.'
                );
            }

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig');
    }
}
