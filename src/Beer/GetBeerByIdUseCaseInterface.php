<?php

declare(strict_types=1);

namespace Hoplog\Beer;

interface GetBeerByIdUseCaseInterface
{
    public function execute(GetBeerByIdInput $input): GetBeerByIdOutput;
}
