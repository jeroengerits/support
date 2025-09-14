<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Geo\Clients;

use JeroenGerits\Support\Coordinates\ValueObjects\Coordinates;
use JeroenGerits\Support\Geo\Contracts\Geocoding;
use JeroenGerits\Support\Geo\Exceptions\GeocodingException;
use JeroenGerits\Support\Geo\ValueObjects\LocationInformation;
use JeroenGerits\Support\Http\Contracts\HttpClient;

class Nominatim implements Geocoding
{
    private const string PROVIDER_NAME = 'Nominatim';

    private const string BASE_URL = 'https://nominatim.openstreetmap.org';

    public function __construct(
        private HttpClient $httpClient,
        private array $config = []
    ) {}

    public function reverseGeocode(Coordinates $coordinates): LocationInformation
    {
        $url = $this->buildReverseGeocodeUrl($coordinates);
        $response = $this->httpClient->get($url, $this->getRequestOptions());

        if (! $response->isSuccessful()) {
            throw GeocodingException::serviceUnavailable(self::PROVIDER_NAME);
        }

        return $this->parseLocationInformation($response->getJson());
    }

    public function geocode(string $address): ?Coordinates
    {
        $url = $this->buildGeocodeUrl($address);
        $response = $this->httpClient->get($url, $this->getRequestOptions());

        if (! $response->isSuccessful()) {
            return null;
        }

        $data = $response->getJson();
        if ($data === []) {
            return null;
        }

        return $this->parseCoordinates($data[0]);
    }

    public function isAvailable(): bool
    {
        try {
            $response = $this->httpClient->get(self::BASE_URL.'/status');

            return $response->isSuccessful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getProviderName(): string
    {
        return self::PROVIDER_NAME;
    }

    private function buildReverseGeocodeUrl(Coordinates $coordinates): string
    {
        $params = [
            'lat' => $coordinates->latitude->value,
            'lon' => $coordinates->longitude->value,
            'format' => 'json',
            'addressdetails' => '1',
            'extratags' => '1',
        ];

        return self::BASE_URL.'/reverse?'.http_build_query($params);
    }

    private function buildGeocodeUrl(string $address): string
    {
        $params = [
            'q' => $address,
            'format' => 'json',
            'addressdetails' => '1',
            'limit' => '1',
        ];

        return self::BASE_URL.'/search?'.http_build_query($params);
    }

    private function getRequestOptions(): array
    {
        return [
            'headers' => [
                'User-Agent' => $this->config['user_agent'] ?? 'Support Package/1.0',
            ],
            'timeout' => $this->config['timeout'] ?? 30,
        ];
    }

    private function parseLocationInformation(array $data): LocationInformation
    {
        $address = $data['address'] ?? [];

        return new LocationInformation(
            city: $address['city'] ?? $address['town'] ?? $address['village'] ?? null,
            state: $address['state'] ?? $address['province'] ?? null,
            country: $address['country'] ?? null,
            postalCode: $address['postcode'] ?? null,
            formattedAddress: $data['display_name'] ?? null,
            timezone: $data['extratags']['timezone'] ?? null,
            confidence: $data['importance'] ?? null,
            countryCode: $address['country_code'] ?? null
        );
    }

    private function parseCoordinates(array $data): Coordinates
    {
        $lat = (float) $data['lat'];
        $lon = (float) $data['lon'];

        return Coordinates::create($lat, $lon);
    }
}
