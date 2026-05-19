<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

interface CreateTastingNoteUseCaseInterface
{
    public function execute(CreateTastingNoteInput $input): CreateTastingNoteOutput;
}
