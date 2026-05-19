<?php

declare(strict_types=1);

namespace Hoplog\Beer;

final readonly class ListBeersOutput
{
    /** @param list<ListBeerItem> $items */
    public function __construct(
        public array $items,
        public int $limit,
        public int $offset,
    ) {
    }
}
