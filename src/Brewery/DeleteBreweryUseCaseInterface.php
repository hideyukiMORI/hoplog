<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

interface DeleteBreweryUseCaseInterface
{
    public function execute(DeleteBreweryInput $input): void;
}
