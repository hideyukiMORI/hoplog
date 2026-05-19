<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

final readonly class Brewery
{
    public function __construct(
        public string $name,
        public string $description,
        public string $country,
        public string $websiteUrl,
        public ?int $id = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {
    }
}
