<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

use Nene2\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;

final readonly class TastingNoteRouteRegistrar
{
    public function __construct(
        private GetTastingNoteByIdHandler $getHandler,
        private CreateTastingNoteHandler $createHandler,
        private UpdateTastingNoteHandler $updateHandler,
        private DeleteTastingNoteHandler $deleteHandler,
        private ListTastingNotesHandler $listHandler,
    ) {
    }

    public function __invoke(Router $router): void
    {
        $getHandler = $this->getHandler;
        $createHandler = $this->createHandler;
        $updateHandler = $this->updateHandler;
        $deleteHandler = $this->deleteHandler;
        $listHandler = $this->listHandler;

        $router->get('/tasting-notes', static fn (ServerRequestInterface $req) => $listHandler->handle($req));
        $router->get('/tasting-notes/{id}', static fn (ServerRequestInterface $req) => $getHandler->handle($req));
        $router->post('/tasting-notes', static fn (ServerRequestInterface $req) => $createHandler->handle($req));
        $router->put('/tasting-notes/{id}', static fn (ServerRequestInterface $req) => $updateHandler->handle($req));
        $router->delete('/tasting-notes/{id}', static fn (ServerRequestInterface $req) => $deleteHandler->handle($req));
    }
}
