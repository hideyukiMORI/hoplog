<?php

declare(strict_types=1);

namespace Hoplog\Beer;

use Nene2\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;

final readonly class BeerRouteRegistrar
{
    public function __construct(
        private GetBeerByIdHandler $getHandler,
        private CreateBeerHandler $createHandler,
        private UpdateBeerHandler $updateHandler,
        private DeleteBeerHandler $deleteHandler,
        private ListBeersHandler $listHandler,
    ) {
    }

    public function __invoke(Router $router): void
    {
        $getHandler = $this->getHandler;
        $createHandler = $this->createHandler;
        $updateHandler = $this->updateHandler;
        $deleteHandler = $this->deleteHandler;
        $listHandler = $this->listHandler;

        $router->get('/beers', static fn (ServerRequestInterface $req) => $listHandler->handle($req));
        $router->get('/beers/{id}', static fn (ServerRequestInterface $req) => $getHandler->handle($req));
        $router->post('/beers', static fn (ServerRequestInterface $req) => $createHandler->handle($req));
        $router->put('/beers/{id}', static fn (ServerRequestInterface $req) => $updateHandler->handle($req));
        $router->delete('/beers/{id}', static fn (ServerRequestInterface $req) => $deleteHandler->handle($req));
    }
}
