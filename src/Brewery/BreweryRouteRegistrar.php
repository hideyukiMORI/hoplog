<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

use Nene2\Routing\Router;
use Psr\Http\Message\ServerRequestInterface;

final readonly class BreweryRouteRegistrar
{
    public function __construct(
        private GetBreweryByIdHandler $getHandler,
        private CreateBreweryHandler $createHandler,
        private UpdateBreweryHandler $updateHandler,
        private DeleteBreweryHandler $deleteHandler,
        private ListBreweriesHandler $listHandler,
    ) {
    }

    public function __invoke(Router $router): void
    {
        $getHandler = $this->getHandler;
        $createHandler = $this->createHandler;
        $updateHandler = $this->updateHandler;
        $deleteHandler = $this->deleteHandler;
        $listHandler = $this->listHandler;

        $router->get('/breweries', static fn (ServerRequestInterface $req) => $listHandler->handle($req));
        $router->get('/breweries/{id}', static fn (ServerRequestInterface $req) => $getHandler->handle($req));
        $router->post('/breweries', static fn (ServerRequestInterface $req) => $createHandler->handle($req));
        $router->put('/breweries/{id}', static fn (ServerRequestInterface $req) => $updateHandler->handle($req));
        $router->delete('/breweries/{id}', static fn (ServerRequestInterface $req) => $deleteHandler->handle($req));
    }
}
