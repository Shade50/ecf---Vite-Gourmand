<?php

namespace App\Service;

use App\Repository\OrderRepository;
use MongoDB\BSON\UTCDateTime;

class StatisticsService
{
    public function __construct(
        private readonly MongoDBService $mongoDBService,
        private readonly OrderRepository $orderRepository,
    ) {}

    public function synchronize(): int
    {
        $collection = $this->getCollection();

        // Reconstruction complète pour éviter les doublons.
        $collection->deleteMany([]);

        $orders = $this->orderRepository->findAll();
        $inserted = 0;

        foreach ($orders as $order) {
            $menu = $order->getMenu();
            $createdAt = $order->getCreateAt();

            if ($menu === null || $createdAt === null) {
                continue;
            }

            $collection->insertOne([
                'orderId' => $order->getId(),
                'menuId' => $menu->getId(),
                'menuName' => $menu->getTitle(),
                'totalPrice' => (float) $order->getTotalPrice(),
                'status' => $order->getStatus(),
                'numberOfPeople' => $order->getNumberOfPeople(),
                'createdAt' => new UTCDateTime(
                    $createdAt->getTimestamp() * 1000
                ),
            ]);

            ++$inserted;
        }

        return $inserted;
    }

    /**
     * Nombre de commandes pour chaque menu.
     */
    public function getOrdersByMenu(
        ?int $menuId = null,
        ?\DateTimeImmutable $startDate = null,
        ?\DateTimeImmutable $endDate = null,
    ): array {
        $match = [];

        if ($menuId !== null) {
            $match['menuId'] = $menuId;
        }

        if ($startDate !== null || $endDate !== null) {
            $match['createdAt'] = [];

            if ($startDate !== null) {
                $match['createdAt']['$gte'] = new UTCDateTime(
                    $startDate->getTimestamp() * 1000
                );
            }

            if ($endDate !== null) {
                $match['createdAt']['$lte'] = new UTCDateTime(
                    $endDate->getTimestamp() * 1000
                );
            }
        }

        $pipeline = [];

        if ($match !== []) {
            $pipeline[] = [
                '$match' => $match,
            ];
        }

        $pipeline[] = [
            '$group' => [
                '_id' => [
                    'menuId' => '$menuId',
                    'menuName' => '$menuName',
                ],
                'ordersCount' => [
                    '$sum' => 1,
                ],
            ],
        ];

        $pipeline[] = [
            '$sort' => [
                'ordersCount' => -1,
            ],
        ];

        $cursor = $this->getCollection()->aggregate($pipeline);

        $statistics = [];

        foreach ($cursor as $document) {
            $statistics[] = [
                'menuId' => (int) $document['_id']['menuId'],
                'menuName' => (string) $document['_id']['menuName'],
                'ordersCount' => (int) $document['ordersCount'],
            ];
        }

        return $statistics;
    }

    /**
     * Chiffre d'affaires par menu avec filtres facultatifs.
     */
    public function getRevenueByMenu(
        ?int $menuId = null,
        ?\DateTimeImmutable $startDate = null,
        ?\DateTimeImmutable $endDate = null,
    ): array {
        $match = [
            // Une commande refusée ou annulée ne génère pas de CA.
            'status' => [
                '$nin' => ['Refusée', 'Annulée'],
            ],
        ];

        if ($menuId !== null) {
            $match['menuId'] = $menuId;
        }

        if ($startDate !== null || $endDate !== null) {
            $match['createdAt'] = [];

            if ($startDate !== null) {
                $match['createdAt']['$gte'] = new UTCDateTime(
                    $startDate->getTimestamp() * 1000
                );
            }

            if ($endDate !== null) {
                $match['createdAt']['$lte'] = new UTCDateTime(
                    $endDate->getTimestamp() * 1000
                );
            }
        }

        $cursor = $this->getCollection()->aggregate([
            [
                '$match' => $match,
            ],
            [
                '$group' => [
                    '_id' => [
                        'menuId' => '$menuId',
                        'menuName' => '$menuName',
                    ],
                    'revenue' => [
                        '$sum' => '$totalPrice',
                    ],
                    'ordersCount' => [
                        '$sum' => 1,
                    ],
                ],
            ],
            [
                '$sort' => [
                    'revenue' => -1,
                ],
            ],
        ]);

        $statistics = [];

        foreach ($cursor as $document) {
            $statistics[] = [
                'menuId' => $document['_id']['menuId'],
                'menuName' => $document['_id']['menuName'],
                'revenue' => (float) $document['revenue'],
                'ordersCount' => $document['ordersCount'],
            ];
        }

        return $statistics;
    }

    private function getCollection(): \MongoDB\Collection
    {
        return $this->mongoDBService
            ->getDatabase()
            ->selectCollection('menu_statistics');
    }
}
