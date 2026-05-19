# hoplog

Craft beer tasting note JSON API built on [NENE2](https://github.com/hideyukiMORI/nene2).

## Requirements

- Docker Desktop (PHP 8.4 runs inside the container)

## Quick start

```bash
# Clone and start
git clone https://github.com/hideyukiMORI/hoplog.git
cd hoplog
cp .env.example .env

# Install dependencies
docker compose run --rm app composer install

# Start API server (http://localhost:8080)
docker compose up -d app

# Open the SPA frontend
open public/hoplog.html
```

## API endpoints

| Method | Path | Description |
|--------|------|-------------|
| GET | `/health` | Health check |
| GET | `/breweries` | List breweries |
| POST | `/breweries` | Create brewery |
| GET | `/breweries/{id}` | Get brewery |
| PUT | `/breweries/{id}` | Update brewery |
| DELETE | `/breweries/{id}` | Delete brewery |
| GET | `/beers` | List beers |
| POST | `/beers` | Create beer |
| GET | `/beers/{id}` | Get beer |
| PUT | `/beers/{id}` | Update beer |
| DELETE | `/beers/{id}` | Delete beer |
| GET | `/tasting-notes` | List tasting notes |
| POST | `/tasting-notes` | Create tasting note |
| GET | `/tasting-notes/{id}` | Get tasting note |
| PUT | `/tasting-notes/{id}` | Update tasting note |
| DELETE | `/tasting-notes/{id}` | Delete tasting note |

Full spec: [`docs/openapi/openapi.yaml`](docs/openapi/openapi.yaml)

## Development

```bash
# Run all checks (tests + static analysis + code style)
docker compose run --rm app composer check

# Individual checks
docker compose run --rm app composer test      # PHPUnit
docker compose run --rm app composer analyse   # PHPStan level 8
docker compose run --rm app composer cs        # PHP-CS-Fixer (check)
docker compose run --rm app composer cs:fix    # PHP-CS-Fixer (fix)
```

## Project structure

```
src/
  Brewery/          # Brewery domain (Entity, UseCase, Handler, Repository)
  Beer/             # Beer domain
  TastingNote/      # TastingNote domain
  HoplogServiceProvider.php
  HoplogContainerFactory.php
tests/
  Brewery/          # HTTP integration tests with InMemoryRepository
  Beer/
  TastingNote/
docs/
  openapi/          # OpenAPI 3.1.0 spec
  mcp/              # MCP tools catalog (6 read-only tools)
public/
  index.php         # Front controller
  hoplog.html       # SPA frontend
database/
  schema/schema.sql # SQLite / MySQL schema
```

## MCP integration

`docs/mcp/tools.json` exposes 6 read-only tools for AI assistants:
`hoplog_list_breweries`, `hoplog_get_brewery`, `hoplog_list_beers`, `hoplog_get_beer`, `hoplog_list_tasting_notes`, `hoplog_get_tasting_note`

## License

MIT
