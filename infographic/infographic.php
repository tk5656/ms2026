<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once __DIR__ . '/../miraire/config.php';

// AJAXリクエスト判定
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// エラーレスポンス送信関数
function sendErrorResponse($message) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

// キャラクター情報の定義（クラス名も含む）
$characterData = [
    0 => [
        'name' => '雪原ペン次',
        'age' => 40,
        'pledgeShort' => 'コンビニ',
        'catchCopy' => 'ハッピーが一番',
        'image' => 'character0.png',
        'color' => '#0000ff',
        'characterClass' => 'item-penji'
    ],
    1 => [
        'name' => '虹宮ニョロ明',
        'age' => 32,
        'pledgeShort' => '落書き',
        'catchCopy' => '自由が一番じゃん！',
        'image' => 'character1.png',
        'color' => '#ff0000',
        'characterClass' => 'item-nyoroaki'
    ],
    2 => [
        'name' => '鳥谷コケ蔵',
        'age' => 35,
        'pledgeShort' => 'ビアガーデン',
        'catchCopy' => '校則？平和ならいい。',
        'image' => 'character2.png',
        'color' => '#fbff00',
        'characterClass' => 'item-kokezo'
    ],
    3 => [
        'name' => '犬山イチ郎',
        'age' => 36,
        'pledgeShort' => '喫煙所',
        'catchCopy' => '息抜きが必要でしょ？',
        'image' => 'character3.png',
        'color' => '#3bf53b',
        'characterClass' => 'item-ichiro'
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

// データベース接続
$link = mysqli_connect(HOST, USER, PASSWORD, DB);
if (!$link) {
    if ($isAjax) {
        sendErrorResponse('データベース接続に失敗しました');
    }
    die('データベース接続に失敗しました');
}
mysqli_set_charset($link, "utf8mb4");

// キャラクターの投票数を取得
$sql = "SELECT id, current_vote FROM characters ORDER BY id ASC";
$result = mysqli_query($link, $sql);
if (!$result) {
    mysqli_close($link);
    if ($isAjax) {
        sendErrorResponse('データ取得に失敗しました');
    }
    die('データ取得に失敗しました');
}

// キャラクターデータと投票数を結合
$characters = [];
while ($row = mysqli_fetch_assoc($result)) {
    $id = (int)$row['id'];
    if (isset($characterData[$id])) {
        $characters[] = array_merge(
            $characterData[$id],
            [
                'id' => $id,
                'votes' => (int)$row['current_vote']
            ]
        );
    }
}
mysqli_close($link);

// 投票数でソート（降順、同票はID順）
usort($characters, function($a, $b) {
    if ($a['votes'] === $b['votes']) {
        return $a['id'] - $b['id'];
    }
    return $b['votes'] - $a['votes'];
});

// ランキング計算（同順位対応：「1位」「1位」「3位」形式）
$ranking = [];
$currentRank = 1;
$prevVotes = null;

foreach ($characters as $index => $character) {
    if ($prevVotes !== null && $character['votes'] < $prevVotes) {
        $currentRank = $index + 1;
    }
    
    $ranking[] = [
        'rank' => $currentRank,
        'id' => $character['id'],
        'votes' => $character['votes'],
        'name' => $character['name'],
        'age' => $character['age'],
        'pledgeShort' => $character['pledgeShort'],
        'catchCopy' => $character['catchCopy'],
        'image' => $character['image'],
        'color' => $character['color'],
        'characterClass' => $character['characterClass']
    ];
    
    $prevVotes = $character['votes'];
}

// ゲージの最大値（100票を100%として表示）
$maxVotes = 100;

// 表示用の計算値を追加
foreach ($ranking as $index => &$item) {
    $item['order'] = $index + 1;
    // ゲージ幅は100票を基準に計算（100票を超える場合は100%を超える）
    $item['gaugeWidth'] = min(100, ($item['votes'] / $maxVotes) * 100);
}
unset($item);

// AJAXリクエストの場合はJSONを返す
if ($isAjax) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'ranking' => $ranking,
        'maxVotes' => $maxVotes
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// HTMLエスケープ用のヘルパー関数
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// 通常リクエストの場合はHTMLテンプレートを読み込む
require_once __DIR__ . "/tpl/infographic.php";
?>
