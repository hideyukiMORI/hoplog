<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

use Nene2\Http\JsonResponseFactory;
use Nene2\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class GetBreweryByIdHandler
{
    public function __construct(
        private GetBreweryByIdUseCaseInterface $useCase,
        private JsonResponseFactory $response,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getAttribute(Router::PARAMETERS_ATTRIBUTE, []);
        $id = (int) ($params['id'] ?? 0);

        if ($id <= 0) {
            throw new BreweryNotFoundException($id);
        }

        $output = $this->useCase->execute(new GetBreweryByIdInput($id));

        return $this->response->create([
            'id' => $output->id,
            'name' => $output->name,
            'description' => $output->description,
            'country' => $output->country,
            'website_url' => $output->websiteUrl,
            'created_at' => $output->createdAt,
            'updated_at' => $output->updatedAt,
        ]);
    }
}
