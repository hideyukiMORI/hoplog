<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

interface UpdateTastingNoteUseCaseInterface
{
    public function execute(UpdateTastingNoteInput $input): UpdateTastingNoteOutput;
}
