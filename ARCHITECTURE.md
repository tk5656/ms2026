# システム構成

## 全体アーキテクチャ

```
┌─────────────┐
│   ブラウザ   │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  アプリ      │ ← メインアプリケーション
│  (PHP/JS)   │
└──────┬──────┘
       │
       ▼
┌─────────────┐
│   MySQL     │ ← データベース
└─────────────┘
```

## ディレクトリ構造と役割

### 推奨ディレクトリ構造
```
プロジェクトルート/
├── config/          # 設定ファイル
│   └── config.php   # データベース接続設定（Git管理外）
├── src/             # ソースコード
│   ├── models/      # データモデル（DB操作）
│   ├── controllers/ # コントローラー（処理の制御）
│   ├── views/       # ビュー（テンプレート）
│   └── utils/       # ユーティリティ関数
├── public/          # 公開ディレクトリ
│   ├── js/          # JavaScriptファイル
│   ├── css/         # スタイルシート
│   └── images/      # 画像ファイル
└── sql/             # SQLファイル
    └── init.sql     # データベース初期化SQL
```

### 各ディレクトリの役割

#### config/
設定ファイルを格納。データベース接続情報など（Git管理外）。

#### src/models/
データベース操作を担当。SQL文を1か所にまとめる。

#### src/controllers/
リクエストを受け取り、処理を制御。ビジネスロジックを実装。

#### src/views/
HTMLテンプレート。PHPとHTMLを分離。

#### public/
公開ディレクトリ。静的ファイル（JS、CSS、画像）を格納。

## ファイルパスの基準

### 原則
- **すべて`__DIR__`または`BASE_PATH`を基準にする**
- `../`は原則使わない（可読性と保守性のため）

### 例
```php
// 良い例
require_once __DIR__ . '/config.php';
require_once BASE_PATH . '/src/models/UserModel.php';

// 悪い例
require_once '../config.php';
require_once '../../config.php';
```

### BASE_PATHの定義例
```php
// プロジェクトルートで定義
define('BASE_PATH', __DIR__);

// 使用例
require_once BASE_PATH . '/config/config.php';
```

## データフロー

### 一般的な処理の流れ
1. ユーザーがブラウザでアクセス
2. エントリーポイント（例: `index.php`）でリクエストを受信
3. コントローラーで処理を制御
4. モデルでデータベース操作
5. ビューでHTMLを生成
6. レスポンスを返す

### 重要なポイント
- **POST処理後は必ずリダイレクト**（二重送信防止）
- **JavaScriptは補助的な役割のみ**（サーバー側で必ず検証）
- **すべてのリクエストはエントリーポイントを通す**

## データベース接続

### 接続方法
- PDOを使用（プリペアドステートメントでSQLインジェクション対策）
- 文字コードは`utf8mb4`に統一
- 接続情報は設定ファイルに記載（Git管理外）

### 接続情報の管理例
```php
// config/config.php（Git管理外）
const HOST = 'localhost';
const USER = 'root';
const DB = 'database_name';
const PASSWORD = '';
const CHARSET = 'utf8mb4';
```

### PDO接続の例
```php
$dsn = "mysql:host=" . HOST . ";dbname=" . DB . ";charset=" . CHARSET;
$pdo = new PDO($dsn, USER, PASSWORD, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
```

## セキュリティ考慮事項

### クライアント側（JavaScript）
- 入力値の検証は行うが、**サーバー側でも必ず検証**
- 重要な処理はJavaScriptでは行わない
- データベースへの直接アクセスは不可

### サーバー側（PHP）
- SQLインジェクション対策（プリペアドステートメント）
- XSS対策（出力時のエスケープ）
- CSRF対策（トークン使用、推奨）
- エラーメッセージは本番環境では表示しない
