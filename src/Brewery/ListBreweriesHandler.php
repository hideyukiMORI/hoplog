<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

use Nene2\Http\JsonResponseFactory;
use Nene2\Http\PaginationQueryParser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class ListBreweriesHandler
{
    public function __construct(
        private ListBreweriesUseCaseInterface $useCase,
        private JsonResponseFactory $response,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $pagination = PaginationQueryParser::parse($request);

        $output = $this->useCase->execute(new ListBreweriesInput($pagination->limit, $pagination->offset));

        return $this->response->create([
            'items' => array_map(
                static fn (ListBreweryItem $item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'country' => $item->country,
                    'website_url' => $item->websiteUrl,
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
