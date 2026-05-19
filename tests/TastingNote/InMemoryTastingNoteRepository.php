<?php

declare(strict_types=1);

namespace Hoplog\Tests\TastingNote;

use Hoplog\TastingNote\TastingNote;
use Hoplog\TastingNote\TastingNoteRepositoryInterface;

final class InMemoryTastingNoteRepository implements TastingNoteRepositoryInterface
{
    /** @var array<int, TastingNote> */
    private array $notes = [];

    private int $nextId = 1;

    public function findById(int $id): ?TastingNote
    {
        return $this->notes[$id] ?? null;
    }

    /** @return list<TastingNote> */
    public function findAll(int $limit, int $offset): array
    {
        return array_slice(array_values($this->notes), $offset, $limit);
    }

    /** @return list<TastingNote> */
    public function findByBeerId(int $beerId, int $limit, int $offset): array
    {
        $filtered = array_values(array_filter($this->notes, fn (TastingNote $n) => $n->beerId === $beerId));

        return array_slice($filtered, $offset, $limit);
    }

    public function save(TastingNote $note): int
    {
        $id = $this->nextId++;
        $this->notes[$id] = new TastingNote(
            beerId: $note->beerId,
            appearance: $note->appearance,
            aroma: $note->aroma,
            taste: $note->taste,
            overall: $note->overall,
            ratedAt: $note->ratedAt,
            id: $id,
            createdAt: date('Y-m-d H:i:s'),
            updatedAt: date('Y-m-d H:i:s'),
        );

        return $id;
    }

    public function update(TastingNote $note): void
    {
        if ($note->id !== null && isset($this->notes[$note->id])) {
            $this->notes[$note->id] = $note;
        }
    }

    public function delete(int $id): void
    {
        unset($this->notes[$id]);
    }
}
