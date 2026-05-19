<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

use Hoplog\Beer\BeerNotFoundException;
use Hoplog\Beer\BeerRepositoryInterface;

final readonly class UpdateTastingNoteUseCase implements UpdateTastingNoteUseCaseInterface
{
    public function __construct(
        private TastingNoteRepositoryInterface $notes,
        private BeerRepositoryInterface $beers,
    ) {}

    public function execute(UpdateTastingNoteInput $input): UpdateTastingNoteOutput
    {
        $note = $this->notes->findById($input->id);
        if ($note === null) {
            throw new TastingNoteNotFoundException($input->id);
        }

        if ($this->beers->findById($input->beerId) === null) {
            throw new BeerNotFoundException($input->beerId);
        }

        $updated = new TastingNote(
            beerId: $input->beerId,
            appearance: $input->appearance,
            aroma: $input->aroma,
            taste: $input->taste,
            overall: $input->overall,
            ratedAt: $input->ratedAt,
            id: $input->id,
        );

        $this->notes->update($updated);

        return new UpdateTastingNoteOutput(
            id: $input->id,
            beerId: $input->beerId,
            appearance: $input->appearance,
            aroma: $input->aroma,
            taste: $input->taste,
            overall: $input->overall,
            ratedAt: $input->ratedAt,
        );
    }
}
