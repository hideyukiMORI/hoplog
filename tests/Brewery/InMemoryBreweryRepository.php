<?php

declare(strict_types=1);

namespace Hoplog\Tests\Brewery;

use Hoplog\Brewery\Brewery;
use Hoplog\Brewery\BreweryRepositoryInterface;

final class InMemoryBreweryRepository implements BreweryRepositoryInterface
{
    /** @var array<int, Brewery> */
    private array $breweries = [];

    private int $nextId = 1;

    /** @param list<Brewery> $breweries */
    public function __construct(array $breweries = [])
    {
        foreach ($breweries as $brewery) {
            if ($brewery->id !== null) {
                $this->breweries[$brewery->id] = $brewery;
                $this->nextId = max($this->nextId, $brewery->id + 1);
            }
        }
    }

    public function findById(int $id): ?Brewery
    {
        return $this->breweries[$id] ?? null;
    }

    /** @return list<Brewery> */
    public function findAll(int $limit, int $offset): array
    {
        return array_slice(array_values($this->breweries), $offset, $limit);
    }

    public function save(Brewery $brewery): int
    {
        $id = $this->nextId++;
        $this->breweries[$id] = new Brewery(
            name: $brewery->name,
            description: $brewery->description,
            country: $brewery->country,
            websiteUrl: $brewery->websiteUrl,
            id: $id,
            createdAt: date('Y-m-d H:i:s'),
            updatedAt: date('Y-m-d H:i:s'),
        );

        return $id;
    }

    public function update(Brewery $brewery): void
    {
        if ($brewery->id !== null && isset($this->breweries[$brewery->id])) {
            $this->breweries[$brewery->id] = $brewery;
        }
    }

    public function delete(int $id): void
    {
        unset($this->breweries[$id]);
    }
}
