<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $roleExiste = $manager
            ->getRepository(Role::class)
            ->findOneBy(['libelle' => 'ROLE_USER']);

        if ($roleExiste) {
            return;
        }

        $roles = [
            'ROLE_USER',
            'ROLE_EMPLOYEE',
            'ROLE_ADMIN',
        ];

        foreach ($roles as $libelle) {
            $role = new Role();
            $role->setLibelle($libelle);

            $manager->persist($role);
        }

        $manager->flush();
    }
}
