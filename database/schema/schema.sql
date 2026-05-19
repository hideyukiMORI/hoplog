CREATE TABLE IF NOT EXISTS breweries (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    name        VARCHAR(255) NOT NULL,
    description TEXT,
    country     VARCHAR(100),
    website_url VARCHAR(512),
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS beers (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    brewery_id  INTEGER NOT NULL REFERENCES breweries(id),
    name        VARCHAR(255) NOT NULL,
    style       VARCHAR(100),
    abv         REAL,
    image_url   VARCHAR(512),
    description TEXT,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tasting_notes (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    beer_id     INTEGER NOT NULL REFERENCES beers(id),
    appearance  TEXT,
    aroma       TEXT,
    taste       TEXT,
    overall     INTEGER NOT NULL CHECK (overall BETWEEN 1 AND 5),
    rated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
