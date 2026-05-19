<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

use Nene2\Http\JsonResponseFactory;
use Nene2\Http\PaginationQueryParser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class ListTastingNotesHandler
{
    public function __construct(
        private ListTastingNotesUseCaseInterface $useCase,
        private JsonResponseFactory $response,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $pagination = PaginationQueryParser::parse($request);

        $output = $this->useCase->execute(new ListTastingNotesInput($pagination->limit, $pagination->offset));

        return $this->response->create([
            'items' => array_map(
                static fn (ListTastingNoteItem $item) => [
                    'id' => $item->id,
                    'beer_id' => $item->beerId,
                    'appearance' => $item->appearance,
                    'aroma' => $item->aroma,
                    'taste' => $item->taste,
                    'overall' => $item->overall,
                    'rated_at' => $item->ratedAt,
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
