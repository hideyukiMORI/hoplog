<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

final readonly class DeleteBreweryUseCase implements DeleteBreweryUseCaseInterface
{
    public function __construct(
        private BreweryRepositoryInterface $breweries,
    ) {
    }

    public function execute(DeleteBreweryInput $input): void
    {
        $brewery = $this->breweries->findById($input->id);

        if ($brewery === null) {
            throw new BreweryNotFoundException($input->id);
        }

        $this->breweries->delete($input->id);
    }
}
