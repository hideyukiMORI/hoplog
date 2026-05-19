<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

final readonly class ListTastingNotesOutput
{
    /** @param list<ListTastingNoteItem> $items */
    public function __construct(
        public array $items,
        public int $limit,
        public int $offset,
    ) {}
}
