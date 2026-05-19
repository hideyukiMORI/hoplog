<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

use LogicException;
use Nene2\Database\DatabaseQueryExecutorInterface;
use Nene2\DependencyInjection\ContainerBuilder;
use Nene2\DependencyInjection\ServiceProviderInterface;
use Nene2\Error\ProblemDetailsResponseFactory;
use Nene2\Http\JsonResponseFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final readonly class BreweryServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder): void
    {
        $builder
            ->set(
                BreweryRepositoryInterface::class,
                static function (ContainerInterface $c): BreweryRepositoryInterface {
                    $query = $c->get(DatabaseQueryExecutorInterface::class);
                    if (!$query instanceof DatabaseQueryExecutorInterface) {
                        throw new LogicException('Database query executor service is invalid.');
                    }

                    return new PdoBreweryRepository($query);
                },
            )
            ->set(
                ListBreweriesUseCaseInterface::class,
                static function (ContainerInterface $c): ListBreweriesUseCaseInterface {
                    $repo = $c->get(BreweryRepositoryInterface::class);
                    if (!$repo instanceof BreweryRepositoryInterface) {
                        throw new LogicException('Brewery repository service is invalid.');
                    }

                    return new ListBreweriesUseCase($repo);
                },
            )
            ->set(
                GetBreweryByIdUseCaseInterface::class,
                static function (ContainerInterface $c): GetBreweryByIdUseCaseInterface {
                    $repo = $c->get(BreweryRepositoryInterface::class);
                    if (!$repo instanceof BreweryRepositoryInterface) {
                        throw new LogicException('Brewery repository service is invalid.');
                    }

                    return new GetBreweryByIdUseCase($repo);
                },
            )
            ->set(
                CreateBreweryUseCaseInterface::class,
                static function (ContainerInterface $c): CreateBreweryUseCaseInterface {
                    $repo = $c->get(BreweryRepositoryInterface::class);
                    if (!$repo instanceof BreweryRepositoryInterface) {
                        throw new LogicException('Brewery repository service is invalid.');
                    }

                    return new CreateBreweryUseCase($repo);
                },
            )
            ->set(
                UpdateBreweryUseCaseInterface::class,
                static function (ContainerInterface $c): UpdateBreweryUseCaseInterface {
                    $repo = $c->get(BreweryRepositoryInterface::class);
                    if (!$repo instanceof BreweryRepositoryInterface) {
                        throw new LogicException('Brewery repository service is invalid.');
                    }

                    return new UpdateBreweryUseCase($repo);
                },
            )
            ->set(
                DeleteBreweryUseCaseInterface::class,
                static function (ContainerInterface $c): DeleteBreweryUseCaseInterface {
                    $repo = $c->get(BreweryRepositoryInterface::class);
                    if (!$repo instanceof BreweryRepositoryInterface) {
                        throw new LogicException('Brewery repository service is invalid.');
                    }

                    return new DeleteBreweryUseCase($repo);
                },
            )
            ->set(
                ListBreweriesHandler::class,
                static function (ContainerInterface $c): ListBreweriesHandler {
                    $uc = $c->get(ListBreweriesUseCaseInterface::class);
                    $res = $c->get(JsonResponseFactory::class);
                    if (!$uc instanceof ListBreweriesUseCaseInterface) {
                        throw new LogicException('ListBreweries use case service is invalid.');
                    }

                    if (!$res instanceof JsonResponseFactory) {
                        throw new LogicException('JSON response factory service is invalid.');
                    }

                    return new ListBreweriesHandler($uc, $res);
                },
            )
            ->set(
                GetBreweryByIdHandler::class,
                static function (ContainerInterface $c): GetBreweryByIdHandler {
                    $uc = $c->get(GetBreweryByIdUseCaseInterface::class);
                    $res = $c->get(JsonResponseFactory::class);
                    if (!$uc instanceof GetBreweryByIdUseCaseInterface) {
                        throw new LogicException('GetBreweryById use case service is invalid.');
                    }

                    if (!$res instanceof JsonResponseFactory) {
                        throw new LogicException('JSON response factory service is invalid.');
                    }

                    return new GetBreweryByIdHandler($uc, $res);
                },
            )
            ->set(
                CreateBreweryHandler::class,
                static function (ContainerInterface $c): CreateBreweryHandler {
                    $uc = $c->get(CreateBreweryUseCaseInterface::class);
                    $res = $c->get(JsonResponseFactory::class);
                    if (!$uc instanceof CreateBreweryUseCaseInterface) {
                        throw new LogicException('CreateBrewery use case service is invalid.');
                    }

                    if (!$res instanceof JsonResponseFactory) {
                        throw new LogicException('JSON response factory service is invalid.');
                    }

                    return new CreateBreweryHandler($uc, $res);
                },
            )
            ->set(
                UpdateBreweryHandler::class,
                static function (ContainerInterface $c): UpdateBreweryHandler {
                    $uc = $c->get(UpdateBreweryUseCaseInterface::class);
                    $res = $c->get(JsonResponseFactory::class);
                    if (!$uc instanceof UpdateBreweryUseCaseInterface) {
                        throw new LogicException('UpdateBrewery use case service is invalid.');
                    }

                    if (!$res instanceof JsonResponseFactory) {
                        throw new LogicException('JSON response factory service is invalid.');
                    }

                    return new UpdateBreweryHandler($uc, $res);
                },
            )
            ->set(
                DeleteBreweryHandler::class,
                static function (ContainerInterface $c): DeleteBreweryHandler {
                    $uc = $c->get(DeleteBreweryUseCaseInterface::class);
                    $rf = $c->get(ResponseFactoryInterface::class);
                    if (!$uc instanceof DeleteBreweryUseCaseInterface) {
                        throw new LogicException('DeleteBrewery use case service is invalid.');
                    }

                    if (!$rf instanceof ResponseFactoryInterface) {
                        throw new LogicException('Response factory service is invalid.');
                    }

                    return new DeleteBreweryHandler($uc, $rf);
                },
            )
            ->set(
                BreweryNotFoundExceptionHandler::class,
                static function (ContainerInterface $c): BreweryNotFoundExceptionHandler {
                    $pd = $c->get(ProblemDetailsResponseFactory::class);
                    if (!$pd instanceof ProblemDetailsResponseFactory) {
                        throw new LogicException('Problem details response factory service is invalid.');
                    }

                    return new BreweryNotFoundExceptionHandler($pd);
                },
            )
            ->set(
                'hoplog.route_registrar.brewery',
                static function (ContainerInterface $c): BreweryRouteRegistrar {
                    $get = $c->get(GetBreweryByIdHandler::class);
                    $create = $c->get(CreateBreweryHandler::class);
                    $update = $c->get(UpdateBreweryHandler::class);
                    $delete = $c->get(DeleteBreweryHandler::class);
                    $list = $c->get(ListBreweriesHandler::class);
                    if (!$get instanceof GetBreweryByIdHandler) {
                        throw new LogicException('GetBreweryById handler service is invalid.');
                    }

                    if (!$create instanceof CreateBreweryHandler) {
                        throw new LogicException('CreateBrewery handler service is invalid.');
                    }

                    if (!$update instanceof UpdateBreweryHandler) {
                        throw new LogicException('UpdateBrewery handler service is invalid.');
                    }

                    if (!$delete instanceof DeleteBreweryHandler) {
                        throw new LogicException('DeleteBrewery handler service is invalid.');
                    }

                    if (!$list instanceof ListBreweriesHandler) {
                        throw new LogicException('ListBreweries handler service is invalid.');
                    }

                    return new BreweryRouteRegistrar($get, $create, $update, $delete, $list);
                },
            );
    }
}
