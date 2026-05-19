<?php

declare(strict_types=1);

namespace Hoplog\Beer;

final readonly class ListBeersUseCase implements ListBeersUseCaseInterface
{
    public function __construct(
        private BeerRepositoryInterface $beers,
    ) {}

    public function execute(ListBeersInput $input): ListBeersOutput
    {
        $beers = $this->beers->findAll($input->limit, $input->offset);

        $items = array_map(
            static fn(Beer $b) => new ListBeerItem(
                id: (int) $b->id,
                breweryId: $b->breweryId,
                name: $b->name,
                style: $b->style,
                abv: $b->abv,
                imageUrl: $b->imageUrl,
                description: $b->description,
                createdAt: (string) $b->createdAt,
                updatedAt: (string) $b->updatedAt,
            ),
            $beers,
        );

        return new ListBeersOutput(items: $items, limit: $input->limit, offset: $input->offset);
    }
}
