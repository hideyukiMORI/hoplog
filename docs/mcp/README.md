# MCP Integration — hoplog

This document explains how hoplog integrates with the [Model Context Protocol (MCP)](https://modelcontextprotocol.io/) via the NENE2 framework, and serves as a verified working record of the setup.

---

## What This Is

NENE2 ships a built-in stdio MCP server (`LocalMcpServer`) that reads a `tools.json` catalog and proxies MCP tool calls to your app's HTTP API. hoplog wires this up so that Claude Code can call the hoplog REST API directly as MCP tools — no manual HTTP requests needed.

```
Claude Code
    │  (stdio JSON-RPC)
    ▼
bin/mcp-server.php   ← NENE2's LocalMcpServer
    │  (HTTP)
    ▼
http://app:8080      ← hoplog JSON API (Docker container)
```

---

## Verified Working — 2026-05-19

All 6 tools confirmed working end-to-end via Claude Code MCP client:

| Tool | HTTP Status | Sample Response |
|---|---|---|
| `hoplog_list_breweries` | 200 OK | 10 breweries returned |
| `hoplog_get_brewery` | 200 OK | id:1 "月光醸造所" |
| `hoplog_list_beers` | 200 OK | 30 beers returned |
| `hoplog_get_beer` | 200 OK | id:1 "月夜のペールエール" (APA, 5.2%) |
| `hoplog_list_tasting_notes` | 200 OK | 60 notes, ordered by rated_at desc |
| `hoplog_get_tasting_note` | 200 OK | id:60 overall:4 |

---

## How to Set Up MCP in a NENE2 Project

This section documents exactly what hoplog did, so you can replicate it in your own NENE2-based project.

### Step 1 — Create `docs/mcp/tools.json`

Define each tool that maps to an API endpoint. The schema follows NENE2's `LocalMcpToolCatalog` format:

```json
{
  "version": 1,
  "source": "docs/openapi/openapi.yaml",
  "tools": [
    {
      "name": "hoplog_list_breweries",
      "title": "List Breweries",
      "description": "List all breweries with pagination",
      "safety": "read",
      "source": {
        "type": "openapi",
        "operationId": "listBreweries",
        "method": "GET",
        "path": "/breweries"
      },
      "inputSchema": {
        "type": "object",
        "additionalProperties": false,
        "properties": {
          "limit":  { "type": "integer" },
          "offset": { "type": "integer" }
        }
      }
    }
  ]
}
```

Key fields:
- `name` — tool identifier exposed to the MCP client (use a project prefix, e.g. `hoplog_`)
- `safety` — `"read"` for GET endpoints, `"write"` for mutations
- `source.path` — the HTTP path on your running app

### Step 2 — Create `bin/mcp-server.php`

```php
<?php

declare(strict_types=1);

use Nene2\Mcp\LocalMcpException;
use Nene2\Mcp\LocalMcpServer;
use Nene2\Mcp\LocalMcpToolCatalog;
use Nene2\Mcp\NativeLocalMcpHttpClient;

require dirname(__DIR__) . '/vendor/autoload.php';

$root       = dirname(__DIR__);
$apiBaseUrl = getenv('NENE2_LOCAL_API_BASE_URL') ?: 'http://localhost:8080';

$server = new LocalMcpServer(
    new LocalMcpToolCatalog($root . '/docs/mcp/tools.json'),
    new NativeLocalMcpHttpClient(null),
    $apiBaseUrl,
);

while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);
    if ($line === '') { continue; }

    try {
        $message  = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        $response = $server->handle($message);
        if ($response === null) { continue; }
    } catch (Throwable $exception) {
        $response = [
            'jsonrpc' => '2.0',
            'id'      => null,
            'error'   => ['code' => -32700, 'message' => $exception->getMessage()],
        ];
    }

    fwrite(STDOUT, json_encode($response, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL);
}
```

`NENE2_LOCAL_API_BASE_URL` is the base URL the MCP server uses to reach your app. When running via Docker Compose, use the service name as the host (`http://app:8080`), not `localhost`.

### Step 3 — Create `.mcp.json` in the project root

This file tells Claude Code how to launch the MCP server:

```json
{
  "mcpServers": {
    "hoplog": {
      "command": "docker",
      "args": [
        "compose",
        "-f", "/absolute/path/to/your/project/compose.yaml",
        "run", "--rm",
        "-e", "NENE2_LOCAL_API_BASE_URL=http://app:8080",
        "app",
        "php", "bin/mcp-server.php"
      ]
    }
  }
}
```

Important notes:
- Use an **absolute path** for `-f`. Claude Code may be launched from any working directory.
- Pass `NENE2_LOCAL_API_BASE_URL` via `-e` so the MCP server can reach the app container by its Docker network name.
- The MCP server runs as a short-lived `--rm` container each time Claude Code starts it. The **app service must already be running** (`docker compose up -d app`) for HTTP calls to succeed.

### Step 4 — Enable in Claude Code settings

Add to `.claude/settings.local.json`:

```json
{
  "enabledMcpjsonServers": ["hoplog"],
  "enableAllProjectMcpServers": true
}
```

Restart Claude Code. The tools defined in `tools.json` will appear automatically.

---

## Tool Catalog (hoplog)

| Tool | Method | Path | Parameters |
|---|---|---|---|
| `hoplog_list_breweries` | GET | `/breweries` | `limit`, `offset` |
| `hoplog_get_brewery` | GET | `/breweries/{id}` | `id` (required) |
| `hoplog_list_beers` | GET | `/beers` | `limit`, `offset` |
| `hoplog_get_beer` | GET | `/beers/{id}` | `id` (required) |
| `hoplog_list_tasting_notes` | GET | `/tasting-notes` | `limit`, `offset` |
| `hoplog_get_tasting_note` | GET | `/tasting-notes/{id}` | `id` (required) |

All tools are `safety: read` (GET only). Write operations are available via the REST API directly.

---

## Smoke Test (without Claude Code)

To verify the MCP server works independently:

```bash
printf '%s\n' \
  '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"test","version":"0.0.0"}}}' \
  '{"jsonrpc":"2.0","id":2,"method":"tools/list","params":{}}' \
  | docker compose run --rm \
      -e NENE2_LOCAL_API_BASE_URL=http://app:8080 \
      app php bin/mcp-server.php
```

Expected: two JSON-RPC responses — an `initialize` result and a `tools/list` result containing all 6 tools.

---

## Troubleshooting

| Symptom | Likely cause | Fix |
|---|---|---|
| Tools not appearing in Claude Code | `.mcp.json` path wrong or server not listed in settings | Check absolute path in `.mcp.json`; check `enabledMcpjsonServers` |
| Tool call returns error | App container not running | `docker compose up -d app` |
| `Connection refused` on HTTP call | Wrong `NENE2_LOCAL_API_BASE_URL` | Use `http://app:8080` (service name), not `http://localhost:8080` |
| `tools/list` returns empty | `tools.json` path wrong in `mcp-server.php` | Verify `$root . '/docs/mcp/tools.json'` resolves correctly |
