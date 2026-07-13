<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;




final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPassword
    ): Response {

        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('plainPassword')->getData();

            if ($plainPassword){
                $user->setPassword(
                    $userPassword->hashPassword($user, $plainPassword)
                );
            }

            $entityManager->flush();
            $this->addFlash(
                'success',
                'Vos informations ont était modifiées.'
            );

            return $this->redirectToRoute('app_profile');
        }
        return $this->render('profile/index.html.twig', [
            'form' => $form,

        ]);
    }
    #[route('/profil/commande/{id}',
        name: 'app_profile_order_detail',
        requirements:['id' => '\d+'],
        methods: ['get'])]
        public function orderDetail(order $order): Response
        {
            $this->denyAccessUnlessGranted('ROLE_USER');

            /** @var User $user */
            $user = $this->getUser();

            if ($order->getUser() != $user){
                throw $this->createAccessDeniedException(
                    'Vous ne pouvez pas consulter cette commande.');
            }
        
            return $this->render('profile/order_detail.html.twig',[
                'order' => $order,
                ]);
        }
}
