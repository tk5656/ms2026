# インフォグラフィックページ実装方針

## 基本方針

### HTMLの構造について

**重要な理解：**
- HTMLの各`ranking-item`は**キャラクターごとに固有の要素**として作成する
- 順位（1位、2位など）はHTMLの構造ではなく、**データ属性やテキストコンテンツ**として表現する
- 順位が変わった場合、JavaScriptで**DOMの順序を変更**するだけで対応する

**実装状況：**
- ✅ PHPの`foreach`ループで動的に生成
- ✅ `data-character-id`、`data-rank`、`data-votes`、`data-max-votes`属性を追加済み
- ✅ `order`プロパティで順序を制御

### HTML構造の例

```html
<!-- 順位で記述するのではなく、キャラクターごとに記述 -->
<div class="ranking-item" data-character-id="0" data-rank="1" data-votes="40">
    <div class="pledge-item">
        <p class="rank">1</p> <!-- 順位はテキストとして表示 -->
        <p class="pledge-name">コンビニ</p>
        <!-- ... -->
    </div>
</div>

<div class="ranking-item" data-character-id="2" data-rank="2" data-votes="35">
    <!-- ... -->
</div>
```

**ポイント：**
- `data-character-id`：キャラクター固有のID（DBのIDと対応）
- `data-rank`：現在の順位（動的に更新される）
- `data-votes`：現在の投票数（動的に更新される）
- `p.rank`：順位の表示テキスト（動的に更新される）

## 実装の流れ

### 1. PHP側（`infographic.php`）

**処理内容：**
1. ✅ データベースから`characters`テーブルの`id`と`current_vote`を取得
2. ✅ キャラクター情報（名前、年齢、公約、キャッチコピー、画像、色、クラス名）を配列で定義
3. ✅ 投票数でソート（降順、同票はID順）
4. ✅ 順位を計算（同順位対応：「1位」「1位」「3位」形式）
5. ✅ ゲージの最大値を100票に固定（100票を100%として表示）
6. ✅ 表示用の計算値（`order`、`gaugeWidth`）を事前計算
7. ✅ エラーハンドリング関数（`sendErrorResponse()`）を実装
8. ✅ HTMLエスケープ用ヘルパー関数（`h()`）を実装

**AJAXリクエスト判定：**
- `X-Requested-With: XMLHttpRequest`ヘッダーがある場合：JSONを返す
- 通常リクエストの場合：HTMLテンプレートを読み込む

**JSON形式：**
```json
{
  "ranking": [
    {
      "id": 0,
      "rank": 1,
      "votes": 40,
      "name": "雪原ペン次",
      "age": 40,
      "pledgeShort": "コンビニ",
      "catchCopy": "ハッピーが一番",
      "image": "character0.png",
      "color": "#0000ff"
    },
    {
      "id": 1,
      "rank": 2,
      "votes": 32,
      "name": "虹宮ニョロ明",
      "age": 32,
      "pledgeShort": "落書き",
      "catchCopy": "自由が一番じゃん！",
      "image": "character1.png",
      "color": "#ff0000"
    },
    {
      "id": 2,
      "rank": 3,
      "votes": 35,
      "name": "鳥谷コケ蔵",
      "age": 35,
      "pledgeShort": "ビアガーデン",
      "catchCopy": "校則？平和ならいい。",
      "image": "character2.png",
      "color": "#fbff00"
    },
    {
      "id": 3,
      "rank": 4,
      "votes": 36,
      "name": "犬山イチ郎",
      "age": 36,
      "pledgeShort": "喫煙所",
      "catchCopy": "息抜きが必要でしょ？",
      "image": "character3.png",
      "color": "#3bf53b"
    },
    {
      "id": 4,
      "rank": 5,
      "votes": 28,
      "name": "猫川ニャミ子",
      "age": 28,
      "pledgeShort": "ゲーミング",
      "catchCopy": "やるからには、勝て。",
      "image": "character4.png",
      "color": "#aa00ff"
    }
  ],
  "maxVotes": 100
}
```

**注意：画像パスはDB IDに合わせて`character0.png`〜`character4.png`を使用**

**ゲージの最大値：**
- ゲージは100票を100%として表示（`maxVotes = 100`）
- 100票を超える場合は100%で上限を設ける（`min(100, ...)`）

### 2. HTMLテンプレート（`tpl/infographic.php`）

