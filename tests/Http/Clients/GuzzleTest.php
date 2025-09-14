<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;
use JeroenGerits\Support\Http\Clients\Guzzle;
use JeroenGerits\Support\Http\Clients\GuzzleResponse;
use JeroenGerits\Support\Http\Exceptions\HttpException;

describe('HTTP Domain', function (): void {
    describe('Guzzle Client', function (): void {
        it('performs GET requests successfully', function (): void {
            $mockClient = Mockery::mock(GuzzleClient::class);
            $mockResponse = new Response(200, [], '{"success": true}');

            $mockClient->shouldReceive('request')
                ->with('GET', 'https://api.example.com/test', [])
                ->andReturn($mockResponse);

            $guzzle = new Guzzle($mockClient);
            $response = $guzzle->get('https://api.example.com/test');

            expect($response)->toBeInstanceOf(GuzzleResponse::class)
                ->and($response->getStatusCode())->toBe(200)
                ->and($response->isSuccessful())->toBeTrue();
        });

        it('performs POST requests with JSON data', function (): void {
            $mockClient = Mockery::mock(GuzzleClient::class);
            $mockResponse = new Response(201, [], '{"id": 123}');

            $mockClient->shouldReceive('request')
                ->with('POST', 'https://api.example.com/create', ['json' => ['name' => 'Test']])
                ->andReturn($mockResponse);

            $guzzle = new Guzzle($mockClient);
            $response = $guzzle->post('https://api.example.com/create', ['name' => 'Test']);

            expect($response)->toBeInstanceOf(GuzzleResponse::class)
                ->and($response->getStatusCode())->toBe(201);
        });

        it('performs PUT requests with JSON data', function (): void {
            $mockClient = Mockery::mock(GuzzleClient::class);
            $mockResponse = new Response(200, [], '{"updated": true}');

            $mockClient->shouldReceive('request')
                ->with('PUT', 'https://api.example.com/update', ['json' => ['id' => 123, 'name' => 'Updated']])
                ->andReturn($mockResponse);

            $guzzle = new Guzzle($mockClient);
            $response = $guzzle->put('https://api.example.com/update', ['id' => 123, 'name' => 'Updated']);

            expect($response)->toBeInstanceOf(GuzzleResponse::class)
                ->and($response->getStatusCode())->toBe(200);
        });

        it('performs DELETE requests', function (): void {
            $mockClient = Mockery::mock(GuzzleClient::class);
            $mockResponse = new Response(204, [], '');

            $mockClient->shouldReceive('request')
                ->with('DELETE', 'https://api.example.com/delete', [])
                ->andReturn($mockResponse);

            $guzzle = new Guzzle($mockClient);
            $response = $guzzle->delete('https://api.example.com/delete');

            expect($response)->toBeInstanceOf(GuzzleResponse::class)
                ->and($response->getStatusCode())->toBe(204);
        });

        it('handles timeout exceptions', function (): void {
            $mockClient = Mockery::mock(GuzzleClient::class);
            $timeoutException = new TransferException('Request timed out');

            $mockClient->shouldReceive('request')
                ->andThrow($timeoutException);

            $guzzle = new Guzzle($mockClient);

            expect(fn (): \JeroenGerits\Support\Http\Contracts\HttpResponse => $guzzle->get('https://api.example.com/slow'))
                ->toThrow(HttpException::class);
        });

        it('handles connection exceptions', function (): void {
            $mockClient = Mockery::mock(GuzzleClient::class);
            $connectException = new ConnectException('Connection failed', Mockery::mock(\Psr\Http\Message\RequestInterface::class));

            $mockClient->shouldReceive('request')
                ->andThrow($connectException);

            $guzzle = new Guzzle($mockClient);

            expect(fn (): \JeroenGerits\Support\Http\Contracts\HttpResponse => $guzzle->get('https://api.example.com/unreachable'))
                ->toThrow(HttpException::class);
        });

        it('handles request exceptions', function (): void {
            $mockClient = Mockery::mock(GuzzleClient::class);
            $requestException = new RequestException('Request failed', Mockery::mock(\Psr\Http\Message\RequestInterface::class));

            $mockClient->shouldReceive('request')
                ->andThrow($requestException);

            $guzzle = new Guzzle($mockClient);

            expect(fn (): \JeroenGerits\Support\Http\Contracts\HttpResponse => $guzzle->get('https://api.example.com/error'))
                ->toThrow(HttpException::class, 'HTTP request failed: Request failed');
        });

        it('merges default options with request options', function (): void {
            $mockClient = Mockery::mock(GuzzleClient::class);
            $mockResponse = new Response(200, [], '{"success": true}');

            $mockClient->shouldReceive('request')
                ->with('GET', 'https://api.example.com/test', ['timeout' => 60, 'headers' => ['User-Agent' => 'Test']])
                ->andReturn($mockResponse);

            $guzzle = new Guzzle($mockClient, ['timeout' => 60]);
            $response = $guzzle->get('https://api.example.com/test', ['headers' => ['User-Agent' => 'Test']]);

            expect($response)->toBeInstanceOf(GuzzleResponse::class);
        });
    });

    describe('GuzzleResponse', function (): void {
        it('provides response data correctly', function (): void {
            $response = new Response(200, ['Content-Type' => 'application/json'], '{"message": "success"}');
            $guzzleResponse = new GuzzleResponse($response);

            expect($guzzleResponse->getStatusCode())->toBe(200)
                ->and($guzzleResponse->getHeaders())->toBe(['Content-Type' => ['application/json']])
                ->and($guzzleResponse->getBody())->toBe('{"message": "success"}')
                ->and($guzzleResponse->getJson())->toBe(['message' => 'success'])
                ->and($guzzleResponse->isSuccessful())->toBeTrue()
                ->and($guzzleResponse->isClientError())->toBeFalse()
                ->and($guzzleResponse->isServerError())->toBeFalse()
                ->and($guzzleResponse->getReasonPhrase())->toBe('OK');
        });

        it('identifies client errors correctly', function (): void {
            $response = new Response(404, [], 'Not Found');
            $guzzleResponse = new GuzzleResponse($response);

            expect($guzzleResponse->isSuccessful())->toBeFalse()
                ->and($guzzleResponse->isClientError())->toBeTrue()
                ->and($guzzleResponse->isServerError())->toBeFalse();
        });

        it('identifies server errors correctly', function (): void {
            $response = new Response(500, [], 'Internal Server Error');
            $guzzleResponse = new GuzzleResponse($response);

            expect($guzzleResponse->isSuccessful())->toBeFalse()
                ->and($guzzleResponse->isClientError())->toBeFalse()
                ->and($guzzleResponse->isServerError())->toBeTrue();
        });

        it('throws exception for invalid JSON', function (): void {
            $response = new Response(200, [], 'invalid json');
            $guzzleResponse = new GuzzleResponse($response);

            expect(fn (): array => $guzzleResponse->getJson())
                ->toThrow(InvalidArgumentException::class, 'Response body is not valid JSON');
        });
    });
});
