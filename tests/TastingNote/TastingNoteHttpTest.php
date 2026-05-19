<?php

declare(strict_types=1);

namespace Hoplog\Tests\TastingNote;

use Hoplog\Beer\Beer;
use Hoplog\Beer\BeerNotFoundExceptionHandler;
use Hoplog\Brewery\Brewery;
use Hoplog\Brewery\BreweryNotFoundExceptionHandler;
use Hoplog\TastingNote\CreateTastingNoteHandler;
use Hoplog\TastingNote\CreateTastingNoteUseCase;
use Hoplog\TastingNote\DeleteTastingNoteHandler;
use Hoplog\TastingNote\DeleteTastingNoteUseCase;
use Hoplog\TastingNote\GetTastingNoteByIdHandler;
use Hoplog\TastingNote\GetTastingNoteByIdUseCase;
use Hoplog\TastingNote\ListTastingNotesHandler;
use Hoplog\TastingNote\ListTastingNotesUseCase;
use Hoplog\TastingNote\TastingNote;
use Hoplog\TastingNote\TastingNoteNotFoundExceptionHandler;
use Hoplog\TastingNote\TastingNoteRouteRegistrar;
use Hoplog\TastingNote\UpdateTastingNoteHandler;
use Hoplog\TastingNote\UpdateTastingNoteUseCase;
use Hoplog\Tests\Beer\InMemoryBeerRepository;
use Hoplog\Tests\Brewery\InMemoryBreweryRepository;
use Nene2\Error\ProblemDetailsResponseFactory;
use Nene2\Http\JsonResponseFactory;
use Nene2\Http\RuntimeApplicationFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class TastingNoteHttpTest extends TestCase
{
    private Psr17Factory $factory;
    private InMemoryTastingNoteRepository $noteRepository;
    private InMemoryBeerRepository $beerRepository;
    private RequestHandlerInterface $application;
    private int $beerId;

    protected function setUp(): void
    {
        $this->factory = new Psr17Factory();
        $this->noteRepository = new InMemoryTastingNoteRepository();
        $this->beerRepository = new InMemoryBeerRepository();
        $breweryRepository = new InMemoryBreweryRepository();

        $breweryId = $breweryRepository->save(new Brewery(name: 'Test Brewery', description: '', country: 'JP', websiteUrl: ''));
        $this->beerId = $this->beerRepository->save(new Beer(
            breweryId: $breweryId,
            name: 'Test Beer',
            style: 'IPA',
            abv: 6.0,
            imageUrl: '',
            description: '',
        ));

        $jsonResponse = new JsonResponseFactory($this->factory, $this->factory);
        $problemDetails = new ProblemDetailsResponseFactory($this->factory, $this->factory);

        $registrar = new TastingNoteRouteRegistrar(
            new GetTastingNoteByIdHandler(new GetTastingNoteByIdUseCase($this->noteRepository), $jsonResponse),
            new CreateTastingNoteHandler(new CreateTastingNoteUseCase($this->noteRepository, $this->beerRepository), $jsonResponse),
            new UpdateTastingNoteHandler(new UpdateTastingNoteUseCase($this->noteRepository, $this->beerRepository), $jsonResponse),
            new DeleteTastingNoteHandler(new DeleteTastingNoteUseCase($this->noteRepository), $this->factory),
            new ListTastingNotesHandler(new ListTastingNotesUseCase($this->noteRepository), $jsonResponse),
        );

        $this->application = (new RuntimeApplicationFactory(
            $this->factory,
            $this->factory,
            domainExceptionHandlers: [
                new TastingNoteNotFoundExceptionHandler($problemDetails),
                new BeerNotFoundExceptionHandler($problemDetails),
                new BreweryNotFoundExceptionHandler($problemDetails),
            ],
            routeRegistrars: [$registrar],
        ))->create();
    }

    public function testGetTastingNoteByIdReturnsNote(): void
    {
        $id = $this->noteRepository->save(new TastingNote(
            beerId: $this->beerId,
            appearance: 'Golden',
            aroma: 'Citrus',
            taste: 'Hoppy',
            overall: 4,
            ratedAt: '2026-05-19 12:00:00',
        ));

        $response = $this->application->handle(
            $this->factory->createServerRequest('GET', "https://example.test/tasting-notes/{$id}"),
        );
        $payload = $this->decodeJson($response);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame($id, $payload['id']);
        self::assertSame('Golden', $payload['appearance']);
        self::assertSame(4, $payload['overall']);
    }

    public function testGetTastingNoteByIdReturns404WhenAbsent(): void
    {
        $response = $this->application->handle(
            $this->factory->createServerRequest('GET', 'https://example.test/tasting-notes/99'),
        );
        $payload = $this->decodeJson($response);

        self::assertSame(404, $response->getStatusCode());
    }

    public function testPostTastingNoteCreatesAndReturns201(): void
    {
        $body = $this->factory->createStream(json_encode([
            'beer_id' => $this->beerId,
            'appearance' => 'Amber',
            'aroma' => 'Malty',
            'taste' => 'Sweet and bitter',
            'overall' => 5,
            'rated_at' => '2026-05-19 12:00:00',
        ], JSON_THROW_ON_ERROR));

        $response = $this->application->handle(
            $this->factory->createServerRequest('POST', 'https://example.test/tasting-notes')->withBody($body),
        );
        $payload = $this->decodeJson($response);

        self::assertSame(201, $response->getStatusCode());
        self::assertSame(5, $payload['overall']);
        self::assertIsInt($payload['id']);
    }

    public function testPostTastingNoteReturns422WhenOverallOutOfRange(): void
    {
        $body = $this->factory->createStream(json_encode([
            'beer_id' => $this->beerId,
            'overall' => 6,
        ], JSON_THROW_ON_ERROR));

        $response = $this->application->handle(
            $this->factory->createServerRequest('POST', 'https://example.test/tasting-notes')->withBody($body),
        );
        $payload = $this->decodeJson($response);

        self::assertSame(422, $response->getStatusCode());
        $fields = array_column($payload['errors'], 'field');
        self::assertContains('overall', $fields);
    }

    public function testDeleteTastingNoteReturns204(): void
    {
        $id = $this->noteRepository->save(new TastingNote(
            beerId: $this->beerId,
            appearance: '',
            aroma: '',
            taste: '',
            overall: 3,
            ratedAt: '2026-05-19 12:00:00',
        ));

        $response = $this->application->handle(
            $this->factory->createServerRequest('DELETE', "https://example.test/tasting-notes/{$id}"),
        );

        self::assertSame(204, $response->getStatusCode());
        self::assertNull($this->noteRepository->findById($id));
    }

    /** @return array<string, mixed> */
    private function decodeJson(ResponseInterface $response): array
    {
        $payload = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($payload);

        return $payload;
    }
}
