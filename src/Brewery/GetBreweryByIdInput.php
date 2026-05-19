<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

final readonly class GetBreweryByIdInput
{
    public function __construct(
        public int $id,
    ) {}
}
