<?php

declare(strict_types=1);

namespace Hoplog;

use Hoplog\Brewery\BreweryNotFoundExceptionHandler;
use Hoplog\Brewery\BreweryRouteRegistrar;
use Hoplog\Beer\BeerNotFoundExceptionHandler;
use Hoplog\Beer\BeerRouteRegistrar;
use Hoplog\TastingNote\TastingNoteNotFoundExceptionHandler;
use Hoplog\TastingNote\TastingNoteRouteRegistrar;
use LogicException;
use Nene2\DependencyInjection\ContainerBuilder;
use Nene2\Http\RuntimeApplicationFactory;
use Nene2\Http\RuntimeServiceProvider;
use Nene2\Log\RequestIdHolder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

final readonly class HoplogContainerFactory
{
    public function __construct(
        private ?string $projectRoot = null,
    ) {
    }

    public function create(): ContainerInterface
    {
        $projectRoot = $this->projectRoot ?? dirname(__DIR__);

        $builder = (new ContainerBuilder())
            ->value(RuntimeServiceProvider::PROJECT_ROOT, $projectRoot)
            ->addProvider(new RuntimeServiceProvider())
            ->addProvider(new HoplogServiceProvider());

        $builder->set(
            RuntimeApplicationFactory::class,
            static function (ContainerInterface $c): RuntimeApplicationFactory {
                $responseFactory = $c->get(ResponseFactoryInterface::class);
                $streamFactory = $c->get(StreamFactoryInterface::class);
                $logger = $c->get(LoggerInterface::class);
                $requestIdHolder = $c->get(RequestIdHolder::class);

                $breweryExHandler = $c->get(BreweryNotFoundExceptionHandler::class);
                $beerExHandler = $c->get(BeerNotFoundExceptionHandler::class);
                $noteExHandler = $c->get(TastingNoteNotFoundExceptionHandler::class);

                $breweryRegistrar = $c->get('hoplog.route_registrar.brewery');
                $beerRegistrar = $c->get('hoplog.route_registrar.beer');
                $tastingNoteRegistrar = $c->get('hoplog.route_registrar.tasting_note');

                if (!$responseFactory instanceof ResponseFactoryInterface) {
                    throw new LogicException('Response factory service is invalid.');
                }

                if (!$streamFactory instanceof StreamFactoryInterface) {
                    throw new LogicException('Stream factory service is invalid.');
                }

                if (!$logger instanceof LoggerInterface) {
                    throw new LogicException('Logger service is invalid.');
                }

                if (!$requestIdHolder instanceof RequestIdHolder) {
                    throw new LogicException('RequestIdHolder service is invalid.');
                }

                if (!$breweryExHandler instanceof BreweryNotFoundExceptionHandler) {
                    throw new LogicException('BreweryNotFoundExceptionHandler service is invalid.');
                }

                if (!$beerExHandler instanceof BeerNotFoundExceptionHandler) {
                    throw new LogicException('BeerNotFoundExceptionHandler service is invalid.');
                }

                if (!$noteExHandler instanceof TastingNoteNotFoundExceptionHandler) {
                    throw new LogicException('TastingNoteNotFoundExceptionHandler service is invalid.');
                }

                if (!$breweryRegistrar instanceof BreweryRouteRegistrar) {
                    throw new LogicException('Brewery route registrar service is invalid.');
                }

                if (!$beerRegistrar instanceof BeerRouteRegistrar) {
                    throw new LogicException('Beer route registrar service is invalid.');
                }

                if (!$tastingNoteRegistrar instanceof TastingNoteRouteRegistrar) {
                    throw new LogicException('TastingNote route registrar service is invalid.');
                }

                return new RuntimeApplicationFactory(
                    $responseFactory,
                    $streamFactory,
                    $logger,
                    null,
                    [$breweryExHandler, $beerExHandler, $noteExHandler],
                    $requestIdHolder,
                    [$breweryRegistrar, $beerRegistrar, $tastingNoteRegistrar],
                );
            },
        );

        return $builder->build();
    }
}
