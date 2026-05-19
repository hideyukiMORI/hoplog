<?php

declare(strict_types=1);

namespace Hoplog\Beer;

use Hoplog\Brewery\BreweryRepositoryInterface;
use LogicException;
use Nene2\Database\DatabaseQueryExecutorInterface;
use Nene2\DependencyInjection\ContainerBuilder;
use Nene2\DependencyInjection\ServiceProviderInterface;
use Nene2\Error\ProblemDetailsResponseFactory;
use Nene2\Http\JsonResponseFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final readonly class BeerServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder): void
    {
        $builder
            ->set(
                BeerRepositoryInterface::class,
                static function (ContainerInterface $c): BeerRepositoryInterface {
                    $query = $c->get(DatabaseQueryExecutorInterface::class);
                    if (!$query instanceof DatabaseQueryExecutorInterface) {
                        throw new LogicException('Database query executor service is invalid.');
                    }

                    return new PdoBeerRepository($query);
                },
            )
            ->set(
                ListBeersUseCaseInterface::class,
                static function (ContainerInterface $c): ListBeersUseCaseInterface {
                    $repo = $c->get(BeerRepositoryInterface::class);
                    if (!$repo instanceof BeerRepositoryInterface) {
                        throw new LogicException('Beer repository service is invalid.');
                    }

                    return new ListBeersUseCase($repo);
                },
            )
            ->set(
                GetBeerByIdUseCaseInterface::class,
                static function (ContainerInterface $c): GetBeerByIdUseCaseInterface {
                    $repo = $c->get(BeerRepositoryInterface::class);
                    if (!$repo instanceof BeerRepositoryInterface) {
                        throw new LogicException('Beer repository service is invalid.');
                    }

                    return new GetBeerByIdUseCase($repo);
                },
            )
            ->set(
                CreateBeerUseCaseInterface::class,
                static function (ContainerInterface $c): CreateBeerUseCaseInterface {
                    $beers = $c->get(BeerRepositoryInterface::class);
                    $breweries = $c->get(BreweryRepositoryInterface::class);
                    if (!$beers instanceof BeerRepositoryInterface) {
                        throw new LogicException('Beer repository service is invalid.');
                    }

                    if (!$breweries instanceof BreweryRepositoryInterface) {
                        throw new LogicException('Brewery repository service is invalid.');
                    }

                    return new CreateBeerUseCase($beers, $breweries);
                },
            )
            ->set(
                UpdateBeerUseCaseInterface::class,
                static function (ContainerInterface $c): UpdateBeerUseCaseInterface {
                    $beers = $c->get(BeerRepositoryInterface::class);
                    $breweries = $c->get(BreweryRepositoryInterface::class);
                    if (!$beers instanceof BeerRepositoryInterface) {
                        throw new LogicException('Beer repository service is invalid.');
                    }

                    if (!$breweries instanceof BreweryRepositoryInterface) {
                        throw new LogicException('Brewery repository service is invalid.');
                    }

                    return new UpdateBeerUseCase($beers, $breweries);
                },
            )
            ->set(
                DeleteBeerUseCaseInterface::class,
                static function (ContainerInterface $c): DeleteBeerUseCaseInterface {
                    $repo = $c->get(BeerRepositoryInterface::class);
                    if (!$repo instanceof BeerRepositoryInterface) {
                        throw new LogicException('Beer repository service is invalid.');
                    }

                    return new DeleteBeerUseCase($repo);
                },
            )
            ->set(
                ListBeersHandler::class,
                static function (ContainerInterface $c): ListBeersHandler {
                    $uc = $c->get(ListBeersUseCaseInterface::class);
                    $res = $c->get(JsonResponseFactory::class);
                    if (!$uc instanceof ListBeersUseCaseInterface) {
                        throw new LogicException('ListBeers use case service is invalid.');
                    }

                    if (!$res instanceof JsonResponseFactory) {
                        throw new LogicException('JSON response factory service is invalid.');
                    }

                    return new ListBeersHandler($uc, $res);
                },
            )
            ->set(
                GetBeerByIdHandler::class,
                static function (ContainerInterface $c): GetBeerByIdHandler {
                    $uc = $c->get(GetBeerByIdUseCaseInterface::class);
                    $res = $c->get(JsonResponseFactory::class);
                    if (!$uc instanceof GetBeerByIdUseCaseInterface) {
                        throw new LogicException('GetBeerById use case service is invalid.');
                    }

                    if (!$res instanceof JsonResponseFactory) {
                        throw new LogicException('JSON response factory service is invalid.');
                    }

                    return new GetBeerByIdHandler($uc, $res);
                },
            )
            ->set(
                CreateBeerHandler::class,
                static function (ContainerInterface $c): CreateBeerHandler {
                    $uc = $c->get(CreateBeerUseCaseInterface::class);
                    $res = $c->get(JsonResponseFactory::class);
                    if (!$uc instanceof CreateBeerUseCaseInterface) {
                        throw new LogicException('CreateBeer use case service is invalid.');
                    }

                    if (!$res instanceof JsonResponseFactory) {
                        throw new LogicException('JSON response factory service is invalid.');
                    }

                    return new CreateBeerHandler($uc, $res);
                },
            )
            ->set(
                UpdateBeerHandler::class,
                static function (ContainerInterface $c): UpdateBeerHandler {
                    $uc = $c->get(UpdateBeerUseCaseInterface::class);
                    $res = $c->get(JsonResponseFactory::class);
                    if (!$uc instanceof UpdateBeerUseCaseInterface) {
                        throw new LogicException('UpdateBeer use case service is invalid.');
                    }

                    if (!$res instanceof JsonResponseFactory) {
                        throw new LogicException('JSON response factory service is invalid.');
                    }

                    return new UpdateBeerHandler($uc, $res);
                },
            )
            ->set(
                DeleteBeerHandler::class,
                static function (ContainerInterface $c): DeleteBeerHandler {
                    $uc = $c->get(DeleteBeerUseCaseInterface::class);
                    $rf = $c->get(ResponseFactoryInterface::class);
                    if (!$uc instanceof DeleteBeerUseCaseInterface) {
                        throw new LogicException('DeleteBeer use case service is invalid.');
                    }

                    if (!$rf instanceof ResponseFactoryInterface) {
                        throw new LogicException('Response factory service is invalid.');
                    }

                    return new DeleteBeerHandler($uc, $rf);
                },
            )
            ->set(
                BeerNotFoundExceptionHandler::class,
                static function (ContainerInterface $c): BeerNotFoundExceptionHandler {
                    $pd = $c->get(ProblemDetailsResponseFactory::class);
                    if (!$pd instanceof ProblemDetailsResponseFactory) {
                        throw new LogicException('Problem details response factory service is invalid.');
                    }

                    return new BeerNotFoundExceptionHandler($pd);
                },
            )
            ->set(
                'hoplog.route_registrar.beer',
                static function (ContainerInterface $c): BeerRouteRegistrar {
                    $get = $c->get(GetBeerByIdHandler::class);
                    $create = $c->get(CreateBeerHandler::class);
                    $update = $c->get(UpdateBeerHandler::class);
                    $delete = $c->get(DeleteBeerHandler::class);
                    $list = $c->get(ListBeersHandler::class);
                    if (!$get instanceof GetBeerByIdHandler) {
                        throw new LogicException('GetBeerById handler service is invalid.');
                    }

                    if (!$create instanceof CreateBeerHandler) {
                        throw new LogicException('CreateBeer handler service is invalid.');
                    }

                    if (!$update instanceof UpdateBeerHandler) {
                        throw new LogicException('UpdateBeer handler service is invalid.');
                    }

                    if (!$delete instanceof DeleteBeerHandler) {
                        throw new LogicException('DeleteBeer handler service is invalid.');
                    }

                    if (!$list instanceof ListBeersHandler) {
                        throw new LogicException('ListBeers handler service is invalid.');
                    }

                    return new BeerRouteRegistrar($get, $create, $update, $delete, $list);
                },
            );
    }
}
