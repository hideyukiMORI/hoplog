<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

final readonly class ListBreweryItem
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $country,
        public string $websiteUrl,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }
}
