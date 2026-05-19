<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

final readonly class ListBreweriesInput
{
    public function __construct(
        public int $limit,
        public int $offset,
    ) {}
}
