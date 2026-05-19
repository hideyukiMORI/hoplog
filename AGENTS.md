# hoplog — AI Agent Handoff

このドキュメントは Claude Code が hoplog プロジェクトを継続作業するための引継ぎ指示書。
新しいセッションで開いたら、まずこのファイルを読んで現状を把握してから作業を開始すること。

---

## プロジェクト概要

**hoplog** は NENE2 フレームワーク上に構築するクラフトビールテイスティングノート JSON API。

- リポジトリ: https://github.com/hideyukiMORI/hoplog
- フレームワーク: [hideyukimori/nene2](https://packagist.org/packages/hideyukimori/nene2) `^1.4`
- 目的: NENE2 の「クライアントプロジェクトが composer require から自走でどこまで作れるか」の検証

---

## 現在の実装状況（完成済み）

すべての機能は実装・テスト済みで main ブランチにマージされている。

### 実装済み機能

| 機能 | 状態 |
|---|---|
| Brewery CRUD（全5エンドポイント）| ✅ |
| Beer CRUD（全5エンドポイント）| ✅ |
| TastingNote CRUD（全5エンドポイント）| ✅ |
| PHPUnit HTTP 統合テスト（18件）| ✅ |
| PHPStan level 8 | ✅ |
| PHP-CS-Fixer | ✅ |
| OpenAPI 3.1.0 spec | ✅ |
| MCP サーバー + tools.json（6 tools）| ✅ |
| Aesop インスパイア SPA フロントエンド | ✅ |
| SQLite シードデータ（10醸造所/30ビール/60ノート）| ✅ |
| Docker Compose 環境（PHP 8.4 + MySQL）| ✅ |

### 直近の作業（前セッション）

1. **Docker 環境を整備** — `php:8.4-cli` ベースの Dockerfile 作成（`libsqlite3-dev` / Composer 含む）
2. **環境変数のバグ修正** — SQLite 用の変数名が `DB_SQLITE_PATH` → `DB_NAME` に修正済み
3. **CS-Fixer 対応** — `--allow-risky=yes` フラグを composer スクリプトに追加
4. **シードデータ整備** — 架空の醸造所・ビール・テイスティングノート 100 件を `database/seeds/seed.sql` に追加
5. **自動 DB 初期化** — `bin/db-init.php` + compose.yaml 変更で起動時にスキーマ+シード自動適用
6. **MCP サーバー稼働** — `bin/mcp-server.php` + `.mcp.json` で Claude Code から直接 API を呼べる
7. **NENE2 フィードバック** — `docs/nene2-field-trial-10.md` に摩擦 9 件を記録

---

## セッション再起動後にやること

Claude Code を再起動すると `.mcp.json` が読み込まれ、MCP ツールが使えるようになる。
**再起動後の最初のタスク: MCP ツールで hoplog API を実際に操作してみる。**

### 確認手順

1. `docker compose up -d app` でサーバーが起動していることを確認
2. MCP ツール（`hoplog_list_breweries` 等）を呼び出して動作確認
3. 例えば「月光醸造所のビールを全部教えて」「overall 5 のテイスティングノートは？」などを MCP 経由で試す

### 使えるツール

| ツール名 | 説明 |
|---|---|
| `hoplog_list_breweries` | 醸造所一覧（limit/offset 対応）|
| `hoplog_get_brewery` | 醸造所詳細（id 指定）|
| `hoplog_list_beers` | ビール一覧（limit/offset 対応）|
| `hoplog_get_beer` | ビール詳細（id 指定）|
| `hoplog_list_tasting_notes` | テイスティングノート一覧（rated_at 降順）|
| `hoplog_get_tasting_note` | テイスティングノート詳細（id 指定）|

### MCP が動かないとき

```bash
# サーバーが起動しているか確認
curl http://localhost:8080/health

# コンテナが落ちていたら起動
docker compose up -d app

# MCP サーバーを手動でスモークテスト
printf '%s\n' \
  '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"test","version":"0.0.0"}}}' \
  '{"jsonrpc":"2.0","id":2,"method":"tools/list","params":{}}' \
  | docker compose run --rm -e NENE2_LOCAL_API_BASE_URL=http://app:8080 app php bin/mcp-server.php
```

---

## エンティティ設計

```
Brewery（醸造所）
  id, name, description, country, website_url, created_at, updated_at

Beer（ビール）
  id, brewery_id, name, style, abv (度数 float), image_url, description, created_at, updated_at

TastingNote（テイスティングノート）
  id, beer_id, appearance, aroma, taste, overall (1-5 int), rated_at, created_at, updated_at
```

---

## 開発コマンドリファレンス

```bash
# フルチェック（tests + analyse + cs）
docker compose run --rm app composer check

# 個別
docker compose run --rm app composer test       # PHPUnit（18 tests, 62 assertions）
docker compose run --rm app composer analyse    # PHPStan level 8
docker compose run --rm app composer cs         # PHP-CS-Fixer (check)
docker compose run --rm app composer cs:fix     # PHP-CS-Fixer (fix)
docker compose run --rm app composer db:init    # SQLite スキーマ + シード手動適用

# サーバー起動（初回は Docker イメージのビルドが必要）
docker compose build
docker compose up -d app

# ヘルスチェック
curl http://localhost:8080/health
```

---

## プロジェクト構成

```
src/
  Brewery/            # Entity / UseCase / Handler / Repository / ServiceProvider / RouteRegistrar
  Beer/               # 同上
  TastingNote/        # 同上
  HoplogServiceProvider.php
  HoplogContainerFactory.php
tests/
  Brewery/            # HTTP 統合テスト（InMemoryRepository 使用）
  Beer/
  TastingNote/
bin/
  db-init.php         # SQLite スキーマ + シード自動適用スクリプト
  mcp-server.php      # hoplog 向け stdio MCP サーバー
database/
  schema/schema.sql   # テーブル定義
  seeds/seed.sql      # 架空データ 100 件
docs/
  openapi/openapi.yaml    # OpenAPI 3.1.0 全 16 エンドポイント
  mcp/tools.json          # MCP ツールカタログ（6 read-only tools）
  nene2-field-trial-10.md # NENE2 フィードバック（摩擦 9 件）
public/
  index.php           # フロントコントローラー
  hoplog.html         # SPA フロントエンド（Aesop インスパイア）
```

---

## NENE2 参照ドキュメント

- 参照実装（Note）: `vendor/hideyukimori/nene2/src/Example/Note/`
- 参照実装（Tag）: `vendor/hideyukimori/nene2/src/Example/Tag/`
- MCP サーバー実装: `vendor/hideyukimori/nene2/tools/local-mcp-server.php`
- env 変数リファレンス: `vendor/hideyukimori/nene2/docs/reference/environment-variables.md`

---

## 既知のハマりポイント（前セッションで発見）

| 問題 | 正解 |
|---|---|
| SQLite のパスは `DB_SQLITE_PATH` ではなく `DB_NAME` | `DB_NAME=/tmp/hoplog.sqlite` |
| SQLite でも `DB_HOST` / `DB_USER` / `DB_CHARSET` が必須（ダミー値でよい）| `.env.example` 参照 |
| PHP-CS-Fixer の `declare_strict_types` は risky 扱い | `--allow-risky=yes` が必要 |
| コンテナ再起動で `/tmp/hoplog.sqlite` が消える | `bin/db-init.php` が自動再作成する |
| `APP_DEBUG=true` でも 500 の詳細は見えない（NENE2 側の制限）| コンテナ内で直接 PHP 実行してデバッグ |
| MCP の `http://app` はポート指定が必要 | `http://app:8080` |

---

## NENE2 側の Claude に伝えたいこと

詳細は `docs/nene2-field-trial-10.md` を参照。要点のみ:

### 即対応をお願いしたい（ドキュメント追記だけで解決）

1. **`Router::PARAMETERS_ATTRIBUTE` の記載がない**
   ハンドラでパスパラメータを取り出す方法がドキュメントに存在しない。ソースを grep して発見した。
   ```php
   $params = $request->getAttribute(Router::PARAMETERS_ATTRIBUTE);
   $id = (int) $params['id'];
   ```

2. **SQLite 環境変数が分かりにくい**
   `DB_NAME` にファイルパスを書くこと、SQLite でも `DB_HOST`/`DB_USER`/`DB_CHARSET` が必須であることが未記載。
   新規参入者が確実に詰まる。

3. **`ContainerBuilder::set()` の後勝ちルール**
   `RuntimeServiceProvider` が登録した `RuntimeApplicationFactory` を `set()` で上書きできることが未記載。
   アプリのルートを差し替える唯一の手段なのに、これを知らないと NENE2 の Example ルートが残り続ける。

4. **`ValidationException` の構築例**
   カスタムバリデーション（例: `overall` の範囲チェック）で `ValidationException` を手動スローする方法が未記載。

5. **PHP-CS-Fixer の `--allow-risky=yes`**
   サンプル `composer.json` または Getting Started に注記がほしい。

### 機能改善のお願い

6. **`APP_DEBUG=true` のとき例外詳細を出してほしい**
   環境変数の誤設定で全レスポンスが 500 になっても、ログもレスポンスも汎用メッセージのみ。
   `APP_DEBUG=true` 限定でよいので `$e->getMessage()` を `detail` に含めると助かる。

7. **Dockerfile / Docker How-to の提供**
   `php:8.4-cli` には `libsqlite3-dev` も Composer も入っていない。
   推奨 Dockerfile のサンプルを How-to に掲載してほしい。

---

## 注意事項

- シークレット（`.env` 実値）はコミットしない
- NENE2 のソースを直接編集しない（`vendor/` 内は読み取り専用）
- 破壊的 git 操作はユーザーの明示的承認を得てから行う
- エラーは RFC 9457 Problem Details で返す（NENE2 の `ProblemDetailsResponseFactory` を使う）
