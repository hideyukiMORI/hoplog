<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

final readonly class CreateBreweryUseCase implements CreateBreweryUseCaseInterface
{
    public function __construct(
        private BreweryRepositoryInterface $breweries,
    ) {
    }

    public function execute(CreateBreweryInput $input): CreateBreweryOutput
    {
        $id = $this->breweries->save(new Brewery(
            name: $input->name,
            description: $input->description,
            country: $input->country,
            websiteUrl: $input->websiteUrl,
        ));

        return new CreateBreweryOutput(
            id: $id,
            name: $input->name,
            description: $input->description,
            country: $input->country,
            websiteUrl: $input->websiteUrl,
        );
    }
}
