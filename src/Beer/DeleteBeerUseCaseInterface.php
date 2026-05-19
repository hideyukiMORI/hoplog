<?php

declare(strict_types=1);

namespace Hoplog\Beer;

interface DeleteBeerUseCaseInterface
{
    public function execute(DeleteBeerInput $input): void;
}
