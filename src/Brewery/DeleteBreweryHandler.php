<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

use Nene2\Routing\Router;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class DeleteBreweryHandler
{
    public function __construct(
        private DeleteBreweryUseCaseInterface $useCase,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getAttribute(Router::PARAMETERS_ATTRIBUTE, []);
        $id = (int) ($params['id'] ?? 0);

        if ($id <= 0) {
            throw new BreweryNotFoundException($id);
        }

        $this->useCase->execute(new DeleteBreweryInput($id));

        return $this->responseFactory->createResponse(204);
    }
}
