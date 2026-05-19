<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

final readonly class ListTastingNotesUseCase implements ListTastingNotesUseCaseInterface
{
    public function __construct(
        private TastingNoteRepositoryInterface $notes,
    ) {}

    public function execute(ListTastingNotesInput $input): ListTastingNotesOutput
    {
        $notes = $this->notes->findAll($input->limit, $input->offset);

        $items = array_map(
            static fn(TastingNote $n) => new ListTastingNoteItem(
                id: (int) $n->id,
                beerId: $n->beerId,
                appearance: $n->appearance,
                aroma: $n->aroma,
                taste: $n->taste,
                overall: $n->overall,
                ratedAt: $n->ratedAt,
                createdAt: (string) $n->createdAt,
                updatedAt: (string) $n->updatedAt,
            ),
            $notes,
        );

        return new ListTastingNotesOutput(items: $items, limit: $input->limit, offset: $input->offset);
    }
}
