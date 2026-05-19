<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

final readonly class GetTastingNoteByIdOutput
{
    public function __construct(
        public int $id,
        public int $beerId,
        public string $appearance,
        public string $aroma,
        public string $taste,
        public int $overall,
        public string $ratedAt,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }
}
