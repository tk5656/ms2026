<?php
// データベース接続設定
// 注意: このファイルはGit管理外です（.gitignoreに追加済み）

// ★ローカル環境（XAMPP用）
const HOST = 'localhost';   // または '127.0.0.1'
const USER = 'root';        // XAMPPのデフォルトユーザー
const DB   = 'miraire';     // ローカルで作ったDB名に合わせる
const PASSWORD = '';        // XAMPPデフォルトは空文字

// ロリポップサーバー環境の場合、上記を以下のように変更してください
// const HOST = 'mysql80-2.lolipop.lan'; // ロリポップのホスト名（管理画面で確認）
// const USER = 'LAA1691490'; // ロリポップのデータベースユーザー名
// const DB = 'LAA1691490-miraire'; // ロリポップのデータベース名
// const PASSWORD = 'DG12PW11'; // ロリポップのデータベースパスワード
