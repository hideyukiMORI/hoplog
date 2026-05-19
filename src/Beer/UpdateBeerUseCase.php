<?php

declare(strict_types=1);

namespace Hoplog\Beer;

use Hoplog\Brewery\BreweryNotFoundException;
use Hoplog\Brewery\BreweryRepositoryInterface;

final readonly class UpdateBeerUseCase implements UpdateBeerUseCaseInterface
{
    public function __construct(
        private BeerRepositoryInterface $beers,
        private BreweryRepositoryInterface $breweries,
    ) {
    }

    public function execute(UpdateBeerInput $input): UpdateBeerOutput
    {
        $beer = $this->beers->findById($input->id);
        if ($beer === null) {
            throw new BeerNotFoundException($input->id);
        }

        if ($this->breweries->findById($input->breweryId) === null) {
            throw new BreweryNotFoundException($input->breweryId);
        }

        $updated = new Beer(
            breweryId: $input->breweryId,
            name: $input->name,
            style: $input->style,
            abv: $input->abv,
            imageUrl: $input->imageUrl,
            description: $input->description,
            id: $input->id,
        );

        $this->beers->update($updated);

        return new UpdateBeerOutput(
            id: $input->id,
            breweryId: $input->breweryId,
            name: $input->name,
            style: $input->style,
            abv: $input->abv,
            imageUrl: $input->imageUrl,
            description: $input->description,
        );
    }
}
