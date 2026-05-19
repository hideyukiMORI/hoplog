<?php

declare(strict_types=1);

namespace Hoplog\Beer;

final readonly class CreateBeerInput
{
    public function __construct(
        public int $breweryId,
        public string $name,
        public string $style,
        public float $abv,
        public string $imageUrl,
        public string $description,
    ) {
    }
}
