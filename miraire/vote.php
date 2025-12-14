<?php
// データベース接続設定を読み込む
require_once './config.php';

// JSONリクエストかどうかを判定
$isAjax = !empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;

// JSONデータを受け取る
$characterNum = null;
if ($isAjax) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $characterNum = (int)$data['characterNum'];
}

// 投票処理
if ($isAjax && $characterNum !== null) {
    
    // データベースに接続
    $link = mysqli_connect(HOST, USER, PASSWORD, DB);
    mysqli_set_charset($link, "utf8");
    
    // SQL文①: 現在の票数と段階を取得
    $sql1 = "SELECT current_vote_count, current_stage FROM characters WHERE character_id = " . $characterNum . ";";
    $result1 = mysqli_query($link, $sql1);
    $character = mysqli_fetch_assoc($result1);
    
    $currentVoteCount = $character['current_vote_count'];
    $currentStage = $character['current_stage'];
    
    // 票数を1増やす
    $newVoteCount = $currentVoteCount + 1;
    
    // SQL文②: 新しい段階を計算（票数に応じて）
    $sql2 = "SELECT MAX(stage_number) as max_stage FROM isometric_stages WHERE character_id = " . $characterNum . " AND required_vote_count <= " . $newVoteCount . ";";
    $result2 = mysqli_query($link, $sql2);
    $stageResult = mysqli_fetch_assoc($result2);
    $newStage = $stageResult['max_stage'] ?? $currentStage;
    
    // SQL文③: キャラクター情報を更新
    $sql3 = "UPDATE characters SET current_vote_count = " . $newVoteCount . ", current_stage = " . $newStage . " WHERE character_id = " . $characterNum . ";";
    mysqli_query($link, $sql3);
    
    // SQL文④: 投票履歴を記録
    $sql4 = "INSERT INTO votes (character_id, stage_at_vote, vote_count_before, vote_count_after, stage_before, stage_after) VALUES (" . $characterNum . ", " . $currentStage . ", " . $currentVoteCount . ", " . $newVoteCount . ", " . $currentStage . ", " . $newStage . ");";
    mysqli_query($link, $sql4);
    
    // SQL文⑤: 画像パスを取得
    $sql5 = "SELECT isometric_image_path FROM isometric_stages WHERE character_id = " . $characterNum . " AND stage_number = " . $newStage . ";";
    $result5 = mysqli_query($link, $sql5);
    $imageResult = mysqli_fetch_assoc($result5);
    $imagePath = $imageResult['isometric_image_path'] ?? "./images/vote/vote_complete_" . $characterNum . ".png";
    
    // データベース接続を切断
    mysqli_close($link);
    
    // JSON形式で結果を返す
    header('Content-Type: application/json');
    echo json_encode([
        'imagePath' => $imagePath
    ]);
    exit;
    
} else {
    // 通常のページアクセスの場合、投票ページを表示
    require_once "./tpl/vote.php";
}
?>