<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

final readonly class CreateBreweryOutput
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $country,
        public string $websiteUrl,
    ) {}
}
