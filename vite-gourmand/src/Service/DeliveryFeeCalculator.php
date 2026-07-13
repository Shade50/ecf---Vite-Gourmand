<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class DeliveryFeeCalculator
{
    private const BASE_FEE = 5.00;
    private const PRICE_PER_KM = 0.59;

    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {}
    public function geocodeAddress(string $address): ?array
    {
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
            empty($data['features'])
            || empty($data['features'][0]['geometry']['coordinates'])
        ) {
            return null;
        }

        $coordinates = $data['features'][0]['geometry']['coordinates'];

        return [
            'longitude' => $coordinates[0],
            'latitude' => $coordinates[1],
        ];
    }

    public function calculateDistance(array $destination): ?float
    {
        // coordonnées de bordeaux
        $bordeauxLongitude = -0.57918;
        $bordeauxLatitude = 44.837789;

        $url= sprintf(
            'http://router.project-osrm.org/route/v1/driving/%s,%s;%s,%s',
            $bordeauxLongitude,
            $bordeauxLatitude,
            $destination['longitude'],
            $destination['latitude']
        );
        $response = $this->httpClient->request('GET', $url,[
            'query' => [
                'overview' => 'false',

            ],
        ]);

        $data = $response -> toArray();

        if (empty($data['routes'][0]['distance'])){
            return null;
        }
        // ORSM retourne la distance en mètres -> conversation en kilometres
        return $data['routes'][0]['distance'] / 1000;
    }

    public function calculateDeliveryFee(float $distance): float
    {
        return self::BASE_FEE + ($distance * self::PRICE_PER_KM);
    }
}
