<?php
error_reporting(E_ALL);
ini_set("display_errors", "0");

require_once __DIR__ . "/config.php";

function send_json_error($message) {
    echo json_encode(["error" => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

// POSTリクエストの場合（投票処理）
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    // キャラクター番号のチェック
    $character_num = isset($data["character_num"]) ? (int)$data["character_num"] : null;
    if ($character_num === null || $character_num < 0 || $character_num > 4) {
        send_json_error("キャラクター番号が不正です");
    }

    // データベース接続
    $link = mysqli_connect(HOST, USER, PASSWORD, DB);
    if (!$link) {
        send_json_error("DB接続エラー");
    }
    mysqli_set_charset($link, "utf8mb4");

    // 現在の票数を取得
    $sql1 = "SELECT current_vote FROM characters WHERE id = " . $character_num;
    $res1 = mysqli_query($link, $sql1);
    $row1 = $res1 ? mysqli_fetch_assoc($res1) : null;
    if (!$row1) {
        mysqli_close($link);
        send_json_error("キャラクターが見つかりません");
    }
    $current_vote_count = (int)$row1["current_vote"];
    $new_vote_count = $current_vote_count + 1;

    // 現在の段階を取得
    $sql2 = "SELECT MAX(stage_number) AS max_stage
             FROM isometric
             WHERE character_id = " . $character_num . " AND stage_number <= " . $current_vote_count;
    $res2 = mysqli_query($link, $sql2);
    $row2 = $res2 ? mysqli_fetch_assoc($res2) : null;
    $current_stage = isset($row2["max_stage"]) ? (int)$row2["max_stage"] : 0;

    // 新しい段階を取得
    $sql3 = "SELECT MAX(stage_number) AS max_stage
             FROM isometric
             WHERE character_id = " . $character_num . " AND stage_number <= " . $new_vote_count;
    $res3 = mysqli_query($link, $sql3);
    $row3 = $res3 ? mysqli_fetch_assoc($res3) : null;
    $new_stage = isset($row3["max_stage"]) ? (int)$row3["max_stage"] : 0;

    // 票数を更新
    $sql4 = "UPDATE characters SET current_vote = " . $new_vote_count . " WHERE id = " . $character_num;
    mysqli_query($link, $sql4);

    // 投票履歴を記録
    $sql5 = "INSERT INTO votes (character_id) VALUES (" . $character_num . ")";
    mysqli_query($link, $sql5);

    // 投票前の画像パスを取得
    $before_image_path = "./images/vote/vote_complete_" . $character_num . ".png";
    $sql6 = "SELECT isometric_path
             FROM isometric
             WHERE character_id = " . $character_num . " AND stage_number = " . $current_stage;
    $res6 = mysqli_query($link, $sql6);
    if ($res6 && ($row6 = mysqli_fetch_assoc($res6))) {
        $before_image_path = $row6["isometric_path"];
    }

    // 投票後の画像パスを取得
    $after_image_path = "./images/vote/vote_complete_" . $character_num . ".png";
    $sql7 = "SELECT isometric_path
             FROM isometric
             WHERE character_id = " . $character_num . " AND stage_number = " . $new_stage;
    $res7 = mysqli_query($link, $sql7);
    if ($res7 && ($row7 = mysqli_fetch_assoc($res7))) {
        $after_image_path = $row7["isometric_path"];
    }

    // パーツ画像パスを取得（段階が上がった場合のみ）
    $parts_path = null;
    if ($new_stage > $current_stage) {
        $sql8 = "SELECT parts_path
                 FROM isometric
                 WHERE character_id = " . $character_num . " AND stage_number = " . $new_stage;
        $res8 = mysqli_query($link, $sql8);
        if ($res8 && ($row8 = mysqli_fetch_assoc($res8))) {
            $parts_path = $row8["parts_path"];
        }
    }

    mysqli_close($link);

    // 画像パスの調整
    if (substr($before_image_path, 0, 10) === "isometric/") {
        $before_image_path = "../" . $before_image_path;
    }
    if (substr($after_image_path, 0, 10) === "isometric/") {
        $after_image_path = "../" . $after_image_path;
    }
    if ($parts_path && substr($parts_path, 0, 10) === "isometric/") {
        $parts_path = "../" . $parts_path;
    }

    // JSON形式で結果を返す
    echo json_encode([
        "before_image_path" => $before_image_path,
        "after_image_path"  => $after_image_path,
        "parts_path"        => $parts_path,
        "current_stage"     => $current_stage,
        "new_stage"         => $new_stage
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// 通常アクセスの場合はHTMLテンプレートを読み込む
require_once __DIR__ . "/tpl/vote.php";
?>