<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

interface GetBreweryByIdUseCaseInterface
{
    public function execute(GetBreweryByIdInput $input): GetBreweryByIdOutput;
}
