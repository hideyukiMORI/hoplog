<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

use Nene2\Http\JsonRequestBodyParser;
use Nene2\Http\JsonResponseFactory;
use Nene2\Routing\Router;
use Nene2\Validation\ValidationError;
use Nene2\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class UpdateBreweryHandler
{
    public function __construct(
        private UpdateBreweryUseCaseInterface $useCase,
        private JsonResponseFactory $response,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getAttribute(Router::PARAMETERS_ATTRIBUTE, []);
        $id = (int) ($params['id'] ?? 0);

        if ($id <= 0) {
            throw new BreweryNotFoundException($id);
        }

        $body = JsonRequestBodyParser::parse($request);

        $errors = [];

        $name = trim((string) ($body['name'] ?? ''));
        $description = trim((string) ($body['description'] ?? ''));
        $country = trim((string) ($body['country'] ?? ''));
        $websiteUrl = trim((string) ($body['website_url'] ?? ''));

        if ($name === '') {
            $errors[] = new ValidationError('name', 'Name is required.', 'required');
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $output = $this->useCase->execute(new UpdateBreweryInput(
            id: $id,
            name: $name,
            description: $description,
            country: $country,
            websiteUrl: $websiteUrl,
        ));

        return $this->response->create([
            'id' => $output->id,
            'name' => $output->name,
            'description' => $output->description,
            'country' => $output->country,
            'website_url' => $output->websiteUrl,
        ]);
    }
}
