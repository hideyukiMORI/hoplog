<?php

declare(strict_types=1);

namespace Hoplog\Beer;

use Hoplog\Brewery\BreweryNotFoundException;
use Hoplog\Brewery\BreweryRepositoryInterface;

final readonly class CreateBeerUseCase implements CreateBeerUseCaseInterface
{
    public function __construct(
        private BeerRepositoryInterface $beers,
        private BreweryRepositoryInterface $breweries,
    ) {
    }

    public function execute(CreateBeerInput $input): CreateBeerOutput
    {
        if ($this->breweries->findById($input->breweryId) === null) {
            throw new BreweryNotFoundException($input->breweryId);
        }

        $id = $this->beers->save(new Beer(
            breweryId: $input->breweryId,
            name: $input->name,
            style: $input->style,
            abv: $input->abv,
            imageUrl: $input->imageUrl,
            description: $input->description,
        ));

        return new CreateBeerOutput(
            id: $id,
            breweryId: $input->breweryId,
            name: $input->name,
            style: $input->style,
            abv: $input->abv,
            imageUrl: $input->imageUrl,
            description: $input->description,
        );
    }
}
