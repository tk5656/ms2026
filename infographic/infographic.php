<?php
// エラー表示の制御
error_reporting(E_ALL);
ini_set('display_errors', '0');

// データベース接続設定を読み込む
require_once __DIR__ . '/../miraire/config.php';

// AJAXリクエストかどうかを判定（データベース接続前に判定）
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// キャラクター情報の定義
$characterData = [
    0 => [
        'name' => '雪原ペン次',
        'pledge' => 'コンビニを学内に作る',
        'pledgeShort' => 'コンビニ',
        'catchCopy' => 'ハッピーが一番',
        'image' => 'character1.png'
    ],
    1 => [
        'name' => '虹宮ニョロ明',
        'pledge' => '教室に落書きし放題',
        'pledgeShort' => '落書き',
        'catchCopy' => 'ハッピーが一番',
        'image' => 'character2.png'
    ],
    2 => [
        'name' => '鳥谷コケ蔵',
        'pledge' => '屋上にビアガーデンを作る',
        'pledgeShort' => 'ビアガーデン',
        'catchCopy' => 'ハッピーが一番',
        'image' => 'character3.png'
    ],
    3 => [
        'name' => '犬山イチ郎',
        'pledge' => '学内に屋内喫煙所を作る',
        'pledgeShort' => '喫煙所',
        'catchCopy' => 'ハッピーが一番',
        'image' => 'character4.png'
    ],
    4 => [
        'name' => '猫川ニャミ子',
        'pledge' => 'ゲーミングルームを作る',
        'pledgeShort' => 'ゲーミング',
        'catchCopy' => 'ハッピーが一番',
        'image' => 'character5.png'
    ]
];

// データベースに接続
$link = mysqli_connect(HOST, USER, PASSWORD, DB);
if (!$link) {
    if ($isAjax) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'データベース接続に失敗しました'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    die('データベース接続に失敗しました');
}
mysqli_set_charset($link, "utf8mb4");

// キャラクターの投票数を取得
$sql = "SELECT id, current_vote FROM characters ORDER BY id ASC";
$result = mysqli_query($link, $sql);
$characters = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (isset($characterData[$row['id']])) {
            $characters[$row['id']] = [
                'id' => $row['id'],
                'votes' => (int)$row['current_vote'],
                'name' => $characterData[$row['id']]['name'],
                'pledge' => $characterData[$row['id']]['pledge'],
                'pledgeShort' => $characterData[$row['id']]['pledgeShort'],
                'catchCopy' => $characterData[$row['id']]['catchCopy'],
                'image' => $characterData[$row['id']]['image']
            ];
        }
    }
}

// 投票数でソート（降順）
usort($characters, function($a, $b) {
    if ($a['votes'] == $b['votes']) {
        return $a['id'] - $b['id']; // 同票の場合はID順
    }
    return $b['votes'] - $a['votes'];
});

// ランキングを計算（同順位対応）
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
        'pledge' => $character['pledge'],
        'pledgeShort' => $character['pledgeShort'],
        'catchCopy' => $character['catchCopy'],
        'image' => $character['image']
    ];
    $prevVotes = $character['votes'];
}

// 最大投票数を取得（グラフの100%基準）
$maxVotes = 0;
if (!empty($ranking)) {
    $maxVotes = $ranking[0]['votes'];
}
if ($maxVotes == 0) {
    $maxVotes = 1; // 0除算を防ぐ
}

mysqli_close($link);

// AJAXリクエストの場合はJSONを返す
if ($isAjax) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'ranking' => $ranking,
        'maxVotes' => $maxVotes
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once "./tpl/infographic.php";
?>