<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/config.php';

$characterNum = isset($_POST['characterNum']) ? (int)$_POST['characterNum'] : null;

if ($characterNum === null || $characterNum < 0 || $characterNum > 4) {
    header('Location: ./vote.php');
    exit;
}

$characterData = [
    ['name' => '雪原ペン次', 'pledge' => 'コンビニを学内に作る'],
    ['name' => '虹宮ニョロ明', 'pledge' => '教室に落書きし放題'],
    ['name' => '鳥谷コケ蔵', 'pledge' => '屋上にビアガーデンを作る'],
    ['name' => '犬山イチ郎', 'pledge' => '学内に屋内喫煙所を作る'],
    ['name' => '猫川ニャミ子', 'pledge' => 'ゲーミングルームを作る']
];

$characterName = $characterData[$characterNum]['name'];
$characterPledge = $characterData[$characterNum]['pledge'];

$link = mysqli_connect(HOST, USER, PASSWORD, DB);
if (!$link) {
    die('データベース接続に失敗しました');
}
mysqli_set_charset($link, "utf8mb4");

$sql = "SELECT current_vote FROM characters WHERE id = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $characterNum);
mysqli_stmt_execute($stmt);
$currentVote = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['current_vote'] ?? 0;
mysqli_stmt_close($stmt);

$sql = "SELECT isometric_path FROM isometric 
        WHERE character_id = ? 
        AND stage_number = (SELECT MAX(stage_number) FROM isometric 
                            WHERE character_id = ? AND stage_number <= ?)";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "iii", $characterNum, $characterNum, $currentVote);
mysqli_stmt_execute($stmt);
$image = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
mysqli_close($link);

$isometricPath = !empty($image['isometric_path']) 
    ? '../' . $image['isometric_path'] 
    : './images/vote/isometric' . ($characterNum + 1) . '.png';

require_once __DIR__ . "/tpl/voting_completed.php";
?>