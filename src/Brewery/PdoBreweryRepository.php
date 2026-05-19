<?php

declare(strict_types=1);

namespace Hoplog\Brewery;

use Nene2\Database\DatabaseQueryExecutorInterface;

final readonly class PdoBreweryRepository implements BreweryRepositoryInterface
{
    public function __construct(
        private DatabaseQueryExecutorInterface $query,
    ) {
    }

    public function findById(int $id): ?Brewery
    {
        $row = $this->query->fetchOne(
            'SELECT id, name, description, country, website_url, created_at, updated_at FROM breweries WHERE id = ?',
            [$id],
        );

        if ($row === null) {
            return null;
        }

        return $this->hydrate($row);
    }

    /** @return list<Brewery> */
    public function findAll(int $limit, int $offset): array
    {
        $rows = $this->query->fetchAll(
            'SELECT id, name, description, country, website_url, created_at, updated_at FROM breweries ORDER BY id ASC LIMIT ? OFFSET ?',
            [$limit, $offset],
        );

        return array_map(fn (array $row) => $this->hydrate($row), $rows);
    }

    public function save(Brewery $brewery): int
    {
        $this->query->execute(
            'INSERT INTO breweries (name, description, country, website_url) VALUES (?, ?, ?, ?)',
            [$brewery->name, $brewery->description, $brewery->country, $brewery->websiteUrl],
        );

        return $this->query->lastInsertId();
    }

    public function update(Brewery $brewery): void
    {
        $this->query->execute(
            'UPDATE breweries SET name = ?, description = ?, country = ?, website_url = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
            [$brewery->name, $brewery->description, $brewery->country, $brewery->websiteUrl, $brewery->id],
        );
    }

    public function delete(int $id): void
    {
        $this->query->execute('DELETE FROM breweries WHERE id = ?', [$id]);
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row): Brewery
    {
        return new Brewery(
            name: (string) $row['name'],
            description: (string) ($row['description'] ?? ''),
            country: (string) ($row['country'] ?? ''),
            websiteUrl: (string) ($row['website_url'] ?? ''),
            id: (int) $row['id'],
            createdAt: (string) ($row['created_at'] ?? ''),
            updatedAt: (string) ($row['updated_at'] ?? ''),
        );
    }
}
