<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

final readonly class ListTastingNotesInput
{
    public function __construct(
        public int $limit,
        public int $offset,
    ) {
    }
}
