<?php
error_reporting(E_ALL);
ini_set("display_errors", "0");

require_once __DIR__ . "/../miraire/config.php";

// AJAXリクエストかどうかを判定
$is_ajax = !empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && 
          strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest";

function send_error_response($message) {
    echo json_encode(["error" => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

// キャラクター情報の定義（クラス名も含む）
$character_data = [
    0 => [
        "name" => "雪原ペン次",
        "age" => 40,
        "pledge_short" => "コンビニ",
        "catch_copy" => "ハッピーが一番",
        "image" => "character0.png",
        "color" => "#0000ff",
        "character_class" => "item-penji"
    ],
    1 => [
        "name" => "虹宮ニョロ明",
        "age" => 32,
        "pledge_short" => "落書き",
        "catch_copy" => "自由が一番じゃん！",
        "image" => "character1.png",
        "color" => "#ff0000",
        "character_class" => "item-nyoroaki"
    ],
    2 => [
        "name" => "鳥谷コケ蔵",
        "age" => 35,
        "pledge_short" => "ビアガーデン",
        "catch_copy" => "校則？平和ならいい。",
        "image" => "character2.png",
        "color" => "#fbff00",
        "character_class" => "item-kokezo"
    ],
    3 => [
        "name" => "犬山イチ郎",
        "age" => 36,
        "pledge_short" => "喫煙所",
        "catch_copy" => "息抜きが必要でしょ？",
        "image" => "character3.png",
        "color" => "#3bf53b",
        "character_class" => "item-ichiro"
    ],
    4 => [
        "name" => "猫川ニャミ子",
        "age" => 28,
        "pledge_short" => "ゲーミング",
        "catch_copy" => "やるからには、勝て。",
        "image" => "character4.png",
        "color" => "#aa00ff",
        "character_class" => "item-nyamiko"
    ]
];

// データベース接続
$link = mysqli_connect(HOST, USER, PASSWORD, DB);
if (!$link) {
    if ($is_ajax) {
        send_error_response("データベース接続に失敗しました");
    }
    die("データベース接続に失敗しました");
}
mysqli_set_charset($link, "utf8mb4");

// 投票数を取得（投票数の多い順、同票はID順でソート）
$sql = "SELECT id, current_vote FROM characters ORDER BY current_vote DESC, id ASC";
$result = mysqli_query($link, $sql);
if (!$result) {
    mysqli_close($link);
    if ($is_ajax) {
        send_error_response("データ取得に失敗しました");
    }
    die("データ取得に失敗しました");
}

// キャラクター情報と投票数を結合
$characters = [];
while ($row = mysqli_fetch_assoc($result)) {
    $id = (int)$row["id"];
    if (isset($character_data[$id])) {
        $characters[] = [
            "id" => $id,
            "name" => $character_data[$id]["name"],
            "age" => $character_data[$id]["age"],
            "pledge_short" => $character_data[$id]["pledge_short"],
            "catch_copy" => $character_data[$id]["catch_copy"],
            "image" => $character_data[$id]["image"],
            "color" => $character_data[$id]["color"],
            "character_class" => $character_data[$id]["character_class"],
            "votes" => (int)$row["current_vote"]
        ];
    }
}
mysqli_close($link);

// 投票数の多い順にソート（同票はID順）
// SQLでソート済みのため、PHP側でのソートは不要

// 順位を計算（同票は同じ順位）
$ranking = [];
$current_rank = 1;
$prev_votes = null;

foreach ($characters as $index => $character) {
    if ($prev_votes !== null && $character["votes"] < $prev_votes) {
        $current_rank = $index + 1;
    }
    
    $ranking[] = [
        "rank" => $current_rank,
        "id" => $character["id"],
        "votes" => $character["votes"],
        "name" => $character["name"],
        "age" => $character["age"],
        "pledge_short" => $character["pledge_short"],
        "catch_copy" => $character["catch_copy"],
        "image" => $character["image"],
        "color" => $character["color"],
        "character_class" => $character["character_class"]
    ];
    
    $prev_votes = $character["votes"];
}

// ゲージの最大値を100票に設定
$max_votes = 100;

// 表示用の値を計算
foreach ($ranking as $index => $item) {
    $ranking[$index]["order"] = $index + 1;
    $ranking[$index]["gauge_width"] = min(100, ($item["votes"] / $max_votes) * 100);
}

// AJAXリクエストの場合はJSONを返す
if ($is_ajax) {
    echo json_encode([
        "ranking" => $ranking,
        "max_votes" => $max_votes
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, "UTF-8");
}

// 通常アクセスの場合はHTMLテンプレートを読み込む
require_once __DIR__ . "/tpl/infographic.php";
?>
