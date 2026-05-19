<?php

declare(strict_types=1);

namespace Hoplog\Beer;

interface ListBeersUseCaseInterface
{
    public function execute(ListBeersInput $input): ListBeersOutput;
}
