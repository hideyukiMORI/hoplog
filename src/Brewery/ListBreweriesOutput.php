<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

final readonly class ListBreweriesOutput
{
    /** @param list<ListBreweryItem> $items */
    public function __construct(
        public array $items,
        public int $limit,
        public int $offset,
    ) {
    }
}
