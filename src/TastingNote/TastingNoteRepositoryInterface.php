<?php

declare(strict_types=1);

namespace Hoplog\TastingNote;

interface TastingNoteRepositoryInterface
{
    public function findById(int $id): ?TastingNote;

    /** @return list<TastingNote> */
    public function findAll(int $limit, int $offset): array;

    /** @return list<TastingNote> */
    public function findByBeerId(int $beerId, int $limit, int $offset): array;

    public function save(TastingNote $note): int;

    public function update(TastingNote $note): void;

    public function delete(int $id): void;
}
