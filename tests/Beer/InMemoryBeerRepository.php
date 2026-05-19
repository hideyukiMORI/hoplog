<?php

declare(strict_types=1);

namespace Hoplog\Tests\Beer;

use Hoplog\Beer\Beer;
use Hoplog\Beer\BeerRepositoryInterface;

final class InMemoryBeerRepository implements BeerRepositoryInterface
{
    /** @var array<int, Beer> */
    private array $beers = [];

    private int $nextId = 1;

    /** @param list<Beer> $beers */
    public function __construct(array $beers = [])
    {
        foreach ($beers as $beer) {
            if ($beer->id !== null) {
                $this->beers[$beer->id] = $beer;
                $this->nextId = max($this->nextId, $beer->id + 1);
            }
        }
    }

    public function findById(int $id): ?Beer
    {
        return $this->beers[$id] ?? null;
    }

    /** @return list<Beer> */
    public function findAll(int $limit, int $offset): array
    {
        return array_slice(array_values($this->beers), $offset, $limit);
    }

    /** @return list<Beer> */
    public function findByBreweryId(int $breweryId, int $limit, int $offset): array
    {
        $filtered = array_values(array_filter($this->beers, fn(Beer $b) => $b->breweryId === $breweryId));

        return array_slice($filtered, $offset, $limit);
    }

    public function save(Beer $beer): int
    {
        $id = $this->nextId++;
        $this->beers[$id] = new Beer(
            breweryId: $beer->breweryId,
            name: $beer->name,
            style: $beer->style,
            abv: $beer->abv,
            imageUrl: $beer->imageUrl,
            description: $beer->description,
            id: $id,
            createdAt: date('Y-m-d H:i:s'),
            updatedAt: date('Y-m-d H:i:s'),
        );

        return $id;
    }

    public function update(Beer $beer): void
    {
        if ($beer->id !== null && isset($this->beers[$beer->id])) {
            $this->beers[$beer->id] = $beer;
        }
    }

    public function delete(int $id): void
    {
        unset($this->beers[$id]);
    }
}
