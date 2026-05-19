<?php

declare(strict_types=1);

namespace Hoplog\Beer;

interface CreateBeerUseCaseInterface
{
    public function execute(CreateBeerInput $input): CreateBeerOutput;
}
