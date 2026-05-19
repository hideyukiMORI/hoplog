# hoplog

クラフトビールのテイスティングノートを管理する JSON API。
[NENE2](https://github.com/hideyukiMORI/nene2) フレームワーク上に構築。

## 必要なもの

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)（PHP 8.4 はコンテナ内で動く）

## クイックスタート

```bash
git clone https://github.com/hideyukiMORI/hoplog.git
cd hoplog

# Docker イメージをビルド
docker compose build

# 依存パッケージをインストール
docker compose run --rm app composer install

# API サーバーを起動（初回起動時にシードデータが自動投入される）
docker compose up -d app
```

起動後、ブラウザで **http://localhost:8080/hoplog.html** を開くとフロントエンドが使えます。

> **シードデータについて**  
> 初回起動時に架空の醸造所 10 件・ビール 30 件・テイスティングノート 60 件が自動で投入されます。  
> コンテナを再起動しても `/tmp/hoplog.sqlite` が存在しない場合のみ再投入されます。

## API エンドポイント

| メソッド | パス | 説明 |
|--------|------|-------------|
| GET | `/health` | ヘルスチェック |
| GET | `/breweries` | 醸造所一覧 |
| POST | `/breweries` | 醸造所を作成 |
| GET | `/breweries/{id}` | 醸造所を取得 |
| PUT | `/breweries/{id}` | 醸造所を更新 |
| DELETE | `/breweries/{id}` | 醸造所を削除 |
| GET | `/beers` | ビール一覧 |
| POST | `/beers` | ビールを作成 |
| GET | `/beers/{id}` | ビールを取得 |
| PUT | `/beers/{id}` | ビールを更新 |
| DELETE | `/beers/{id}` | ビールを削除 |
| GET | `/tasting-notes` | テイスティングノート一覧（rated_at 降順）|
| POST | `/tasting-notes` | テイスティングノートを作成 |
| GET | `/tasting-notes/{id}` | テイスティングノートを取得 |
| PUT | `/tasting-notes/{id}` | テイスティングノートを更新 |
| DELETE | `/tasting-notes/{id}` | テイスティングノートを削除 |

完全な仕様: [`docs/openapi/openapi.yaml`](docs/openapi/openapi.yaml)

## 開発コマンド

```bash
# テスト・静的解析・コードスタイルを一括実行
docker compose run --rm app composer check

# 個別実行
docker compose run --rm app composer test       # PHPUnit（18 tests）
docker compose run --rm app composer analyse    # PHPStan level 8
docker compose run --rm app composer cs         # PHP-CS-Fixer（チェックのみ）
docker compose run --rm app composer cs:fix     # PHP-CS-Fixer（自動修正）
docker compose run --rm app composer db:init    # DB 手動初期化（スキーマ + シード）
```

## MCP 連携

Claude Code などの MCP クライアントから hoplog API を直接呼び出せます。
`.mcp.json` に設定済みなので、Claude Code を再起動するだけで以下のツールが使えます。

| ツール | 説明 |
|---|---|
| `hoplog_list_breweries` | 醸造所一覧 |
| `hoplog_get_brewery` | 醸造所詳細 |
| `hoplog_list_beers` | ビール一覧 |
| `hoplog_get_beer` | ビール詳細 |
| `hoplog_list_tasting_notes` | テイスティングノート一覧 |
| `hoplog_get_tasting_note` | テイスティングノート詳細 |

> MCP を使うには `docker compose up -d app` でサーバーが起動している必要があります。

## データベース

デフォルトは SQLite（ローカル開発向け）。MySQL に切り替える場合は `.env.example` を参照。

```bash
cp .env.example .env
# .env を編集して DB_ADAPTER=mysql に変更
```

## プロジェクト構成

```
src/
  Brewery/          # Entity / UseCase / Handler / Repository
  Beer/
  TastingNote/
  HoplogServiceProvider.php
  HoplogContainerFactory.php
tests/
  Brewery/          # InMemoryRepository を使った HTTP 統合テスト
  Beer/
  TastingNote/
bin/
  db-init.php       # SQLite スキーマ + シード自動適用
  mcp-server.php    # MCP サーバー（Claude Code 連携用）
database/
  schema/schema.sql
  seeds/seed.sql
docs/
  openapi/openapi.yaml
  mcp/tools.json
public/
  index.php         # フロントコントローラー
  hoplog.html       # SPA フロントエンド
```

## License

MIT
