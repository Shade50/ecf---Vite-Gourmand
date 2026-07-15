<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'is_admin' => $this->isGranted('ROLE_ADMIN'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $plainPassword)
            );

            $roleLibelle = 'ROLE_USER';

            if (
                $this->isGranted('ROLE_ADMIN')
                && $form->has('createAsEmployee')
                && $form->get('createAsEmployee')->getData()
            ) {
                $roleLibelle = 'ROLE_EMPLOYEE';
            }

            $role = $entityManager
                ->getRepository(Role::class)
                ->findOneBy(['libelle' => $roleLibelle]);

            if ($role === null) {
                throw new \RuntimeException(
                    sprintf('Le rôle %s est introuvable.', $roleLibelle)
                );
            }

            $user->setRole($role);

            $entityManager->persist($user);
            $entityManager->flush();

            if ($this->isGranted('ROLE_ADMIN')) {
                $this->addFlash(
                    'success',
                    $roleLibelle === 'ROLE_EMPLOYEE'
                        ? 'Le compte employé a bien été créé.'
                        : 'Le compte utilisateur a bien été créé.'
                );

                return $this->redirectToRoute('app_admin');
            }

            return $security->login($user, 'form_login', 'main');
        }

        $roleEmployee = $entityManager
            ->getRepository(Role::class)
            ->findOneBy(['libelle' => 'ROLE_EMPLOYEE']);

        $employees = [];

        if ($this->isGranted('ROLE_ADMIN') && $roleEmployee) {
            $employees = $entityManager
                ->getRepository(User::class)
                ->findBy(
                    ['role' => $roleEmployee],
                    ['nom' => 'ASC']
                );
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'employees' => $employees,
        ]);
    }

    #[Route(
        '/admin/employees/{id}/toggle',
        name: 'app_admin_employee_toggle',
        methods: ['POST']
    )]
    public function toggleEmployee(
        User $employee,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid(
            'toggle_employee_' . $employee->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        if (!in_array('ROLE_EMPLOYEE', $employee->getRoles(), true)) {
            throw $this->createNotFoundException('Cet utilisateur n’est pas un employé.');
        }

        $employee->setIsActive(!$employee->isActive());
        $entityManager->flush();

        $this->addFlash(
            'success',
            $employee->isActive()
                ? 'Le compte employé a été réactivé.'
                : 'Le compte employé a été désactivé.'
        );

        return $this->redirectToRoute('app_register');
    }
}
