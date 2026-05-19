<?php

declare(strict_types=1);

namespace Hoplog\Beer;

final readonly class GetBeerByIdUseCase implements GetBeerByIdUseCaseInterface
{
    public function __construct(
        private BeerRepositoryInterface $beers,
    ) {}

    public function execute(GetBeerByIdInput $input): GetBeerByIdOutput
    {
        $beer = $this->beers->findById($input->id);

        if ($beer === null) {
            throw new BeerNotFoundException($input->id);
        }

        return new GetBeerByIdOutput(
            id: (int) $beer->id,
            breweryId: $beer->breweryId,
            name: $beer->name,
            style: $beer->style,
            abv: $beer->abv,
            imageUrl: $beer->imageUrl,
            description: $beer->description,
            createdAt: (string) $beer->createdAt,
            updatedAt: (string) $beer->updatedAt,
        );
    }
}
