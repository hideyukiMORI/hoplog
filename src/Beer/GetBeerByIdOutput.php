<?php

declare(strict_types=1);

namespace Hoplog\Beer;

final readonly class GetBeerByIdOutput
{
    public function __construct(
        public int $id,
        public int $breweryId,
        public string $name,
        public string $style,
        public float $abv,
        public string $imageUrl,
        public string $description,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }
}
