<?php
// エラー表示の制御
// 本番環境（ロリポップ等）: display_errors = '0'
// 開発環境（XAMPP等）: display_errors = '1'
error_reporting(E_ALL);
ini_set('display_errors', '0'); // ロリポップサーバーでは0に設定

// キャラクター番号を取得（POSTデータがない場合や範囲外の場合は0をデフォルト値として使用）
$characterNum = isset($_POST['characterNum']) ? (int)$_POST['characterNum'] : 0;
// 入力値の検証（0-4の範囲内かチェック）
if ($characterNum < 0 || $characterNum > 4) {
    $characterNum = 0;
}

require_once __DIR__ . "/tpl/vote_result.php";
?>