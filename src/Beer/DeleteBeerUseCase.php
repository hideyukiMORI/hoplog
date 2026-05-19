<?php

declare(strict_types=1);

namespace Hoplog\Beer;

final readonly class DeleteBeerUseCase implements DeleteBeerUseCaseInterface
{
    public function __construct(
        private BeerRepositoryInterface $beers,
    ) {
    }

    public function execute(DeleteBeerInput $input): void
    {
        if ($this->beers->findById($input->id) === null) {
            throw new BeerNotFoundException($input->id);
        }

        $this->beers->delete($input->id);
    }
}
