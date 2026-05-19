<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

use Hoplog\Beer\BeerNotFoundException;
use Hoplog\Beer\BeerRepositoryInterface;

final readonly class CreateTastingNoteUseCase implements CreateTastingNoteUseCaseInterface
{
    public function __construct(
        private TastingNoteRepositoryInterface $notes,
        private BeerRepositoryInterface $beers,
    ) {}

    public function execute(CreateTastingNoteInput $input): CreateTastingNoteOutput
    {
        if ($this->beers->findById($input->beerId) === null) {
            throw new BeerNotFoundException($input->beerId);
        }

        $id = $this->notes->save(new TastingNote(
            beerId: $input->beerId,
            appearance: $input->appearance,
            aroma: $input->aroma,
            taste: $input->taste,
            overall: $input->overall,
            ratedAt: $input->ratedAt,
        ));

        return new CreateTastingNoteOutput(
            id: $id,
            beerId: $input->beerId,
            appearance: $input->appearance,
            aroma: $input->aroma,
            taste: $input->taste,
            overall: $input->overall,
            ratedAt: $input->ratedAt,
        );
    }
}
