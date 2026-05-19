<?php

declare(strict_types=1);

namespace Hoplog\Tests\Brewery;

use Hoplog\Brewery\Brewery;
use Hoplog\Brewery\BreweryNotFoundExceptionHandler;
use Hoplog\Brewery\BreweryRouteRegistrar;
use Hoplog\Brewery\CreateBreweryHandler;
use Hoplog\Brewery\CreateBreweryUseCase;
use Hoplog\Brewery\DeleteBreweryHandler;
use Hoplog\Brewery\DeleteBreweryUseCase;
use Hoplog\Brewery\GetBreweryByIdHandler;
use Hoplog\Brewery\GetBreweryByIdUseCase;
use Hoplog\Brewery\ListBreweriesHandler;
use Hoplog\Brewery\ListBreweriesUseCase;
use Hoplog\Brewery\UpdateBreweryHandler;
use Hoplog\Brewery\UpdateBreweryUseCase;
use Nene2\Error\ProblemDetailsResponseFactory;
use Nene2\Http\JsonResponseFactory;
use Nene2\Http\RuntimeApplicationFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class BreweryHttpTest extends TestCase
{
    private Psr17Factory $factory;
    private InMemoryBreweryRepository $repository;
    private RequestHandlerInterface $application;

    protected function setUp(): void
    {
        $this->factory = new Psr17Factory();
        $this->repository = new InMemoryBreweryRepository();

        $jsonResponse = new JsonResponseFactory($this->factory, $this->factory);
        $problemDetails = new ProblemDetailsResponseFactory($this->factory, $this->factory);

        $registrar = new BreweryRouteRegistrar(
            new GetBreweryByIdHandler(new GetBreweryByIdUseCase($this->repository), $jsonResponse),
            new CreateBreweryHandler(new CreateBreweryUseCase($this->repository), $jsonResponse),
            new UpdateBreweryHandler(new UpdateBreweryUseCase($this->repository), $jsonResponse),
            new DeleteBreweryHandler(new DeleteBreweryUseCase($this->repository), $this->factory),
            new ListBreweriesHandler(new ListBreweriesUseCase($this->repository), $jsonResponse),
        );

        $this->application = (new RuntimeApplicationFactory(
            $this->factory,
            $this->factory,
            domainExceptionHandlers: [new BreweryNotFoundExceptionHandler($problemDetails)],
            routeRegistrars: [$registrar],
        ))->create();
    }

    public function testGetBreweryByIdReturnsBrewery(): void
    {
        $id = $this->repository->save(new Brewery(name: 'Yona Yona', description: 'Craft brewery', country: 'JP', websiteUrl: 'https://yonayona.jp'));

        $response = $this->application->handle(
            $this->factory->createServerRequest('GET', "https://example.test/breweries/{$id}"),
        );
        $payload = $this->decodeJson($response);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame($id, $payload['id']);
        self::assertSame('Yona Yona', $payload['name']);
        self::assertSame('JP', $payload['country']);
    }

    public function testGetBreweryByIdReturns404WhenAbsent(): void
    {
        $response = $this->application->handle(
            $this->factory->createServerRequest('GET', 'https://example.test/breweries/99'),
        );
        $payload = $this->decodeJson($response);

        self::assertSame(404, $response->getStatusCode());
        self::assertSame('https://nene2.dev/problems/not-found', $payload['type']);
    }

    public function testPostBreweryCreatesAndReturns201(): void
    {
        $body = $this->factory->createStream(json_encode([
            'name' => 'Minoh Beer',
            'description' => 'Osaka brewery',
            'country' => 'JP',
            'website_url' => 'https://minoh.net',
        ], JSON_THROW_ON_ERROR));

        $response = $this->application->handle(
            $this->factory->createServerRequest('POST', 'https://example.test/breweries')->withBody($body),
        );
        $payload = $this->decodeJson($response);

        self::assertSame(201, $response->getStatusCode());
        self::assertStringStartsWith('/breweries/', $response->getHeaderLine('Location'));
        self::assertSame('Minoh Beer', $payload['name']);
        self::assertIsInt($payload['id']);
    }

    public function testPostBreweryReturns422WhenNameMissing(): void
    {
        $body = $this->factory->createStream(json_encode(['country' => 'JP'], JSON_THROW_ON_ERROR));
        $response = $this->application->handle(
            $this->factory->createServerRequest('POST', 'https://example.test/breweries')->withBody($body),
        );
        $payload = $this->decodeJson($response);

        self::assertSame(422, $response->getStatusCode());
        self::assertSame('https://nene2.dev/problems/validation-failed', $payload['type']);
        $fields = array_column($payload['errors'], 'field');
        self::assertContains('name', $fields);
    }

    public function testDeleteBreweryReturns204(): void
    {
        $id = $this->repository->save(new Brewery(name: 'To Delete', description: '', country: '', websiteUrl: ''));

        $response = $this->application->handle(
            $this->factory->createServerRequest('DELETE', "https://example.test/breweries/{$id}"),
        );

        self::assertSame(204, $response->getStatusCode());
        self::assertNull($this->repository->findById($id));
    }

    public function testListBreweriesReturnsEmptyItems(): void
    {
        $response = $this->application->handle(
            $this->factory->createServerRequest('GET', 'https://example.test/breweries'),
        );
        $payload = $this->decodeJson($response);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame([], $payload['items']);
    }

    public function testPutBreweryUpdatesBrewery(): void
    {
        $id = $this->repository->save(new Brewery(name: 'Original', description: '', country: 'JP', websiteUrl: ''));

        $body = $this->factory->createStream(json_encode([
            'name' => 'Updated',
            'description' => 'New desc',
            'country' => 'US',
            'website_url' => '',
        ], JSON_THROW_ON_ERROR));

        $response = $this->application->handle(
            $this->factory->createServerRequest('PUT', "https://example.test/breweries/{$id}")->withBody($body),
        );
        $payload = $this->decodeJson($response);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('Updated', $payload['name']);
        self::assertSame('US', $payload['country']);
    }

    /** @return array<string, mixed> */
    private function decodeJson(ResponseInterface $response): array
    {
        $payload = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($payload);

        return $payload;
    }
}
