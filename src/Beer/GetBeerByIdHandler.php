<?php

declare(strict_types=1);

namespace Hoplog\Beer;

use Nene2\Http\JsonResponseFactory;
use Nene2\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class GetBeerByIdHandler
{
    public function __construct(
        private GetBeerByIdUseCaseInterface $useCase,
        private JsonResponseFactory $response,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getAttribute(Router::PARAMETERS_ATTRIBUTE, []);
        $id = (int) ($params['id'] ?? 0);

        if ($id <= 0) {
            throw new BeerNotFoundException($id);
        }

        $output = $this->useCase->execute(new GetBeerByIdInput($id));

        return $this->response->create([
            'id' => $output->id,
            'brewery_id' => $output->breweryId,
            'name' => $output->name,
            'style' => $output->style,
            'abv' => $output->abv,
            'image_url' => $output->imageUrl,
            'description' => $output->description,
            'created_at' => $output->createdAt,
            'updated_at' => $output->updatedAt,
        ]);
    }
}
