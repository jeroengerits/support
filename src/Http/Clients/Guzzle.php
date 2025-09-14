<?php

declare(strict_types=1);

namespace JeroenGerits\Support\Http\Clients;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TimeoutException;
use GuzzleHttp\Exception\TransferException;
use JeroenGerits\Support\Http\Contracts\HttpClient;
use JeroenGerits\Support\Http\Contracts\HttpResponse;
use JeroenGerits\Support\Http\Exceptions\HttpConnectionException;
use JeroenGerits\Support\Http\Exceptions\HttpException;
use JeroenGerits\Support\Http\Exceptions\HttpTimeoutException;

class Guzzle implements HttpClient
{
    public function __construct(
        private GuzzleClient $client = new GuzzleClient,
        private array $defaultOptions = []
    ) {}

    public function get(string $url, array $options = []): HttpResponse
    {
        return $this->request('GET', $url, $options);
    }

    public function post(string $url, array $data = [], array $options = []): HttpResponse
    {
        $options['json'] = $data;

        return $this->request('POST', $url, $options);
    }

    public function put(string $url, array $data = [], array $options = []): HttpResponse
    {
        $options['json'] = $data;

        return $this->request('PUT', $url, $options);
    }

    public function delete(string $url, array $options = []): HttpResponse
    {
        return $this->request('DELETE', $url, $options);
    }

    public function request(string $method, string $url, array $options = []): HttpResponse
    {
        try {
            $options = array_merge($this->defaultOptions, $options);
            $response = $this->client->request($method, $url, $options);

            return new GuzzleResponse($response);
        } catch (TimeoutException $e) {
            throw HttpTimeoutException::timeout($url, $options['timeout'] ?? 30);
        } catch (ConnectException $e) {
            throw HttpConnectionException::connectionError($url, $e->getMessage());
        } catch (RequestException $e) {
            throw new HttpException(
                message: "HTTP request failed: {$e->getMessage()}",
                code: HttpException::CODE_REQUEST_FAILED,
                previous: $e
            );
        } catch (TransferException $e) {
            throw new HttpException(
                message: "HTTP request failed: {$e->getMessage()}",
                code: HttpException::CODE_REQUEST_FAILED,
                previous: $e
            );
        }
    }
}
