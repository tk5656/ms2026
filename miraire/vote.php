<?php
// エラー表示の制御
// 本番環境（ロリポップ等）: display_errors = '0'
// 開発環境（XAMPP等）: display_errors = '1'
error_reporting(E_ALL);
// ★ローカル(XAMPP)では 1 にして詳細エラーを確認する
ini_set('display_errors', '1');

// データベース接続設定を読み込む
require_once __DIR__ . '/config.php';

// JSONリクエストかどうかを判定
// 一部の環境では CONTENT_TYPE ではなく HTTP_CONTENT_TYPE になる場合があるため両方を見る
$contentType = '';
if (!empty($_SERVER['CONTENT_TYPE'])) {
    $contentType = $_SERVER['CONTENT_TYPE'];
} elseif (!empty($_SERVER['HTTP_CONTENT_TYPE'])) {
    $contentType = $_SERVER['HTTP_CONTENT_TYPE'];
}

$isAjax = !empty($contentType) && stripos($contentType, 'application/json') !== false;

// JSONデータを受け取る
$characterNum = null;
if ($isAjax) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $characterNum = isset($data['characterNum']) ? (int)$data['characterNum'] : null;
}

// 投票処理
if ($isAjax && $characterNum !== null && $characterNum >= 0 && $characterNum <= 4) {
    
    // データベースに接続
    $link = mysqli_connect(HOST, USER, PASSWORD, DB);
    if (!$link) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'データベース接続に失敗しました'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    mysqli_set_charset($link, "utf8mb4");
    
    // SQL文①: 現在の票数を取得（プリペアドステートメントでSQLインジェクション対策）
    $sql1 = "SELECT current_vote FROM characters WHERE id = ?";
    $stmt1 = mysqli_prepare($link, $sql1);
    if (!$stmt1) {
        mysqli_close($link);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'データベースクエリの準備に失敗しました'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    mysqli_stmt_bind_param($stmt1, "i", $characterNum);
    if (!mysqli_stmt_execute($stmt1)) {
        mysqli_stmt_close($stmt1);
        mysqli_close($link);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'データベースクエリの実行に失敗しました'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $result1 = mysqli_stmt_get_result($stmt1);
    $character = mysqli_fetch_assoc($result1);
    mysqli_stmt_close($stmt1);
    
    // キャラクターが存在しない場合の処理
    if (!$character) {
        mysqli_close($link);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'キャラクターが見つかりません'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $currentVoteCount = $character['current_vote'] ?? 0;
    
    // 票数を1増やす
    $newVoteCount = $currentVoteCount + 1;
    
    // SQL文②: 現在の段階を取得（現在の票数に応じて）
    $sql2 = "SELECT MAX(stage_number) as max_stage FROM isometric WHERE character_id = ? AND stage_number <= ?";
    $stmt2 = mysqli_prepare($link, $sql2);
    if (!$stmt2) {
        mysqli_close($link);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'データベースクエリの準備に失敗しました'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    mysqli_stmt_bind_param($stmt2, "ii", $characterNum, $currentVoteCount);
    mysqli_stmt_execute($stmt2);
    $result2 = mysqli_stmt_get_result($stmt2);
    $currentStageResult = mysqli_fetch_assoc($result2);
    $currentStage = $currentStageResult['max_stage'] ?? 0;
    mysqli_stmt_close($stmt2);
    
    // SQL文③: 新しい段階を計算（票数に応じて、stage_numberが票数以下で最大のものを取得）
    $sql3 = "SELECT MAX(stage_number) as max_stage FROM isometric WHERE character_id = ? AND stage_number <= ?";
    $stmt3 = mysqli_prepare($link, $sql3);
    if (!$stmt3) {
        mysqli_close($link);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'データベースクエリの準備に失敗しました'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    mysqli_stmt_bind_param($stmt3, "ii", $characterNum, $newVoteCount);
    mysqli_stmt_execute($stmt3);
    $result3 = mysqli_stmt_get_result($stmt3);
    $stageResult = mysqli_fetch_assoc($result3);
    $newStage = $stageResult['max_stage'] ?? 0;
    mysqli_stmt_close($stmt3);
    
    // SQL文④: キャラクター情報を更新（票数のみ更新、段階は保持しない）
    $sql4 = "UPDATE characters SET current_vote = ? WHERE id = ?";
    $stmt4 = mysqli_prepare($link, $sql4);
    if (!$stmt4) {
        mysqli_close($link);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'データベースクエリの準備に失敗しました'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    mysqli_stmt_bind_param($stmt4, "ii", $newVoteCount, $characterNum);
    if (!mysqli_stmt_execute($stmt4)) {
        mysqli_stmt_close($stmt4);
        mysqli_close($link);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'データベースの更新に失敗しました'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    mysqli_stmt_close($stmt4);
    
    // SQL文⑤: 投票履歴を記録
    $sql5 = "INSERT INTO votes (character_id) VALUES (?)";
    $stmt5 = mysqli_prepare($link, $sql5);
    if (!$stmt5) {
        mysqli_close($link);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'データベースクエリの準備に失敗しました'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    mysqli_stmt_bind_param($stmt5, "i", $characterNum);
    mysqli_stmt_execute($stmt5);
    mysqli_stmt_close($stmt5);
    
    // SQL文⑥: 投票前の画像パスを取得
    $sql6 = "SELECT isometric_path FROM isometric WHERE character_id = ? AND stage_number = ?";
    $stmt6 = mysqli_prepare($link, $sql6);
    if ($stmt6) {
        mysqli_stmt_bind_param($stmt6, "ii", $characterNum, $currentStage);
        mysqli_stmt_execute($stmt6);
        $result6 = mysqli_stmt_get_result($stmt6);
        $beforeImageResult = mysqli_fetch_assoc($result6);
        mysqli_stmt_close($stmt6);
    }
    $beforeImagePath = $beforeImageResult['isometric_path'] ?? "./images/vote/vote_complete_" . $characterNum . ".png";
    // パスを調整（DBのパスはisometric/...なので、../isometric/...に変換）
    // 注意: 画像パスはブラウザから見た相対パスとして返す必要があるため、../を使用
    if (strpos($beforeImagePath, 'isometric/') === 0) {
        $beforeImagePath = '../' . $beforeImagePath;
    }
    
    // SQL文⑦: 投票後の画像パスを取得
    $sql7 = "SELECT isometric_path FROM isometric WHERE character_id = ? AND stage_number = ?";
    $stmt7 = mysqli_prepare($link, $sql7);
    if ($stmt7) {
        mysqli_stmt_bind_param($stmt7, "ii", $characterNum, $newStage);
        mysqli_stmt_execute($stmt7);
        $result7 = mysqli_stmt_get_result($stmt7);
        $afterImageResult = mysqli_fetch_assoc($result7);
        mysqli_stmt_close($stmt7);
    }
    $afterImagePath = $afterImageResult['isometric_path'] ?? "./images/vote/vote_complete_" . $characterNum . ".png";
    // パスを調整
    if (strpos($afterImagePath, 'isometric/') === 0) {
        $afterImagePath = '../' . $afterImagePath;
    }
    
    // SQL文⑧: 獲得したパーツの画像パスを取得
    $partsPath = null;
    if ($newStage > $currentStage) {
        $sql8 = "SELECT parts_path FROM isometric WHERE character_id = ? AND stage_number = ?";
        $stmt8 = mysqli_prepare($link, $sql8);
        if ($stmt8) {
            mysqli_stmt_bind_param($stmt8, "ii", $characterNum, $newStage);
            mysqli_stmt_execute($stmt8);
            $result8 = mysqli_stmt_get_result($stmt8);
            $partsResult = mysqli_fetch_assoc($result8);
            $partsPath = $partsResult['parts_path'] ?? null;
            mysqli_stmt_close($stmt8);
        }
        // パスを調整
        if ($partsPath && strpos($partsPath, 'isometric/') === 0) {
            $partsPath = '../' . $partsPath;
        }
    }
    
    // データベース接続を切断
    mysqli_close($link);
    
    // JSON形式で結果を返す
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'beforeImagePath' => $beforeImagePath,
        'afterImagePath' => $afterImagePath,
        'partsPath' => $partsPath,
        'currentStage' => $currentStage,
        'newStage' => $newStage
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
    
} else if (!$isAjax) {
    // 通常のページアクセスの場合、投票ページを表示
    require_once __DIR__ . "/tpl/vote.php";
} else {
    // 無効なリクエスト
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['error' => '無効なリクエストです'], JSON_UNESCAPED_UNICODE);
    exit;
}
?>