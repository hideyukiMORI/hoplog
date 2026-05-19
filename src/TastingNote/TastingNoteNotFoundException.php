<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

use RuntimeException;

final class TastingNoteNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Tasting note with id {$id} was not found.");
    }
}
