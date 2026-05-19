<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

use Nene2\Http\JsonRequestBodyParser;
use Nene2\Http\JsonResponseFactory;
use Nene2\Routing\Router;
use Nene2\Validation\ValidationError;
use Nene2\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class UpdateTastingNoteHandler
{
    public function __construct(
        private UpdateTastingNoteUseCaseInterface $useCase,
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

        $body = JsonRequestBodyParser::parse($request);

        $errors = [];

        $beerId = (int) ($body['beer_id'] ?? 0);
        $appearance = trim((string) ($body['appearance'] ?? ''));
        $aroma = trim((string) ($body['aroma'] ?? ''));
        $taste = trim((string) ($body['taste'] ?? ''));
        $overall = (int) ($body['overall'] ?? 0);
        $ratedAt = trim((string) ($body['rated_at'] ?? date('Y-m-d H:i:s')));

        if ($beerId <= 0) {
            $errors[] = new ValidationError('beer_id', 'Valid beer_id is required.', 'required');
        }

        if ($overall < 1 || $overall > 5) {
            $errors[] = new ValidationError('overall', 'Overall must be between 1 and 5.', 'range');
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $output = $this->useCase->execute(new UpdateTastingNoteInput(
            id: $id,
            beerId: $beerId,
            appearance: $appearance,
            aroma: $aroma,
            taste: $taste,
            overall: $overall,
            ratedAt: $ratedAt,
        ));

        return $this->response->create([
            'id' => $output->id,
            'beer_id' => $output->beerId,
            'appearance' => $output->appearance,
            'aroma' => $output->aroma,
            'taste' => $output->taste,
            'overall' => $output->overall,
            'rated_at' => $output->ratedAt,
        ]);
    }
}
