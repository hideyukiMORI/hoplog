<?php

declare(strict_types=1);

namespace Hoplog\Tests\Beer;

use Hoplog\Beer\Beer;
use Hoplog\Beer\BeerNotFoundExceptionHandler;
use Hoplog\Beer\BeerRouteRegistrar;
use Hoplog\Beer\CreateBeerHandler;
use Hoplog\Beer\CreateBeerUseCase;
use Hoplog\Beer\DeleteBeerHandler;
use Hoplog\Beer\DeleteBeerUseCase;
use Hoplog\Beer\GetBeerByIdHandler;
use Hoplog\Beer\GetBeerByIdUseCase;
use Hoplog\Beer\ListBeersHandler;
use Hoplog\Beer\ListBeersUseCase;
use Hoplog\Beer\UpdateBeerHandler;
use Hoplog\Beer\UpdateBeerUseCase;
use Hoplog\Brewery\Brewery;
use Hoplog\Brewery\BreweryNotFoundExceptionHandler;
use Hoplog\Tests\Brewery\InMemoryBreweryRepository;
use Nene2\Error\ProblemDetailsResponseFactory;
use Nene2\Http\JsonResponseFactory;
use Nene2\Http\RuntimeApplicationFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class BeerHttpTest extends TestCase
{
    private Psr17Factory $factory;
    private InMemoryBeerRepository $beerRepository;
    private InMemoryBreweryRepository $breweryRepository;
    private RequestHandlerInterface $application;
    private int $breweryId;

    protected function setUp(): void
    {
        $this->factory = new Psr17Factory();
        $this->beerRepository = new InMemoryBeerRepository();
        $this->breweryRepository = new InMemoryBreweryRepository();
        $this->breweryId = $this->breweryRepository->save(
            new Brewery(name: 'Test Brewery', description: '', country: 'JP', websiteUrl: ''),
        );

        $jsonResponse = new JsonResponseFactory($this->factory, $this->factory);
        $problemDetails = new ProblemDetailsResponseFactory($this->factory, $this->factory);

        $registrar = new BeerRouteRegistrar(
            new GetBeerByIdHandler(new GetBeerByIdUseCase($this->beerRepository), $jsonResponse),
            new CreateBeerHandler(new CreateBeerUseCase($this->beerRepository, $this->breweryRepository), $jsonResponse),
            new UpdateBeerHandler(new UpdateBeerUseCase($this->beerRepository, $this->breweryRepository), $jsonResponse),
            new DeleteBeerHandler(new DeleteBeerUseCase($this->beerRepository), $this->factory),
            new ListBeersHandler(new ListBeersUseCase($this->beerRepository), $jsonResponse),
        );

        $this->application = (new RuntimeApplicationFactory(
            $this->factory,
            $this->factory,
            domainExceptionHandlers: [
                new BeerNotFoundExceptionHandler($problemDetails),
                new BreweryNotFoundExceptionHandler($problemDetails),
            ],
            routeRegistrars: [$registrar],
        ))->create();
    }

    public function testGetBeerByIdReturnsBeer(): void
    {
        $id = $this->beerRepository->save(new Beer(
            breweryId: $this->breweryId,
            name: 'Yona Yona Ale',
            style: 'American Pale Ale',
            abv: 5.5,
            imageUrl: '',
            description: '',
        ));

        $response = $this->application->handle(
            $this->factory->createServerRequest('GET', "https://example.test/beers/{$id}"),
        );
        $payload = $this->decodeJson($response);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame($id, $payload['id']);
        self::assertSame('Yona Yona Ale', $payload['name']);
        self::assertSame(5.5, $payload['abv']);
    }

    public function testGetBeerByIdReturns404WhenAbsent(): void
    {
        $response = $this->application->handle(
            $this->factory->createServerRequest('GET', 'https://example.test/beers/99'),
        );
        $payload = $this->decodeJson($response);

        self::assertSame(404, $response->getStatusCode());
        self::assertSame('https://nene2.dev/problems/not-found', $payload['type']);
    }

    public function testPostBeerCreatesAndReturns201(): void
    {
        $body = $this->factory->createStream(json_encode([
            'brewery_id' => $this->breweryId,
            'name' => 'Tokyo Black',
            'style' => 'Porter',
            'abv' => 5.0,
            'image_url' => '',
            'description' => 'Dark and rich',
        ], JSON_THROW_ON_ERROR));

        $response = $this->application->handle(
            $this->factory->createServerRequest('POST', 'https://example.test/beers')->withBody($body),
        );
        $payload = $this->decodeJson($response);

        self::assertSame(201, $response->getStatusCode());
        self::assertSame('Tokyo Black', $payload['name']);
        self::assertIsInt($payload['id']);
    }

    public function testPostBeerReturns422WhenNameMissing(): void
    {
        $body = $this->factory->createStream(json_encode(['brewery_id' => $this->breweryId], JSON_THROW_ON_ERROR));
        $response = $this->application->handle(
            $this->factory->createServerRequest('POST', 'https://example.test/beers')->withBody($body),
        );
        $payload = $this->decodeJson($response);

        self::assertSame(422, $response->getStatusCode());
        $fields = array_column($payload['errors'], 'field');
        self::assertContains('name', $fields);
    }

    public function testDeleteBeerReturns204(): void
    {
        $id = $this->beerRepository->save(new Beer(breweryId: $this->breweryId, name: 'Delete Me', style: '', abv: 0.0, imageUrl: '', description: ''));

        $response = $this->application->handle(
            $this->factory->createServerRequest('DELETE', "https://example.test/beers/{$id}"),
        );

        self::assertSame(204, $response->getStatusCode());
        self::assertNull($this->beerRepository->findById($id));
    }

    public function testListBeersReturnsItems(): void
    {
        $this->beerRepository->save(new Beer(breweryId: $this->breweryId, name: 'A', style: 'IPA', abv: 6.0, imageUrl: '', description: ''));
        $this->beerRepository->save(new Beer(breweryId: $this->breweryId, name: 'B', style: 'Stout', abv: 5.0, imageUrl: '', description: ''));

        $response = $this->application->handle(
            $this->factory->createServerRequest('GET', 'https://example.test/beers'),
        );
        $payload = $this->decodeJson($response);

        self::assertSame(200, $response->getStatusCode());
        self::assertCount(2, $payload['items']);
    }

    /** @return array<string, mixed> */
    private function decodeJson(ResponseInterface $response): array
    {
        $payload = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($payload);

        return $payload;
    }
}
