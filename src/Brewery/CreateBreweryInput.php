<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

final readonly class CreateBreweryInput
{
    public function __construct(
        public string $name,
        public string $description,
        public string $country,
        public string $websiteUrl,
    ) {
    }
}
