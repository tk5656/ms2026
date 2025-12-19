# インフォグラフィックページ実装方針

## 基本方針

### HTMLの構造について

**重要な理解：**
- HTMLの各`ranking-item`は**キャラクターごとに固有の要素**として作成する
- 順位（1位、2位など）はHTMLの構造ではなく、**データ属性やテキストコンテンツ**として表現する
- 順位が変わった場合、JavaScriptで**DOMの順序を変更**するだけで対応する

**現在の実装状況：**
- 現在：静的HTMLで各キャラクターを記述（`item-penji`、`item-nyoroaki`など）
- `data-character-id`属性は既に追加済み（0〜4）
- 将来的に：PHPの`foreach`ループで動的に生成し、`data-rank`、`data-votes`属性も追加

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
1. データベースから`characters`テーブルの`id`と`current_vote`を取得
2. キャラクター情報（名前、年齢、公約、キャッチコピー、画像、色）を配列で定義
3. 投票数でソート（降順、同票はID順）
4. 順位を計算（同順位対応）
5. 最大投票数を計算（ゲージの100%基準）

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
  "maxVotes": 40
}
```

**注意：画像パスはDB IDに合わせて`character0.png`〜`character4.png`を使用**

### 2. HTMLテンプレート（`tpl/infographic.php`）

**変更点：**
- 静的HTML（item1〜item5）をPHPの`foreach`ループに変更
- 各`ranking-item`に`data-character-id`、`data-rank`、`data-votes`属性を追加
- ゲージの幅をPHPで動的に設定：`style="width: <?php echo ($item['votes'] / $maxVotes) * 100; ?>%;"`
- 公約名の背景色をインラインスタイルで設定：`style="background-color: <?php echo $item['color']; ?>;"`

**現在のHTML構造（静的）：**
```html
<div class="container">
    <div class="top-line">
        <p><img src="images/top-line.png" alt=""></p>
    </div>
    <div class="ranking">
        <div class="ranking-item item-penji" data-character-id="0">
            <!-- 内容 -->
        </div>
        <!-- 他の4キャラクター -->
    </div>
    <div class="bottom-logo">
        <img src="images/title_logo.svg" alt="ミライレのロゴ">
    </div>
</div>
<script src="js/infographic.js"></script>
```

**将来的なHTML構造（PHPループ化後）：**
```php
<div class="container">
    <div class="top-line">
        <p><img src="images/top-line.png" alt=""></p>
    </div>
    <div class="ranking" id="rankingContainer">
        <?php foreach ($ranking as $item): ?>
        <div class="ranking-item" 
             data-character-id="<?php echo $item['id']; ?>"
             data-rank="<?php echo $item['rank']; ?>"
             data-votes="<?php echo $item['votes']; ?>"
             data-max-votes="<?php echo $maxVotes; ?>">
            <div class="pledge-item">
                <p class="rank"><?php echo $item['rank']; ?></p>
                <p class="pledge-name" style="background-color: <?php echo $item['color']; ?>;">
                    <?php echo $item['pledgeShort']; ?>
                </p>
                <img src="images/<?php echo $item['image']; ?>" alt="キャラクターの画像">
                <div class="text">
                    <p class="catch-copy"><?php echo $item['catchCopy']; ?></p>
                    <p class="name"><?php echo $item['name']; ?><span>(<?php echo $item['age']; ?>)</span></p>
                </div>
            </div>
            <div class="gauge">
                <div class="sector">
                    <div class="now-gauge" style="width: <?php echo ($item['votes'] / $maxVotes) * 100; ?>%;"></div>
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

**処理フロー：**
1. **初期化**：DOMから現在のランキング状態を取得
2. **定期的な更新**：`setInterval`で数秒ごと（例：5秒）に`infographic.php`にAJAXリクエスト
3. **データ取得**：`fetch`でJSONを取得・パース
4. **順位変更の検出**：
   - 現在のDOMの`data-rank`と新しいデータの`rank`を比較
   - 順位が変わったキャラクターを特定
5. **アニメーション処理**：
   - 順位が変わった場合：
     - **方法1（推奨）**：`order`プロパティを変更 + `transform: translateY()`でアニメーション
       - 各要素の現在位置を`getBoundingClientRect()`で取得
       - 新しい位置を計算
       - `transform: translateY()`で移動
       - CSSトランジションでアニメーション（例：0.5秒）
       - アニメーション完了後に`order`プロパティを更新して`transform`をリセット
     - **方法2**：`order`プロパティのみ変更（アニメーションなし、シンプル）
   - 順位が変わらない場合：
     - 投票数とゲージ幅のみ更新
6. **内容の更新**：
   - 順位テキスト（`p.rank`）
   - 投票数（`span`内のテキスト）
   - ゲージ幅（`div.now-gauge`の`width`）
   - データ属性（`data-rank`、`data-votes`）

