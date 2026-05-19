<?php

declare(strict_types=1);

$adapter = getenv('DB_ADAPTER') ?: 'sqlite';

if ($adapter !== 'sqlite') {
    exit(0);
}

$path = getenv('DB_NAME') ?: '/tmp/hoplog.sqlite';

$isNew = !file_exists($path);

$db = new PDO('sqlite:' . $path, options: [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

if ($isNew) {
    $schema = file_get_contents(__DIR__ . '/../database/schema/schema.sql');
    $seed   = file_get_contents(__DIR__ . '/../database/seeds/seed.sql');

    $db->exec((string) $schema);
    $db->exec((string) $seed);

    echo "[db-init] Created {$path} and applied schema + seed." . PHP_EOL;
} else {
    echo "[db-init] {$path} already exists, skipping." . PHP_EOL;
}
