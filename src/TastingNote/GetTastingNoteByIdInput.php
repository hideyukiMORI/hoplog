<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

final readonly class GetTastingNoteByIdInput
{
    public function __construct(
        public int $id,
    ) {}
}
