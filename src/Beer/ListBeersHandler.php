<?php

declare(strict_types=1);

namespace Hoplog\Beer;

use Nene2\Http\JsonResponseFactory;
use Nene2\Http\PaginationQueryParser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class ListBeersHandler
{
    public function __construct(
        private ListBeersUseCaseInterface $useCase,
        private JsonResponseFactory $response,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $pagination = PaginationQueryParser::parse($request);

        $output = $this->useCase->execute(new ListBeersInput($pagination->limit, $pagination->offset));

        return $this->response->create([
            'items' => array_map(
                static fn(ListBeerItem $item) => [
                    'id' => $item->id,
                    'brewery_id' => $item->breweryId,
                    'name' => $item->name,
                    'style' => $item->style,
                    'abv' => $item->abv,
                    'image_url' => $item->imageUrl,
                    'description' => $item->description,
                    'created_at' => $item->createdAt,
                    'updated_at' => $item->updatedAt,
                ],
                $output->items,
            ),
            'limit' => $output->limit,
            'offset' => $output->offset,
        ]);
    }
}
