<?php

namespace App\Service;

use App\Repository\SiteSettingsRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DeliveryFeeCalculator
{
    private const BASE_FEE = 5.00;
    private const PRICE_PER_KM = 0.59;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly SiteSettingsRepository $siteSettingsRepository,
    ) {
    }

    public function geocodeAddress(string $address): ?array
    {
        $address = trim($address);

        if ($address === '') {
            return null;
        }

        try {
            $response = $this->httpClient->request(
                'GET',
                'https://data.geopf.fr/geocodage/search',
                [
                    'query' => [
                        'q' => $address,
                        'limit' => 1,
                    ],
                ]
            );

            $data = $response->toArray();

            if (
                empty($data['features']) ||
                empty($data['features'][0]['geometry']['coordinates'])
            ) {
                return null;
            }

            $coordinates =
                $data['features'][0]['geometry']['coordinates'];

            return [
                'longitude' => (float) $coordinates[0],
                'latitude' => (float) $coordinates[1],
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    public function calculateDistance(array $destination): ?float
    {
        /*
         * Récupération des informations du restaurant
         * depuis la configuration enregistrée en base.
         */
        $settings = $this->siteSettingsRepository->findOneBy([]);

        if ($settings === null) {
            return null;
        }

        $restaurantAddress = sprintf(
            '%s %s %s',
            $settings->getAddress(),
            $settings->getPostalCode(),
            $settings->getCity()
        );

        /*
         * Transformation de l’adresse du restaurant
         * en coordonnées GPS.
         */
        $restaurantCoordinates = $this->geocodeAddress(
            $restaurantAddress
        );

        if ($restaurantCoordinates === null) {
            return null;
        }

        /*
         * OSRM attend les coordonnées dans l’ordre :
         * longitude, latitude.
         */
        $url = sprintf(
            'https://router.project-osrm.org/route/v1/driving/%s,%s;%s,%s',
            $restaurantCoordinates['longitude'],
            $restaurantCoordinates['latitude'],
            $destination['longitude'],
            $destination['latitude']
        );

        try {
            $response = $this->httpClient->request(
                'GET',
                $url,
                [
                    'query' => [
                        'overview' => 'false',
                    ],
                ]
            );

            $data = $response->toArray();

            if (empty($data['routes'][0]['distance'])) {
                return null;
            }

            /*
             * OSRM renvoie la distance en mètres.
             * Conversion en kilomètres.
             */
            return (float) $data['routes'][0]['distance'] / 1000;
        } catch (\Throwable) {
            return null;
        }
    }

    public function calculateDeliveryFee(float $distance): float
    {
        return self::BASE_FEE
            + ($distance * self::PRICE_PER_KM);
    }
}