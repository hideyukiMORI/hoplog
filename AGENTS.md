# hoplog — AI Agent Handoff

このドキュメントは Claude Code が hoplog プロジェクトを自走で構築するための引継ぎ指示書。
新しいセッションで開いたら、まずこのファイルを読んで現状を把握してから作業を開始すること。

---

## プロジェクト概要

**hoplog** は NENE2 フレームワーク上に構築するクラフトビールテイスティングノート JSON API。

- リポジトリ: https://github.com/hideyukiMORI/hoplog
- フレームワーク: [hideyukimori/nene2](https://packagist.org/packages/hideyukimori/nene2) `^1.4`
- 目的: NENE2 の「クライアントプロジェクトが composer require から自走でどこまで作れるか」の検証

---

## エンティティ設計（決定済み）

```
Brewery（醸造所）
  id, name, description, country, website_url, created_at, updated_at

Beer（ビール）
  id, brewery_id, name, style, abv (度数 float), image_url, description, created_at, updated_at

TastingNote（テイスティングノート）
  id, beer_id, appearance, aroma, taste, overall (1-5 int), rated_at, created_at, updated_at
```

- `image_url` は文字列フィールド（ファイルアップロード・ストレージは対象外）
- `brewery_id` / `beer_id` は外部キー（整合性チェックは UseCase 層で行う）

---

## 技術スタック

| 項目 | 内容 |
|---|---|
| PHP | `>=8.4.1 <9.0` |
| フレームワーク | `hideyukimori/nene2:^1.4` |
| DB（テスト） | SQLite（インメモリ） |
| DB（本番想定） | MySQL（オプション） |
| コンテナ | Docker（`compose.yaml` を作成する） |
| テスト | PHPUnit（NENE2 同梱） |
| 静的解析 | PHPStan |
| CS | PHP-CS-Fixer |

---

## 完成形の定義

以下がすべて満たされたら「完成」とみなす:

- [ ] `composer check` がグリーン（tests + analyse + cs）
- [ ] Brewery / Beer / TastingNote の CRUD エンドポイント実装済み
- [ ] Beer・TastingNote のリスト取得でページネーション動作
- [ ] OpenAPI spec（`docs/openapi/openapi.yaml`）が全エンドポイントをカバー
- [ ] MCP ツールカタログ（`docs/mcp/tools.json`）に read 系ツールを追加
- [ ] `docker compose run --rm app composer check` がローカルで通る

---

## ワークフロー指示

### 基本ルール

1. **Issue ドリブン**: 作業は必ず GitHub Issue を起点にする
2. **ブランチ命名**: `type/issue-number-summary`（例: `feat/1-project-setup`）
3. **コミット**: Conventional Commits（`feat`, `fix`, `docs`, `chore` 等）
4. **`main` へ直接コミットしない**

### ペース制御（重要）

- **1 Issue 完了するたびに停止してサマリーを出す**
- ユーザーが「次」「続けて」と言ったら次の Issue へ進む
- ユーザーが確認・修正を求めたらそれに対応してから次へ

### Issue の推奨分割

| Issue | 内容 |
|---|---|
| #1 | プロジェクトセットアップ（composer init, Docker, .env, front controller） |
| #2 | Brewery CRUD（Entity / UseCase / Handler / Repository / Routes / Tests） |
| #3 | Beer CRUD（同上） |
| #4 | TastingNote CRUD（同上） |
| #5 | OpenAPI spec 追加（全エンドポイント） |
| #6 | MCP ツールカタログ追加 |
| #7 | 最終 `composer check` 検証・README 整備 |

実装中に気づいた問題は随時 Issue を追加して記録する。

---

## 開発コマンドリファレンス

```bash
# チェック全部
docker compose run --rm app composer check

# 個別
docker compose run --rm app composer test
docker compose run --rm app composer analyse
docker compose run --rm app composer cs
docker compose run --rm app composer cs:fix

# HTTP スモーク
docker compose up -d app
curl -i http://localhost:8080/health
```

---

## NENE2 参照ドキュメント

- セットアップ: https://github.com/hideyukiMORI/NENE2/blob/main/docs/development/client-project-start.md
- エンドポイントスキャフォールド: https://github.com/hideyukiMORI/NENE2/blob/main/docs/development/endpoint-scaffold.md
- ドメイン層: https://github.com/hideyukiMORI/NENE2/blob/main/docs/development/domain-layer.md
- 参照実装（Note）: `vendor/hideyukimori/nene2/src/Example/Note/`
- 参照実装（Tag）: `vendor/hideyukimori/nene2/src/Example/Tag/`

---

## 注意事項

- シークレット（`.env` 実値）はコミットしない
- NENE2 のソースを直接編集しない（`vendor/` 内は読み取り専用）
- 破壊的 git 操作はユーザーの明示的承認を得てから行う
- エラーは RFC 9457 Problem Details で返す（NENE2 の `ProblemDetailsResponseFactory` を使う）
