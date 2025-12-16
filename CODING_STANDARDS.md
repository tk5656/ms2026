# コーディング規約

## 命名規則

### 変数名
- **スネークケース**（小文字 + アンダースコア）
```php
$user_name = '田中太郎';
$item_count = 10;
```

### 関数名
- **スネークケース**（小文字 + アンダースコア）
```php
function get_user_name() {
    // 処理
}

function calculate_total() {
    // 処理
}
```

### クラス名
- **パスカルケース**（大文字始まり、単語の区切りも大文字）
```php
class UserController {
    // 処理
}

class ItemModel {
    // 処理
}
```

### 定数名
- **大文字 + アンダースコア**
```php
const MAX_ITEM_COUNT = 100;
const DATABASE_NAME = 'myapp';
```

### ファイル名
- **スネークケース**（小文字 + アンダースコア）
```
user_list.php
item_detail.php
config.php
```

### HTML/CSSのクラス名
- **スネークケース**（小文字 + アンダースコア）
```html
<div class="user_name">田中太郎</div>
<div class="item_list">商品一覧</div>
<div class="modal_content">モーダル内容</div>
```
```css
.user_name {
    color: blue;
}

.item_list {
    padding: 10px;
}
```

**注意**: HTMLのクラス名とPHPのクラス名は別物です
- HTMLの`class`属性 = CSS用の識別子（スネークケース推奨）
- PHPの`class`キーワード = プログラミングのクラス（パスカルケース）

## PHPコーディング規約

### ファイルパス
```php
// 良い例
require_once __DIR__ . '/config.php';
require_once BASE_PATH . '/src/models/UserModel.php';

// 悪い例
require_once '../config.php';
require_once '../../config.php';
```

### データベース接続
```php
// PDOを使用（必須）
$pdo = new PDO($dsn, $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

// 文字コード設定
$pdo->exec('SET NAMES utf8mb4');
```

### SQL文
```php
// 良い例（プリペアドステートメント）
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$result = $stmt->fetch();

// 悪い例（絶対にNG - SQLインジェクション脆弱）
$sql = "SELECT * FROM users WHERE id = " . $user_id;
$result = $pdo->query($sql);
```

### エラーハンドリング
```php
// データベース接続エラー
$link = mysqli_connect(HOST, USER, PASSWORD, DB);
if (!$link) {
    // エラー処理
    header('Content-Type: application/json');
    echo json_encode(['error' => 'データベース接続に失敗しました']);
    exit;
}

// データ存在チェック
$user = mysqli_fetch_assoc($result);
if (!$user) {
    // エラー処理
    header('Content-Type: application/json');
    echo json_encode(['error' => 'データが見つかりません']);
    exit;
}
```

### 出力時のエスケープ（XSS対策）
```php
// 良い例
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// 悪い例（絶対にNG）
echo $user_input;
```

### POST処理後のリダイレクト
```php
// POST処理後は必ずリダイレクト（二重送信防止）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 処理実行
    // ...
    
    // リダイレクト
    header('Location: result.php');
    exit;
}
```

## JavaScriptコーディング規約

### 変数名
- **キャメルケース**（小文字始まり、単語の区切りは大文字）
```javascript
const userName = '田中太郎';
const itemCount = 10;
```

**注意**: PHPとJavaScriptでは命名規則が異なります
- **PHP**: スネークケース（`$user_name`, `get_user_name()`）
- **JavaScript**: キャメルケース（`userName`, `getUserName()`）

これは各言語の慣習に従うためです。プロジェクト内で統一することが重要です。

### 関数名
- **キャメルケース**
```javascript
function getUserName() {
    // 処理
}

function calculateTotal() {
    // 処理
}
```

### DOM要素の取得
```javascript
// nullチェックを必ず行う
const element = document.getElementById('modal');
if (!element) {
    console.error('要素が見つかりません');
    return;
}
```

### エラーハンドリング
```javascript
fetch('./api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ key: 'value' })
})
.then(response => {
    if (!response.ok) {
        throw new Error('リクエストに失敗しました');
    }
    return response.json();
})
.then(data => {
    if (data.error) {
        alert(data.error);
        return;
    }
    // 正常処理
})
.catch(error => {
    console.error('エラー:', error);
    alert('処理中にエラーが発生しました。');
});
```

## セキュリティ規約

### 必須実装事項

#### 1. SQLインジェクション対策
- **プリペアドステートメントを必ず使用**
- SQLに変数を直接埋め込まない

#### 2. XSS対策
- **出力時に必ずエスケープ**
- `htmlspecialchars()`を使用

#### 3. 二重送信防止
- **POST処理後は必ずリダイレクト**
- JavaScriptでの処理は補助的

#### 4. エラー表示
- **開発環境**: エラー表示ON（デバッグ用）
- **本番環境**: エラー表示OFF（ログに記録のみ）

#### 5. 設定ファイルの管理
- **データベース接続情報はGit管理しない**
- `.gitignore`に追加

### 推奨実装事項

#### 1. CSRF対策
- トークンを使用（将来実装推奨）

#### 2. 入力値検証
- サーバー側で必ず検証
- JavaScriptは補助的

#### 3. レート制限
- 連続リクエストの制限（将来実装推奨）

## コメント規約

### 関数のコメント
```php
/**
 * ユーザー情報を取得
 * 
 * @param int $user_id ユーザーID
 * @return array ユーザー情報
 */
function get_user($user_id) {
    // 処理
}
```

### 複雑な処理にはコメント
```php
// SQL文①: 現在のデータを取得
$sql1 = "SELECT * FROM items WHERE id = ?";
$stmt1 = $pdo->prepare($sql1);
$stmt1->execute([$item_id]);
```

## ファイル構成規約

### 設定ファイル
- `config.php`: データベース接続情報（Git管理外）
- 環境ごとに設定ファイルを分ける（将来）

### SQLファイル
- `init.sql`: データベース初期化SQL
- 初期データも含める

### テンプレートファイル
- `views/`または`tpl/`: HTMLテンプレート
- PHPとHTMLを分離

## その他の重要なルール

### 1. ファイルパス
- `__DIR__`または`BASE_PATH`を使用
- `../`は原則使わない

### 2. データベース操作
- SQL文は1か所にまとめる
- 直接DBを触らない（アプリケーション経由）

### 3. JavaScriptの役割
- 重要な処理は行わない（サーバー側で必ず検証）
- UX向上のための補助的な役割

### 4. エントリーポイント
- すべてのリクエストは`index.php`などのエントリーポイントを通す
- 直接ファイルにアクセスさせない（将来）

### 5. 環境差分
- 開発環境と本番環境の違いを最初に共有
- 設定ファイルで管理

## チェックリスト

コードを書く前に確認：
- [ ] SQLインジェクション対策は実装済みか
- [ ] XSS対策は実装済みか
- [ ] POST処理後はリダイレクトしているか
- [ ] エラーハンドリングは実装済みか
- [ ] ファイルパスは`__DIR__`または`BASE_PATH`を使用しているか
- [ ] データベース接続情報はGit管理外か
- [ ] エラー表示は開発環境のみONか
- [ ] 命名規則は統一されているか
