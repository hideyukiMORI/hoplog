<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

interface ListBreweriesUseCaseInterface
{
    public function execute(ListBreweriesInput $input): ListBreweriesOutput;
}
