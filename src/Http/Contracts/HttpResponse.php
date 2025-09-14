<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Http\Contracts;

interface HttpResponse
{
    public function getStatusCode(): int;

    public function getHeaders(): array;

    public function getBody(): string;

    public function getJson(): array;

    public function isSuccessful(): bool;

    public function isClientError(): bool;

    public function isServerError(): bool;

    public function getReasonPhrase(): string;
}
