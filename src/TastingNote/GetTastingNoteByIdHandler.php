<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

use Nene2\Http\JsonResponseFactory;
use Nene2\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class GetTastingNoteByIdHandler
{
    public function __construct(
        private GetTastingNoteByIdUseCaseInterface $useCase,
        private JsonResponseFactory $response,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getAttribute(Router::PARAMETERS_ATTRIBUTE, []);
        $id = (int) ($params['id'] ?? 0);

        if ($id <= 0) {
            throw new TastingNoteNotFoundException($id);
        }

        $output = $this->useCase->execute(new GetTastingNoteByIdInput($id));

        return $this->response->create([
            'id' => $output->id,
            'beer_id' => $output->beerId,
            'appearance' => $output->appearance,
            'aroma' => $output->aroma,
            'taste' => $output->taste,
            'overall' => $output->overall,
            'rated_at' => $output->ratedAt,
            'created_at' => $output->createdAt,
            'updated_at' => $output->updatedAt,
        ]);
    }
}
