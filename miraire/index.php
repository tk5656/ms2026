<?php
// エラー表示の制御
// 本番環境（ロリポップ等）: display_errors = '0'
// 開発環境（XAMPP等）: display_errors = '1'
error_reporting(E_ALL);
ini_set('display_errors', '0'); // ロリポップサーバーでは0に設定

require_once __DIR__ . "/tpl/index.php";
?>