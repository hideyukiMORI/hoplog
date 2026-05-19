<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

final readonly class GetTastingNoteByIdUseCase implements GetTastingNoteByIdUseCaseInterface
{
    public function __construct(
        private TastingNoteRepositoryInterface $notes,
    ) {
    }

    public function execute(GetTastingNoteByIdInput $input): GetTastingNoteByIdOutput
    {
        $note = $this->notes->findById($input->id);

        if ($note === null) {
            throw new TastingNoteNotFoundException($input->id);
        }

        return new GetTastingNoteByIdOutput(
            id: (int) $note->id,
            beerId: $note->beerId,
            appearance: $note->appearance,
            aroma: $note->aroma,
            taste: $note->taste,
            overall: $note->overall,
            ratedAt: $note->ratedAt,
            createdAt: (string) $note->createdAt,
            updatedAt: (string) $note->updatedAt,
        );
    }
}
