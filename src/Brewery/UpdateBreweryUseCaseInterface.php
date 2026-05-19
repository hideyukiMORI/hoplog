<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

interface UpdateBreweryUseCaseInterface
{
    public function execute(UpdateBreweryInput $input): UpdateBreweryOutput;
}