**アニメーション実装の詳細（推奨方法）：**
```javascript
// 1. 現在の位置を取得
const currentPositions = new Map();
rankingItems.forEach((item) => {
    const characterId = parseInt(item.dataset.characterId);
    currentPositions.set(characterId, {
        element: item,
        top: item.getBoundingClientRect().top
    });
});

// 2. 新しい順序で位置を計算してアニメーション
let newTop = rankingContainer.getBoundingClientRect().top;
const gap = 16; // gap: 1rem = 16px

newRanking.forEach((newItem, index) => {
    const current = currentPositions.get(newItem.id);
    if (current) {
        const currentTop = current.top;
        const moveDistance = newTop - currentTop;
        
        if (Math.abs(moveDistance) > 1) { // 1px以上の移動がある場合
            current.element.style.transform = `translateY(${moveDistance}px)`;
            current.element.style.transition = 'transform 0.5s ease-in-out';
        }
        
        newTop += current.element.offsetHeight + gap;
    }
});

// 3. アニメーション完了後にorderプロパティを更新してtransformをリセット
setTimeout(() => {
    newRanking.forEach((item, index) => {
        const element = document.querySelector(`[data-character-id="${item.id}"]`);
        if (element) {
            element.style.order = index + 1;
            element.style.transform = '';
            element.style.transition = '';
        }
    });
}, 500);
```

**シンプルな方法（アニメーションなし）：**
```javascript
// orderプロパティのみ変更（アニメーションなし）
newRanking.forEach((item, index) => {
    const element = document.querySelector(`[data-character-id="${item.id}"]`);
    if (element) {
        element.style.order = index + 1;
    }
});
```

### 4. CSS（`infographic.scss`）

**現在の実装状況：**
1. **グリッドレイアウト**：
   - 固定の5つのグリッドエリア（`item1`〜`item5`）を使用
   - 各キャラクターにクラス名（`item-penji`、`item-nyoroaki`など）で`grid-area`を割り当て
   - **改善案**：`grid-area`の代わりに`order`プロパティを使用（よりシンプル）

2. **色の指定方法**：
   - 現在：クラス名（`item-penji`など）で指定
   ```scss
   div.ranking-item.item-penji {
       p.pledge-name {
           background-color: $penjiColor; // 雪原ペン次
       }
       div.now-gauge {
           background-color: $penjiColor;
       }
   }
   ```
   - 将来的には：`data-character-id`属性で指定するか、インラインスタイルで直接指定（PHPで設定）

3. **レイアウトの調整**：
   - `div.container`：3段グリッド（`top-line`、`ranking`、`bottom-logo`）
   - `div.ranking`：5段グリッド（`item1`〜`item5`）
   - `overflow-x: hidden; overflow-y: visible;`でシャドウが切れないように設定
   - `padding: 0 3% 1rem;`で左右の余白とシャドウ分の下部余白を確保

4. **アニメーション用のスタイル（未実装）**：
   ```scss
   div.ranking-item {
       transition: transform 0.5s ease-in-out;
       position: relative;
   }
   
   div.ranking.ranking-updating {
       position: relative;
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
        'color' => '#aa00ff'
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

### 現在の実装状況
- ✅ HTMLテンプレートの基本構造（静的）
- ✅ CSSの基本レイアウト（グリッド、色指定）
- ✅ `data-character-id`属性の追加
- ✅ `top-line`の追加
- ✅ シャドウ表示の修正（`overflow-y: visible`）

### 今後の実装タスク
1. **PHP側の実装**（データ取得、ランキング計算、JSON返却）
2. **HTMLテンプレートの修正**（PHPループ化、`data-rank`、`data-votes`属性追加）
3. **JavaScriptの実装**（AJAX、順位変更検出、アニメーション、`order`プロパティの変更）
4. **CSSの追加**（アニメーション用スタイル、`order`プロパティのサポート）
5. **レイアウトの動的変更**（JavaScriptで`order`プロパティを変更）

### 実装方法の選択

**ポーリングについて：**
- ✅ PHPとJavaScript（フレームワークなし）なので、`setInterval`で定期的に更新するのが唯一の選択肢
- 5秒ごとの更新が適切（サーバー負荷とリアルタイム性のバランス）

**レイアウトの動的変更：**
- ❌ `grid-area`を動的に変更する方法は複雑で一般的ではない
- ✅ `order`プロパティを使用する方法が推奨（シンプルで一般的）

## 実装方法の見直し

### ポーリング（定期的な更新）について

**確認：PHPとJavaScript（フレームワークなし）での実装**
- ✅ **ポーリング（`setInterval`）が唯一の選択肢です**
- WebSocketやServer-Sent Eventsは使用しないため、数秒ごとにAJAXリクエストを送信する方法が一般的で合理的
- 5秒ごとの更新が適切（サーバー負荷とリアルタイム性のバランス）

### グリッドレイアウトの動的変更について（問題点と改善案）

**現在の方針の問題点：**
- `grid-area`を動的に変更するのは**複雑で一般的ではない**
- グリッドレイアウトとアニメーションの組み合わせが複雑

**改善案1：Flexbox + `order`プロパティ（推奨）**
- グリッドレイアウトをFlexboxに変更
- `order`プロパティで順序を制御（よりシンプルで一般的）
- アニメーションも`transform: translateY()`で実装可能

**改善案2：グリッドレイアウト + `order`プロパティ**
- 現在のグリッドレイアウトを維持
- `grid-area`の代わりに`order`プロパティを使用
- グリッドレイアウトでも`order`プロパティは有効

**推奨実装方法：**
```javascript
// orderプロパティで順序を変更（シンプル）
function updateRankingOrder(newRanking) {
    newRanking.forEach((item, index) => {
        const element = document.querySelector(`[data-character-id="${item.id}"]`);
        if (element) {
            element.style.order = index + 1; // 1, 2, 3, 4, 5
        }
    });
}
```

この方法の方が：
- シンプルで理解しやすい
- 一般的な実装方法
- アニメーションとの相性が良い