**実装状況：**
- ✅ PHPの`foreach`ループで動的生成
- ✅ 各`ranking-item`に`data-character-id`、`data-rank`、`data-votes`、`data-max-votes`属性を追加
- ✅ `order`プロパティをインラインスタイルで設定
- ✅ ゲージの幅をPHPで事前計算：`style="width: <?php echo h($item['gaugeWidth']); ?>%;"`
- ✅ 公約名の背景色をインラインスタイルで設定：`style="background-color: <?php echo h($item['color']); ?>;"`
- ✅ HTMLエスケープ用ヘルパー関数（`h()`）を使用

**実装済みのHTML構造（PHPループ化）：**
```php
<div class="container">
    <div class="top-line">
        <p><img src="images/top-line.png" alt=""></p>
    </div>
    <div class="ranking" id="rankingContainer">
        <?php foreach ($ranking as $item): ?>
        <div class="ranking-item <?php echo h($item['characterClass']); ?>" 
             data-character-id="<?php echo h($item['id']); ?>"
             data-rank="<?php echo h($item['rank']); ?>"
             data-votes="<?php echo h($item['votes']); ?>"
             data-max-votes="<?php echo h($maxVotes); ?>"
             style="order: <?php echo h($item['order']); ?>;">
            <div class="pledge-item">
                <p class="rank"><?php echo h($item['rank']); ?></p>
                <p class="pledge-name" style="background-color: <?php echo h($item['color']); ?>;">
                    <?php echo h($item['pledgeShort']); ?>
                </p>
                <img src="images/<?php echo h($item['image']); ?>" alt="キャラクターの画像">
                <div class="text">
                    <p class="catch-copy"><?php echo h($item['catchCopy']); ?></p>
                    <p class="name"><?php echo h($item['name']); ?><span>(<?php echo h($item['age']); ?>)</span></p>
                </div>
            </div>
            <div class="gauge">
                <div class="sector">
                    <div class="now-gauge" style="width: <?php echo h($item['gaugeWidth']); ?>%; background-color: <?php echo h($item['color']); ?>;"></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="bottom-logo">
        <img src="images/title_logo.svg" alt="ミライレのロゴ">
    </div>
</div>
<script src="js/infographic.js"></script>
```

### 3. JavaScript（`infographic.js`）

**実装状況：**
- ✅ 定数定義（`UPDATE_INTERVAL = 5000`、`ANIMATION_DURATION = 1000`など）
- ✅ エラーハンドリング（静かに失敗）
- ✅ `DocumentFragment`を使用したパフォーマンス改善
- ✅ `Map`を使用した効率的なデータ管理

**処理フロー：**
1. ✅ **初期化**：DOMから現在のランキング状態を取得
2. ✅ **定期的な更新**：`setInterval`で5秒ごとに`infographic.php`にAJAXリクエスト
3. ✅ **データ取得**：`fetch`でJSONを取得・パース
4. ✅ **順位変更の検出**：
   - `Map`を使用して効率的に比較
   - 現在のDOMの`data-rank`と新しいデータの`rank`を比較
5. ✅ **アニメーション処理**：
   - 順位が変わった場合：
     - `transform: translateY()`でアニメーション（1秒固定）
     - 各要素の現在位置を`getBoundingClientRect()`で取得
     - 新しい位置を計算して移動
     - アニメーション完了後に`order`プロパティを更新して`transform`をリセット
     - `DocumentFragment`を使用してDOM操作を最適化
   - 順位が変わらない場合：
     - 投票数とゲージ幅のみ更新
6. ✅ **内容の更新**：
   - 順位テキスト（`p.rank`）
   - 投票数（`span`内のテキスト）
   - ゲージ幅（`div.now-gauge`の`width`）
   - データ属性（`data-rank`、`data-votes`、`data-max-votes`）

**実装済みのアニメーション：**
- アニメーション時間：1秒固定（自然な速度）
- 移動距離に応じた計算：`getBoundingClientRect()`で現在位置を取得
- `transform: translateY()`で移動
- CSSトランジション：`transform 1s ease-in-out`
- アニメーション完了後に`order`プロパティを更新してDOMを再構築
- `DocumentFragment`を使用してパフォーマンスを最適化

### 4. CSS（`infographic.scss`）

