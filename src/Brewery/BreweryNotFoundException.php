<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

use RuntimeException;

final class BreweryNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Brewery with id {$id} was not found.");
    }
}
