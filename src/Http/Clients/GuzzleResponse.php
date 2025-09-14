<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Http\Clients;

use JeroenGerits\Support\Http\Contracts\HttpResponse;
use Psr\Http\Message\ResponseInterface;

class GuzzleResponse implements HttpResponse
{
    public function __construct(private ResponseInterface $response) {}

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    public function getBody(): string
    {
        return (string) $this->response->getBody();
    }

    public function getJson(): array
    {
        $body = $this->getBody();
        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Response body is not valid JSON');
        }

        return $decoded;
    }

    public function isSuccessful(): bool
    {
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
    }

    public function isClientError(): bool
    {
        return $this->getStatusCode() >= 400 && $this->getStatusCode() < 500;
    }

    public function isServerError(): bool
    {
        return $this->getStatusCode() >= 500 && $this->getStatusCode() < 600;
    }

    public function getReasonPhrase(): string
    {
        return $this->response->getReasonPhrase();
    }
}