**実装状況：**
1. ✅ **グリッドレイアウト**：
   - `div.container`：3段グリッド（`top-line`、`ranking`、`bottom-logo`）
   - `div.ranking`：5段グリッド（`repeat(5, 1fr)`）
   - ✅ `order`プロパティで順序を制御（初期値は現在の順位）

2. ✅ **色の指定方法**：
   - クラス名（`item-penji`など）でCSS指定（フォールバック）
   - インラインスタイルで直接指定（PHPで設定、優先）
   - 公約名の背景色とゲージの色をインラインスタイルで設定

3. ✅ **レイアウトの調整**：
   - `overflow-x: hidden; overflow-y: visible;`でシャドウが切れないように設定
   - `padding: 0 3% 1rem;`で左右の余白とシャドウ分の下部余白を確保
   - `box-sizing: border-box;`でサイズ計算を統一

4. ✅ **アニメーション用のスタイル**：
   ```scss
   div.ranking.ranking-updating {
       position: relative;
   }
   
   div.ranking-item {
       transition: transform 1s ease-in-out;
       position: relative;
   }
   
   div.gauge div.sector div.now-gauge {
       transition: width 0.5s ease-in-out, background-color 0.5s ease-in-out;
   }
   ```

## データ構造

### キャラクター情報（PHPで定義）

```php
$characterData = [
    0 => [
        'name' => '雪原ペン次',
        'age' => 40,
        'pledgeShort' => 'コンビニ',
        'catchCopy' => 'ハッピーが一番',
        'image' => 'character0.png',  // DB IDに合わせて0から開始
        'color' => '#0000ff'
    ],
    1 => [
        'name' => '虹宮ニョロ明',
        'age' => 32,
        'pledgeShort' => '落書き',
        'catchCopy' => '自由が一番じゃん！',
        'image' => 'character1.png',
        'color' => '#ff0000'
    ],
    2 => [
        'name' => '鳥谷コケ蔵',
        'age' => 35,
        'pledgeShort' => 'ビアガーデン',
        'catchCopy' => '校則？平和ならいい。',
        'image' => 'character2.png',
        'color' => '#fbff00'
    ],
    3 => [
        'name' => '犬山イチ郎',
        'age' => 36,
        'pledgeShort' => '喫煙所',
        'catchCopy' => '息抜きが必要でしょ？',
        'image' => 'character3.png',
        'color' => '#3bf53b'
    ],
    4 => [
        'name' => '猫川ニャミ子',
        'age' => 28,
        'pledgeShort' => 'ゲーミング',
        'catchCopy' => 'やるからには、勝て。',
        'image' => 'character4.png',
        'color' => '#aa00ff',
        'characterClass' => 'item-nyamiko'
    ]
];
```

**注意：画像パスはDB ID（0〜4）に合わせて`character0.png`〜`character4.png`を使用**

## 重要なポイント

1. **HTMLはキャラクターごとに固有の要素として作成**
   - 順位は`data-rank`属性や`p.rank`テキストで表現
   - 順位が変わっても、HTML要素自体はキャラクターに紐づいている

2. **色はキャラクター固有**
   - `data-character-id`属性を使ってCSSで指定
   - またはインラインスタイルで直接指定

3. **レイアウトの動的変更（改善案）**
   - **現在の問題点**：`grid-area`を動的に変更するのは複雑で一般的ではない
   - **推奨方法**：`order`プロパティを使用
     - グリッドレイアウトでもFlexboxでも`order`プロパティは有効
     - JavaScriptで`element.style.order = index + 1;`のように設定するだけ
     - よりシンプルで一般的な実装方法
   - **代替案**：Flexboxに変更して`order`プロパティを使用（より柔軟）

4. **アニメーション**
   - `transform: translateY()`で位置を移動
   - CSSトランジションでスムーズにアニメーション
   - アニメーション完了後にDOMを再構築

5. **パフォーマンス**
   - 5秒ごとの更新は適切
   - DOM操作を最小限に
   - アニメーション中は更新をスキップ

## 実装の順序

