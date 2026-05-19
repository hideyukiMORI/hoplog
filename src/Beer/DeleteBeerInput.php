<?php

declare(strict_types=1);

namespace Hoplog\Beer;

final readonly class DeleteBeerInput
{
    public function __construct(
        public int $id,
    ) {}
}
