<?php

declare(strict_types=1);

namespace Hoplog\Beer;

interface BeerRepositoryInterface
{
    public function findById(int $id): ?Beer;

    /** @return list<Beer> */
    public function findAll(int $limit, int $offset): array;

    /** @return list<Beer> */
    public function findByBreweryId(int $breweryId, int $limit, int $offset): array;

    public function save(Beer $beer): int;

    public function update(Beer $beer): void;

    public function delete(int $id): void;
}
