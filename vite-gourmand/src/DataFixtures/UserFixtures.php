<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $adminExiste = $manager
    ->getRepository(User::class)
    ->findOneBy(['email' => 'admin@vite-gourmand.fr']);

if ($adminExiste) {
    return;
}

        $roleAdmin = $manager
            ->getRepository(Role::class)
            ->findOneBy(['libelle' => 'ROLE_ADMIN']);

        if ($roleAdmin === null) {
            throw new \RuntimeException(
                'Le rôle ROLE_ADMIN doit exister avant de créer l’administrateur.'
            );
        }

        $admin = new User();
        $admin->setEmail('admin@vite-gourmand.fr');
        $admin->setNom('Administrateur');
        $admin->setPrenom('Admin');
        $admin->setGsm('0600000000');
        $admin->setAdressePostale('Vite & Gourmand - Bordeaux');
        $admin->setRole($roleAdmin);

        $admin->setPassword(
            $this->passwordHasher->hashPassword(
                $admin,
                'Admin123!'
            )
        );

        $manager->persist($admin);
        $manager->flush();
    }
}