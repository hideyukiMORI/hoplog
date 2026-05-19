<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

final readonly class CreateTastingNoteInput
{
    public function __construct(
        public int $beerId,
        public string $appearance,
        public string $aroma,
        public string $taste,
        public int $overall,
        public string $ratedAt,
    ) {}
}