### 実装完了状況
- ✅ HTMLテンプレートの基本構造（PHPループ化）
- ✅ CSSの基本レイアウト（グリッド、色指定、アニメーション）
- ✅ `data-character-id`、`data-rank`、`data-votes`、`data-max-votes`属性の追加
- ✅ `top-line`の追加
- ✅ シャドウ表示の修正（`overflow-y: visible`）
- ✅ **PHP側の実装**（データ取得、ランキング計算、JSON返却、エラーハンドリング）
- ✅ **HTMLテンプレートの修正**（PHPループ化、データ属性追加、ヘルパー関数使用）
- ✅ **JavaScriptの実装**（AJAX、順位変更検出、アニメーション、`order`プロパティの変更）
- ✅ **CSSの追加**（アニメーション用スタイル、`order`プロパティのサポート）
- ✅ **レイアウトの動的変更**（JavaScriptで`order`プロパティを変更）

### 実装の詳細

**PHP側：**
- ✅ データベース接続とエラーハンドリング
- ✅ キャラクター情報の定義（クラス名も含む）
- ✅ 投票数の取得とソート（降順、同票はID順）
- ✅ 順位計算（同順位対応：「1位」「1位」「3位」形式）
- ✅ ゲージの最大値を100票に固定
- ✅ 表示用の計算値（`order`、`gaugeWidth`）を事前計算
- ✅ AJAXリクエスト判定とJSON返却
- ✅ HTMLエスケープ用ヘルパー関数（`h()`）

**JavaScript側：**
- ✅ 定数定義（更新間隔、アニメーション時間など）
- ✅ 5秒ごとの自動更新
- ✅ 順位変更の検出（`Map`を使用）
- ✅ アニメーション実装（1秒固定、`transform: translateY()`）
- ✅ `order`プロパティによる順序変更
- ✅ `DocumentFragment`を使用したパフォーマンス改善
- ✅ エラーは静かに処理

**CSS側：**
- ✅ アニメーション用のトランジション
- ✅ `order`プロパティのサポート

### 実装方法の選択

**ポーリングについて：**
- ✅ `setInterval`で5秒ごとに更新（実装済み）
- ✅ サーバー負荷とリアルタイム性のバランスを考慮

**レイアウトの動的変更：**
- ✅ `order`プロパティを使用（実装済み）
- ✅ グリッドレイアウトでも`order`プロパティは有効
- ✅ アニメーションは`transform: translateY()`で実装

**ゲージの表示：**
- ✅ 100票を100%として表示（固定）
- ✅ 100票を超える場合は100%で上限を設ける

## 実装方法の詳細

### ポーリング（定期的な更新）

**実装済み：**
- ✅ `setInterval`で5秒ごとに更新
- ✅ アニメーション中は更新をスキップ（`isAnimating`フラグ）
- ✅ エラーは静かに処理（`console.log`なし）

### グリッドレイアウトの動的変更

**実装済み：**
- ✅ グリッドレイアウト + `order`プロパティを使用
- ✅ `grid-area`は使用せず、`order`プロパティで順序を制御
- ✅ アニメーションは`transform: translateY()`で実装
- ✅ `DocumentFragment`を使用してDOM操作を最適化

**実装方法：**
```javascript
// orderプロパティで順序を変更（実装済み）
function rebuildRanking(newRanking) {
    // DocumentFragmentを使用してパフォーマンスを最適化
    const fragment = document.createDocumentFragment();
    
    newRanking.forEach(function(newItem, index) {
        const element = existingElements.get(newItem.characterId);
        if (element) {
            element.style.order = index + 1; // 1, 2, 3, 4, 5
            fragment.appendChild(element);
        }
    });
    
    rankingContainer.appendChild(fragment);
}
```

**実装の利点：**
- ✅ シンプルで理解しやすい
- ✅ 一般的な実装方法
- ✅ アニメーションとの相性が良い
- ✅ パフォーマンスを最適化

## コードの改善内容

### PHP側の改善
- ✅ データ構造の統合（`$characterClassMap`を`$characterData`に統合）
- ✅ エラーハンドリングの改善（`sendErrorResponse()`関数）
- ✅ ロジックの簡素化（`array_merge()`、`max()`の使用）
- ✅ HTMLエスケープ用ヘルパー関数（`h()`）

### JavaScript側の改善
- ✅ 定数の整理（マジックナンバーを定数化）
- ✅ パフォーマンス改善（`Number()`、`DocumentFragment`の使用）
- ✅ コードの整理（`Map`を使用、`init()`関数に集約）

### テンプレート側の改善
- ✅ `h()`関数を使用してコードを簡潔化
- ✅ 不要な変数を削除
