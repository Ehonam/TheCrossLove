<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeocodingService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Géocode une adresse en utilisant l'API Nominatim (OpenStreetMap)
     *
     * @param string $address L'adresse complète à géocoder
     * @return array|null [latitude, longitude] ou null si non trouvé
     */
    public function geocodeAddress(string $address): ?array
    {
        try {
            $response = $this->httpClient->request('GET', 'https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                ],
                'headers' => [
                    'User-Agent' => 'TheCrossLove/1.0 (contact@thecrosslove.com)',
                ],
            ]);

            $data = $response->toArray();

            if (!empty($data)) {
                return [
                    'latitude' => (float) $data[0]['lat'],
                    'longitude' => (float) $data[0]['lon'],
                ];
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Géocode une ville et un pays
     *
     * @param string $city La ville
     * @param string $country Le pays
     * @return array|null [latitude, longitude] ou null si non trouvé
     */
    public function geocodeCity(string $city, string $country): ?array
    {
        return $this->geocodeAddress($city . ', ' . $country);
    }
}
