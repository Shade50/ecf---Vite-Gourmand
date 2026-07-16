<?php

namespace App\Service;

use App\Entity\Order;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class MailService
{
    public function __construct(
            private readonly MailerInterface $mailer
        ) {}

    private function sendMail(
        Order $order,
        string $subject,
        string $message
    ): void {
        $email = (new Email())
            ->from('contact@vite-gourmand.fr')
            ->to($order->getUser()->getEmail())
            ->subject($subject)
            ->text($message);

        $this->mailer->send($email);
    }

    

    public function sendOrderCreated(Order $order): void
    {
        $this->sendMail(
            $order,
            'Votre commande a bien été enregistrée',
            sprintf(
                "Bonjour %s,

                Votre commande n°%d a bien été enregistrée.

                Merci de votre confiance.

                L'équipe Vite & Gourmand",
                $order->getUser()->getPrenom(),
                $order->getId()
            )
        );
    }

    public function sendOrderUpdated(Order $order): void 
    {
        $this->sendMail(
            $order,
            'Votre commande a bien été modifiée',
            sprintf(
                "Bonjour %s,

                Votre commande n°%d a bien été modifiée.

                Merci de votre confiance.

                L'équipe Vite & Gourmand",
                $order->getUser()->getPrenom(),
                $order->getId()
            )
        );
    }

    public function sendOrderCancelled(Order $order): void 
    {
        $this->sendMail(
            $order,
            'Votre commande a bien été annulée',
            sprintf(
                "Bonjour %s,

                Votre commande n°%d a bien été annulée.

                Merci de votre confiance.

                L'équipe Vite & Gourmand",
                $order->getUser()->getPrenom(),
                $order->getId()
            )
        );
    }

    public function sendOrderAccepted(Order $order): void 
    {
        $this->sendMail(
            $order,
            'Votre commande a bien été acceptée',
            sprintf(
                "Bonjour %s,

                Votre commande n°%d a bien été acceptée.

                Merci de votre confiance.

                L'équipe Vite & Gourmand",
                $order->getUser()->getPrenom(),
                $order->getId()
            )
        );
    }

    public function sendOrderFinished(Order $order): void 
    {
        $this->sendMail(
            $order,
            'Votre commande est terminée',
            sprintf(
                "Bonjour %s,

                Votre commande n°%d est terminée.

                Merci de votre confiance.

                L'équipe Vite & Gourmand",
                $order->getUser()->getPrenom(),
                $order->getId()
            )
        );
    }

    public function sendReviewRequest(Order $order): void 
    {
        $this->sendMail(
        $order,
        'Donnez votre avis',
        sprintf(
            "Bonjour %s,

            Merci pour votre commande.

            Vous pouvez maintenant laisser un avis depuis votre espace client.

            L'équipe Vite & Gourmand",
            $order->getUser()->getPrenom()
        )
    );
    }
}
