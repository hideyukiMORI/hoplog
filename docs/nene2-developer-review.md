# NENE2 Developer Review

**Project:** hoplog — craft beer tasting note JSON API  
**NENE2 version:** v1.4.x  
**PHP:** 8.4 (Docker)  
**Date:** 2026-05-19  
**Scope:** 3 domains × 5 CRUD endpoints, PHPUnit integration tests, MCP integration, SQLite + MySQL support

> For the detailed friction report (specific pain points with code examples), see [nene2-field-trial-10.md](nene2-field-trial-10.md).  
> This document is a higher-level, opinionated assessment for developers evaluating NENE2.

---

## What NENE2 Gets Right

### Architecture is consistent and learnable

NENE2 enforces a clear Entity → UseCase → Handler → Repository layering. Once you understand it from the example implementations (Note/Tag), the second and third domains write themselves — you're copying structure, not figuring out new patterns. For a small API, this is exactly the right amount of structure.

### Static analysis is a first-class citizen

PHPStan level 8 is the baseline. This is a deliberate choice that pays off: type errors surface at development time rather than production. The framework's own internals are written to this standard, so integrating with them at level 8 is natural rather than a fight.

### The three-piece toolkit covers 80% of API boilerplate

`PaginationQueryParser`, `JsonRequestBodyParser`, and `ProblemDetailsResponseFactory` handle the repetitive parts of every JSON API — query string parsing, request body validation, and RFC 9457 error responses — without requiring configuration. They just work.

### MCP integration is genuinely novel

`LocalMcpServer` + `tools.json` is NENE2's standout feature. Write a JSON catalog that maps tool names to your API endpoints, drop in the 40-line `bin/mcp-server.php` entrypoint, and your API becomes callable by Claude Code as MCP tools — with zero extra HTTP client code. No other PHP framework ships this. For AI-native applications, this is a meaningful advantage.

Verified working as of 2026-05-19: all 6 hoplog tools returned HTTP 200 end-to-end through Claude Code. See [docs/mcp/README.md](mcp/README.md).

---

## Where It Falls Short

### Documentation gaps cause predictable dead ends

Most of the friction in building hoplog came not from the framework's design but from missing documentation. Two examples that will catch every new user:

- **`Router::PARAMETERS_ATTRIBUTE`** — the only way to read path parameters in a handler, not mentioned anywhere in the docs. You have to grep the source.
- **SQLite environment variables** — the key is `DB_NAME` (not `DB_SQLITE_PATH`), and `DB_HOST`/`DB_USER`/`DB_CHARSET` are required even though SQLite ignores them. Neither fact is documented.

These are fixable with a few lines of documentation, not framework changes. But until they're fixed, new users will hit them and lose time.

### `APP_DEBUG=true` doesn't help when things go wrong

When a misconfiguration causes every request to return 500, `APP_DEBUG=true` still shows only the generic Problem Details response. No stack trace in the response, no stack trace in the logs. Debugging means running PHP directly inside the container. This is the single biggest developer experience gap.

### No official Docker setup

`php:8.4-cli` requires manual installation of `libsqlite3-dev`, `pdo_sqlite`, `pdo_mysql`, and Composer. None of this is documented. A recommended Dockerfile or Docker how-to would eliminate an entire category of setup friction.

---

## NENE2 vs. Laravel

This comparison comes up naturally — both are PHP frameworks for building web applications. But they are not competing for the same jobs.

| | NENE2 | Laravel |
|---|---|---|
| Size | Micro | Full-stack |
| Learning curve | Low (few concepts) | Medium-high (many subsystems) |
| Boilerplate for a JSON API | Low | Medium (depends on what you bring in) |
| Ecosystem | Minimal | Very large |
| MCP / AI integration | Built-in | Not present |
| Documentation | Thin | Comprehensive |
| Best fit | Small-to-medium JSON APIs | Any scale, general web apps |

**"Should I use NENE2 or Laravel?"** depends on the job:

- Building a focused JSON API that you want Claude Code or another AI client to consume directly? NENE2 is the better fit. Laravel's surface area becomes overhead.
- Building a web application with authentication, queues, scheduled jobs, Blade templates, and a team that already knows Laravel? Laravel wins on ecosystem and documentation depth.

NENE2 is not a Laravel alternative in the general sense. It is closer to Slim or Lumen in scope, with MCP integration as a differentiator.

---

## Verdict

NENE2 is a well-designed micro-framework for PHP 8 JSON APIs. The architecture is consistent, the type discipline is strong, and the MCP integration is a genuine differentiator that no comparable framework offers.

The main risk is documentation. The framework's design decisions are mostly sound, but the gaps between what the docs say and what the code requires are numerous enough to make the first project noticeably slower than it should be.

**Recommended for:**
- Greenfield JSON APIs where you want a clean, typed architecture without Laravel's weight
- Projects where Claude Code or another MCP client will be consuming the API
- Developers who are comfortable reading framework source when docs fall short

**Not recommended (yet) for:**
- Teams who need comprehensive documentation and can't afford to investigate internals
- Projects requiring the Laravel ecosystem (queues, Cashier, Telescope, etc.)
- General-purpose web applications beyond JSON API scope
