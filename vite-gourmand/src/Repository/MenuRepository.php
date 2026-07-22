<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Menu>
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    /**
     * Recherche les menus selon les filtres sélectionnés.
     *
     * @return Menu[]
     */
    public function findByFilters(
        ?float $priceMin,
        ?float $priceMax,
        ?int $themeId,
        ?string $regime,
        ?int $numberOfPeople
    ): array {
        $queryBuilder = $this->createQueryBuilder('menu')
            ->select('DISTINCT menu')
            ->leftJoin('menu.theme', 'theme')
            ->leftJoin('menu.plats', 'plat')
            ->addSelect('theme')
            ->orderBy('menu.price', 'ASC');

        if ($priceMin !== null) {
            $queryBuilder
                ->andWhere('menu.price >= :priceMin')
                ->setParameter('priceMin', $priceMin);
        }

        if ($priceMax !== null) {
            $queryBuilder
                ->andWhere('menu.price <= :priceMax')
                ->setParameter('priceMax', $priceMax);
        }

        if ($themeId !== null) {
            $queryBuilder
                ->andWhere('theme.id = :themeId')
                ->setParameter('themeId', $themeId);
        }

        if ($regime !== null && $regime !== '') {
            $queryBuilder
                ->andWhere('plat.regime = :regime')
                ->setParameter('regime', $regime);
        }

        if ($numberOfPeople !== null) {
            $queryBuilder
                ->andWhere('menu.minimumPerson <= :numberOfPeople')
                ->setParameter('numberOfPeople', $numberOfPeople);
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }
}
