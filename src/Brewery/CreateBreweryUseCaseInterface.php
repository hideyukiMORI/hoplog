<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

interface CreateBreweryUseCaseInterface
{
    public function execute(CreateBreweryInput $input): CreateBreweryOutput;
}
