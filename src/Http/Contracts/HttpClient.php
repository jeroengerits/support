<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Http\Contracts;

interface HttpClient
{
    public function get(string $url, array $options = []): HttpResponse;

    public function post(string $url, array $data = [], array $options = []): HttpResponse;

    public function put(string $url, array $data = [], array $options = []): HttpResponse;

    public function delete(string $url, array $options = []): HttpResponse;

    public function request(string $method, string $url, array $options = []): HttpResponse;
}
