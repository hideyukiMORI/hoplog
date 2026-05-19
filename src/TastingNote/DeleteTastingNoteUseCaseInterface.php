<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

interface DeleteTastingNoteUseCaseInterface
{
    public function execute(DeleteTastingNoteInput $input): void;
}
