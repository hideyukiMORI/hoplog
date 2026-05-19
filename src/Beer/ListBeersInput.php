<?php

declare(strict_types=1);

namespace Hoplog\Beer;

final readonly class ListBeersInput
{
    public function __construct(
        public int $limit,
        public int $offset,
    ) {
    }
}
