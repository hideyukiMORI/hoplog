<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

interface GetTastingNoteByIdUseCaseInterface
{
    public function execute(GetTastingNoteByIdInput $input): GetTastingNoteByIdOutput;
}
