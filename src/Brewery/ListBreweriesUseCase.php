<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

final readonly class ListBreweriesUseCase implements ListBreweriesUseCaseInterface
{
    public function __construct(
        private BreweryRepositoryInterface $breweries,
    ) {}

    public function execute(ListBreweriesInput $input): ListBreweriesOutput
    {
        $breweries = $this->breweries->findAll($input->limit, $input->offset);

        $items = array_map(
            static fn(Brewery $b) => new ListBreweryItem(
                id: (int) $b->id,
                name: $b->name,
                description: $b->description,
                country: $b->country,
                websiteUrl: $b->websiteUrl,
                createdAt: (string) $b->createdAt,
                updatedAt: (string) $b->updatedAt,
            ),
            $breweries,
        );

        return new ListBreweriesOutput(items: $items, limit: $input->limit, offset: $input->offset);
    }
}
