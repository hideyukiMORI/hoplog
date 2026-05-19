<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

final readonly class UpdateBreweryUseCase implements UpdateBreweryUseCaseInterface
{
    public function __construct(
        private BreweryRepositoryInterface $breweries,
    ) {
    }

    public function execute(UpdateBreweryInput $input): UpdateBreweryOutput
    {
        $brewery = $this->breweries->findById($input->id);

        if ($brewery === null) {
            throw new BreweryNotFoundException($input->id);
        }

        $updated = new Brewery(
            name: $input->name,
            description: $input->description,
            country: $input->country,
            websiteUrl: $input->websiteUrl,
            id: $input->id,
        );

        $this->breweries->update($updated);

        return new UpdateBreweryOutput(
            id: $input->id,
            name: $input->name,
            description: $input->description,
            country: $input->country,
            websiteUrl: $input->websiteUrl,
        );
    }
}
