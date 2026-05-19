<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

final readonly class DeleteTastingNoteInput
{
    public function __construct(
        public int $id,
    ) {}
}
