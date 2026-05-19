<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

interface ListTastingNotesUseCaseInterface
{
    public function execute(ListTastingNotesInput $input): ListTastingNotesOutput;
}
