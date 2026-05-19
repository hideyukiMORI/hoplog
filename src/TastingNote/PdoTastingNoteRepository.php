<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

use Nene2\Database\DatabaseQueryExecutorInterface;

final readonly class PdoTastingNoteRepository implements TastingNoteRepositoryInterface
{
    public function __construct(
        private DatabaseQueryExecutorInterface $query,
    ) {
    }

    public function findById(int $id): ?TastingNote
    {
        $row = $this->query->fetchOne(
            'SELECT id, beer_id, appearance, aroma, taste, overall, rated_at, created_at, updated_at FROM tasting_notes WHERE id = ?',
            [$id],
        );

        return $row !== null ? $this->hydrate($row) : null;
    }

    /** @return list<TastingNote> */
    public function findAll(int $limit, int $offset): array
    {
        $rows = $this->query->fetchAll(
            'SELECT id, beer_id, appearance, aroma, taste, overall, rated_at, created_at, updated_at FROM tasting_notes ORDER BY rated_at DESC LIMIT ? OFFSET ?',
            [$limit, $offset],
        );

        return array_map(fn (array $row) => $this->hydrate($row), $rows);
    }

    /** @return list<TastingNote> */
    public function findByBeerId(int $beerId, int $limit, int $offset): array
    {
        $rows = $this->query->fetchAll(
            'SELECT id, beer_id, appearance, aroma, taste, overall, rated_at, created_at, updated_at FROM tasting_notes WHERE beer_id = ? ORDER BY rated_at DESC LIMIT ? OFFSET ?',
            [$beerId, $limit, $offset],
        );

        return array_map(fn (array $row) => $this->hydrate($row), $rows);
    }

    public function save(TastingNote $note): int
    {
        $this->query->execute(
            'INSERT INTO tasting_notes (beer_id, appearance, aroma, taste, overall, rated_at) VALUES (?, ?, ?, ?, ?, ?)',
            [$note->beerId, $note->appearance, $note->aroma, $note->taste, $note->overall, $note->ratedAt],
        );

        return $this->query->lastInsertId();
    }

    public function update(TastingNote $note): void
    {
        $this->query->execute(
            'UPDATE tasting_notes SET beer_id = ?, appearance = ?, aroma = ?, taste = ?, overall = ?, rated_at = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
            [$note->beerId, $note->appearance, $note->aroma, $note->taste, $note->overall, $note->ratedAt, $note->id],
        );
    }

    public function delete(int $id): void
    {
        $this->query->execute('DELETE FROM tasting_notes WHERE id = ?', [$id]);
    }

    /** @param array<string, mixed> $row */
    private function hydrate(array $row): TastingNote
    {
        return new TastingNote(
            beerId: (int) $row['beer_id'],
            appearance: (string) ($row['appearance'] ?? ''),
            aroma: (string) ($row['aroma'] ?? ''),
            taste: (string) ($row['taste'] ?? ''),
            overall: (int) $row['overall'],
            ratedAt: (string) ($row['rated_at'] ?? ''),
            id: (int) $row['id'],
            createdAt: (string) ($row['created_at'] ?? ''),
            updatedAt: (string) ($row['updated_at'] ?? ''),
        );
    }
}
