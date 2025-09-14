<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Shared\Clients;

use JeroenGerits\Support\Shared\Exceptions\ServiceException;

class Registry
{
    private array $services = [];

    public function register(string $name, callable $factory): void
    {
        $this->services[$name] = $factory;
    }

    public function get(string $name): mixed
    {
        if (! isset($this->services[$name])) {
            throw ServiceException::notFound($name);
        }

        return $this->services[$name]();
    }

    public function has(string $name): bool
    {
        return isset($this->services[$name]);
    }

    public function list(): array
    {
        return array_keys($this->services);
    }
}
