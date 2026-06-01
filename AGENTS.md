# おのくん里親コミュニティMAP — プロジェクト概要

## 概要
WordPressプラグイン。おのくん里親さんの活動場所を日本・世界マップで可視化するサービス。

## 技術スタック
- WordPress プラグイン（PHP）
- Google Maps API
- カスタム投稿タイプ（satoya_member）

## ファイル構成
- `satoya-community-map.php` — メインファイル
- `includes/cpt.php` — カスタム投稿タイプ登録
- `includes/form-handler.php` — 登録フォーム処理
- `includes/map-shortcode.php` — マップ表示ショートコード [satoya_map]
- `includes/admin.php` — 管理画面
- `includes/llmo.php` — LLMO（JSON-LD構造化データ）
- `assets/css/satoya-map.css` — スタイル
- `assets/js/satoya-map.js` — マップJS
- `assets/images/` — 画像

## ショートコード
- `[satoya_map]` — マップ表示
- `[satoya_form]` — 登録フォーム

## 本番環境
- URL: https://onokun.com/satoya-map/
- WordPress管理画面: https://onokun.com/wp-admin

## 注意事項
- revolink.phpは別プラグイン（revolink-plugin）に切り分け済み
- プラグイン変更後はzipでWordPressにアップロードして反映
- GitHubリポジトリ: https://github.com/hayator11/satoya-community-map
