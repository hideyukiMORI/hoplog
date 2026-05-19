<?php

declare(strict_types=1);

namespace Hoplog\Beer;

use RuntimeException;

final class BeerNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Beer with id {$id} was not found.");
    }
}
