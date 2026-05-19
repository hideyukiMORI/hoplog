# hoplog

A JSON API for managing craft beer tasting notes, built on the [NENE2](https://github.com/hideyukiMORI/nene2) framework.

## Requirements

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (PHP 8.4 runs inside the container)

## Quick Start

```bash
git clone https://github.com/hideyukiMORI/hoplog.git
cd hoplog

# Build the Docker image
docker compose build

# Install dependencies
docker compose run --rm app composer install

# Start the API server (seed data is applied automatically on first boot)
docker compose up -d app
```

After starting, open **http://localhost:8080/hoplog.html** in your browser to use the frontend.

> **Seed data**
> On first boot, 10 breweries, 30 beers, and 60 tasting notes are seeded automatically.
> They are re-seeded only when `/tmp/hoplog.sqlite` does not exist (i.e. after a container wipe).

## API Endpoints

| Method | Path | Description |
|--------|------|-------------|
| GET | `/health` | Health check |
| GET | `/breweries` | List breweries |
| POST | `/breweries` | Create a brewery |
| GET | `/breweries/{id}` | Get a brewery |
| PUT | `/breweries/{id}` | Update a brewery |
| DELETE | `/breweries/{id}` | Delete a brewery |
| GET | `/beers` | List beers |
| POST | `/beers` | Create a beer |
| GET | `/beers/{id}` | Get a beer |
| PUT | `/beers/{id}` | Update a beer |
| DELETE | `/beers/{id}` | Delete a beer |
| GET | `/tasting-notes` | List tasting notes (ordered by `rated_at` desc) |
| POST | `/tasting-notes` | Create a tasting note |
| GET | `/tasting-notes/{id}` | Get a tasting note |
| PUT | `/tasting-notes/{id}` | Update a tasting note |
| DELETE | `/tasting-notes/{id}` | Delete a tasting note |

Full spec: [`docs/openapi/openapi.yaml`](docs/openapi/openapi.yaml)

## Development Commands

```bash
# Run all checks at once (tests + static analysis + code style)
docker compose run --rm app composer check

# Run individually
docker compose run --rm app composer test       # PHPUnit (18 tests)
docker compose run --rm app composer analyse    # PHPStan level 8
docker compose run --rm app composer cs         # PHP-CS-Fixer (check only)
docker compose run --rm app composer cs:fix     # PHP-CS-Fixer (auto-fix)
docker compose run --rm app composer db:init    # Manually re-initialize DB (schema + seed)
```

## MCP Integration

hoplog exposes its API as MCP tools so that Claude Code (and other MCP clients) can call it directly.
`.mcp.json` is pre-configured — just restart Claude Code and the tools become available.

| Tool | Description |
|---|---|
| `hoplog_list_breweries` | List breweries |
| `hoplog_get_brewery` | Get brewery by ID |
| `hoplog_list_beers` | List beers |
| `hoplog_get_beer` | Get beer by ID |
| `hoplog_list_tasting_notes` | List tasting notes |
| `hoplog_get_tasting_note` | Get tasting note by ID |

For the full setup guide and verified working proof, see [docs/mcp/README.md](docs/mcp/README.md).

> The MCP server requires `docker compose up -d app` to be running first.

## Database

SQLite by default (for local development). To switch to MySQL, see `.env.example`.

```bash
cp .env.example .env
# Edit .env and set DB_ADAPTER=mysql
```

## Project Structure

```
src/
  Brewery/          # Entity / UseCase / Handler / Repository
  Beer/
  TastingNote/
  HoplogServiceProvider.php
  HoplogContainerFactory.php
tests/
  Brewery/          # HTTP integration tests using InMemoryRepository
  Beer/
  TastingNote/
bin/
  db-init.php       # SQLite schema + seed auto-apply
  mcp-server.php    # MCP server (Claude Code integration)
database/
  schema/schema.sql
  seeds/seed.sql
docs/
  openapi/openapi.yaml
  mcp/tools.json
  mcp/README.md     # MCP setup guide & verification record
public/
  index.php         # Front controller
  hoplog.html       # SPA frontend
```

## License

MIT
