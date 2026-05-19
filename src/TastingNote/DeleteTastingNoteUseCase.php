<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

final readonly class DeleteTastingNoteUseCase implements DeleteTastingNoteUseCaseInterface
{
    public function __construct(
        private TastingNoteRepositoryInterface $notes,
    ) {}

    public function execute(DeleteTastingNoteInput $input): void
    {
        if ($this->notes->findById($input->id) === null) {
            throw new TastingNoteNotFoundException($input->id);
        }

        $this->notes->delete($input->id);
    }
}
