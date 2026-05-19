<?php

declare(strict_types=1);

namespace Hoplog\Beer;

use Nene2\Http\JsonRequestBodyParser;
use Nene2\Http\JsonResponseFactory;
use Nene2\Routing\Router;
use Nene2\Validation\ValidationError;
use Nene2\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class UpdateBeerHandler
{
    public function __construct(
        private UpdateBeerUseCaseInterface $useCase,
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

        $body = JsonRequestBodyParser::parse($request);

        $errors = [];

        $breweryId = (int) ($body['brewery_id'] ?? 0);
        $name = trim((string) ($body['name'] ?? ''));
        $style = trim((string) ($body['style'] ?? ''));
        $abv = (float) ($body['abv'] ?? 0.0);
        $imageUrl = trim((string) ($body['image_url'] ?? ''));
        $description = trim((string) ($body['description'] ?? ''));

        if ($breweryId <= 0) {
            $errors[] = new ValidationError('brewery_id', 'Valid brewery_id is required.', 'required');
        }

        if ($name === '') {
            $errors[] = new ValidationError('name', 'Name is required.', 'required');
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $output = $this->useCase->execute(new UpdateBeerInput(
            id: $id,
            breweryId: $breweryId,
            name: $name,
            style: $style,
            abv: $abv,
            imageUrl: $imageUrl,
            description: $description,
        ));

        return $this->response->create([
            'id' => $output->id,
            'brewery_id' => $output->breweryId,
            'name' => $output->name,
            'style' => $output->style,
            'abv' => $output->abv,
            'image_url' => $output->imageUrl,
            'description' => $output->description,
        ]);
    }
}
