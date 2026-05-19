<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

interface BreweryRepositoryInterface
{
    public function findById(int $id): ?Brewery;

    /** @return list<Brewery> */
    public function findAll(int $limit, int $offset): array;

    public function save(Brewery $brewery): int;

    public function update(Brewery $brewery): void;

    public function delete(int $id): void;
}
