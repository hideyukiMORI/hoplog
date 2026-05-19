<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

use Hoplog\Beer\BeerRepositoryInterface;
use LogicException;
use Nene2\Database\DatabaseQueryExecutorInterface;
use Nene2\DependencyInjection\ContainerBuilder;
use Nene2\DependencyInjection\ServiceProviderInterface;
use Nene2\Error\ProblemDetailsResponseFactory;
use Nene2\Http\JsonResponseFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final readonly class TastingNoteServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder): void
    {
        $builder
            ->set(
                TastingNoteRepositoryInterface::class,
                static function (ContainerInterface $c): TastingNoteRepositoryInterface {
                    $query = $c->get(DatabaseQueryExecutorInterface::class);
                    if (!$query instanceof DatabaseQueryExecutorInterface) {
                        throw new LogicException('Database query executor service is invalid.');
                    }

                    return new PdoTastingNoteRepository($query);
                },
            )
            ->set(
                ListTastingNotesUseCaseInterface::class,
                static function (ContainerInterface $c): ListTastingNotesUseCaseInterface {
                    $repo = $c->get(TastingNoteRepositoryInterface::class);
                    if (!$repo instanceof TastingNoteRepositoryInterface) {
                        throw new LogicException('TastingNote repository service is invalid.');
                    }

                    return new ListTastingNotesUseCase($repo);
                },
            )
            ->set(
                GetTastingNoteByIdUseCaseInterface::class,
                static function (ContainerInterface $c): GetTastingNoteByIdUseCaseInterface {
                    $repo = $c->get(TastingNoteRepositoryInterface::class);
                    if (!$repo instanceof TastingNoteRepositoryInterface) {
                        throw new LogicException('TastingNote repository service is invalid.');
                    }

                    return new GetTastingNoteByIdUseCase($repo);
                },
            )
            ->set(
                CreateTastingNoteUseCaseInterface::class,
                static function (ContainerInterface $c): CreateTastingNoteUseCaseInterface {
                    $notes = $c->get(TastingNoteRepositoryInterface::class);
                    $beers = $c->get(BeerRepositoryInterface::class);
                    if (!$notes instanceof TastingNoteRepositoryInterface) {
                        throw new LogicException('TastingNote repository service is invalid.');
                    }

                    if (!$beers instanceof BeerRepositoryInterface) {
                        throw new LogicException('Beer repository service is invalid.');
                    }

                    return new CreateTastingNoteUseCase($notes, $beers);
                },
            )
            ->set(
                UpdateTastingNoteUseCaseInterface::class,
                static function (ContainerInterface $c): UpdateTastingNoteUseCaseInterface {
                    $notes = $c->get(TastingNoteRepositoryInterface::class);
                    $beers = $c->get(BeerRepositoryInterface::class);
                    if (!$notes instanceof TastingNoteRepositoryInterface) {
                        throw new LogicException('TastingNote repository service is invalid.');
                    }

                    if (!$beers instanceof BeerRepositoryInterface) {
                        throw new LogicException('Beer repository service is invalid.');
                    }

                    return new UpdateTastingNoteUseCase($notes, $beers);
                },
            )
            ->set(
                DeleteTastingNoteUseCaseInterface::class,
                static function (ContainerInterface $c): DeleteTastingNoteUseCaseInterface {
                    $repo = $c->get(TastingNoteRepositoryInterface::class);
                    if (!$repo instanceof TastingNoteRepositoryInterface) {
                        throw new LogicException('TastingNote repository service is invalid.');
                    }

                    return new DeleteTastingNoteUseCase($repo);
                },
            )
            ->set(
                ListTastingNotesHandler::class,
                static function (ContainerInterface $c): ListTastingNotesHandler {
                    $uc = $c->get(ListTastingNotesUseCaseInterface::class);
                    $res = $c->get(JsonResponseFactory::class);
                    if (!$uc instanceof ListTastingNotesUseCaseInterface) {
                        throw new LogicException('ListTastingNotes use case service is invalid.');
                    }

                    if (!$res instanceof JsonResponseFactory) {
                        throw new LogicException('JSON response factory service is invalid.');
                    }

                    return new ListTastingNotesHandler($uc, $res);
                },
            )
            ->set(
                GetTastingNoteByIdHandler::class,
                static function (ContainerInterface $c): GetTastingNoteByIdHandler {
                    $uc = $c->get(GetTastingNoteByIdUseCaseInterface::class);
                    $res = $c->get(JsonResponseFactory::class);
                    if (!$uc instanceof GetTastingNoteByIdUseCaseInterface) {
                        throw new LogicException('GetTastingNoteById use case service is invalid.');
                    }

                    if (!$res instanceof JsonResponseFactory) {
                        throw new LogicException('JSON response factory service is invalid.');
                    }

                    return new GetTastingNoteByIdHandler($uc, $res);
                },
            )
            ->set(
                CreateTastingNoteHandler::class,
                static function (ContainerInterface $c): CreateTastingNoteHandler {
                    $uc = $c->get(CreateTastingNoteUseCaseInterface::class);
                    $res = $c->get(JsonResponseFactory::class);
                    if (!$uc instanceof CreateTastingNoteUseCaseInterface) {
                        throw new LogicException('CreateTastingNote use case service is invalid.');
                    }

                    if (!$res instanceof JsonResponseFactory) {
                        throw new LogicException('JSON response factory service is invalid.');
                    }

                    return new CreateTastingNoteHandler($uc, $res);
                },
            )
            ->set(
                UpdateTastingNoteHandler::class,
                static function (ContainerInterface $c): UpdateTastingNoteHandler {
                    $uc = $c->get(UpdateTastingNoteUseCaseInterface::class);
                    $res = $c->get(JsonResponseFactory::class);
                    if (!$uc instanceof UpdateTastingNoteUseCaseInterface) {
                        throw new LogicException('UpdateTastingNote use case service is invalid.');
                    }

                    if (!$res instanceof JsonResponseFactory) {
                        throw new LogicException('JSON response factory service is invalid.');
                    }

                    return new UpdateTastingNoteHandler($uc, $res);
                },
            )
            ->set(
                DeleteTastingNoteHandler::class,
                static function (ContainerInterface $c): DeleteTastingNoteHandler {
                    $uc = $c->get(DeleteTastingNoteUseCaseInterface::class);
                    $rf = $c->get(ResponseFactoryInterface::class);
                    if (!$uc instanceof DeleteTastingNoteUseCaseInterface) {
                        throw new LogicException('DeleteTastingNote use case service is invalid.');
                    }

                    if (!$rf instanceof ResponseFactoryInterface) {
                        throw new LogicException('Response factory service is invalid.');
                    }

                    return new DeleteTastingNoteHandler($uc, $rf);
                },
            )
            ->set(
                TastingNoteNotFoundExceptionHandler::class,
                static function (ContainerInterface $c): TastingNoteNotFoundExceptionHandler {
                    $pd = $c->get(ProblemDetailsResponseFactory::class);
                    if (!$pd instanceof ProblemDetailsResponseFactory) {
                        throw new LogicException('Problem details response factory service is invalid.');
                    }

                    return new TastingNoteNotFoundExceptionHandler($pd);
                },
            )
            ->set(
                'hoplog.route_registrar.tasting_note',
                static function (ContainerInterface $c): TastingNoteRouteRegistrar {
                    $get = $c->get(GetTastingNoteByIdHandler::class);
                    $create = $c->get(CreateTastingNoteHandler::class);
                    $update = $c->get(UpdateTastingNoteHandler::class);
                    $delete = $c->get(DeleteTastingNoteHandler::class);
                    $list = $c->get(ListTastingNotesHandler::class);
                    if (!$get instanceof GetTastingNoteByIdHandler) {
                        throw new LogicException('GetTastingNoteById handler service is invalid.');
                    }

                    if (!$create instanceof CreateTastingNoteHandler) {
                        throw new LogicException('CreateTastingNote handler service is invalid.');
                    }

                    if (!$update instanceof UpdateTastingNoteHandler) {
                        throw new LogicException('UpdateTastingNote handler service is invalid.');
                    }

                    if (!$delete instanceof DeleteTastingNoteHandler) {
                        throw new LogicException('DeleteTastingNote handler service is invalid.');
                    }

                    if (!$list instanceof ListTastingNotesHandler) {
                        throw new LogicException('ListTastingNotes handler service is invalid.');
                    }

                    return new TastingNoteRouteRegistrar($get, $create, $update, $delete, $list);
                },
            );
    }
}
