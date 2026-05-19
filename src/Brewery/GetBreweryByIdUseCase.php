<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

final readonly class GetBreweryByIdUseCase implements GetBreweryByIdUseCaseInterface
{
    public function __construct(
        private BreweryRepositoryInterface $breweries,
    ) {}

    public function execute(GetBreweryByIdInput $input): GetBreweryByIdOutput
    {
        $brewery = $this->breweries->findById($input->id);

        if ($brewery === null) {
            throw new BreweryNotFoundException($input->id);
        }

        return new GetBreweryByIdOutput(
            id: (int) $brewery->id,
            name: $brewery->name,
            description: $brewery->description,
            country: $brewery->country,
            websiteUrl: $brewery->websiteUrl,
            createdAt: (string) $brewery->createdAt,
            updatedAt: (string) $brewery->updatedAt,
        );
    }
}
