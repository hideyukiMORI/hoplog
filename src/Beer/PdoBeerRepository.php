<?php

declare(strict_types=1);

namespace Hoplog\Beer;

use Nene2\Database\DatabaseQueryExecutorInterface;

final readonly class PdoBeerRepository implements BeerRepositoryInterface
{
    public function __construct(
        private DatabaseQueryExecutorInterface $query,
    ) {}

    public function findById(int $id): ?Beer
    {
        $row = $this->query->fetchOne(
            'SELECT id, brewery_id, name, style, abv, image_url, description, created_at, updated_at FROM beers WHERE id = ?',
            [$id],
        );

        return $row !== null ? $this->hydrate($row) : null;
    }

    /** @return list<Beer> */
    public function findAll(int $limit, int $offset): array
    {
        $rows = $this->query->fetchAll(
            'SELECT id, brewery_id, name, style, abv, image_url, description, created_at, updated_at FROM beers ORDER BY id ASC LIMIT ? OFFSET ?',
            [$limit, $offset],
        );

        return array_map(fn(array $row) => $this->hydrate($row), $rows);
    }

    /** @return list<Beer> */
    public function findByBreweryId(int $breweryId, int $limit, int $offset): array
    {
        $rows = $this->query->fetchAll(
            'SELECT id, brewery_id, name, style, abv, image_url, description, created_at, updated_at FROM beers WHERE brewery_id = ? ORDER BY id ASC LIMIT ? OFFSET ?',
            [$breweryId, $limit, $offset],
        );

        return array_map(fn(array $row) => $this->hydrate($row), $rows);
    }

    public function save(Beer $beer): int
    {
        $this->query->execute(
            'INSERT INTO beers (brewery_id, name, style, abv, image_url, description) VALUES (?, ?, ?, ?, ?, ?)',
            [$beer->breweryId, $beer->name, $beer->style, $beer->abv, $beer->imageUrl, $beer->description],
        );

        return $this->query->lastInsertId();
    }

    public function update(Beer $beer): void
    {
        $this->query->execute(
            'UPDATE beers SET brewery_id = ?, name = ?, style = ?, abv = ?, image_url = ?, description = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
            [$beer->breweryId, $beer->name, $beer->style, $beer->abv, $beer->imageUrl, $beer->description, $beer->id],
        );
    }

    public function delete(int $id): void
    {
        $this->query->execute('DELETE FROM beers WHERE id = ?', [$id]);
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row): Beer
    {
        return new Beer(
            breweryId: (int) $row['brewery_id'],
            name: (string) $row['name'],
            style: (string) ($row['style'] ?? ''),
            abv: (float) ($row['abv'] ?? 0.0),
            imageUrl: (string) ($row['image_url'] ?? ''),
            description: (string) ($row['description'] ?? ''),
            id: (int) $row['id'],
            createdAt: (string) ($row['created_at'] ?? ''),
            updatedAt: (string) ($row['updated_at'] ?? ''),
        );
    }
}
