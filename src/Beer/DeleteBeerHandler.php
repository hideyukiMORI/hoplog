<?php

declare(strict_types=1);

namespace Hoplog\Beer;

use Nene2\Routing\Router;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DeleteBeerHandler
{
    public function __construct(
        private DeleteBeerUseCaseInterface $useCase,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getAttribute(Router::PARAMETERS_ATTRIBUTE, []);
        $id = (int) ($params['id'] ?? 0);

        if ($id <= 0) {
            throw new BeerNotFoundException($id);
        }

        $this->useCase->execute(new DeleteBeerInput($id));

        return $this->responseFactory->createResponse(204);
    }
}
