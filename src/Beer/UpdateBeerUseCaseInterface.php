<?php

declare(strict_types=1);

namespace Hoplog\Beer;

interface UpdateBeerUseCaseInterface
{
    public function execute(UpdateBeerInput $input): UpdateBeerOutput;
}
