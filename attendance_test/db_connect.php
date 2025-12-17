<?php
// セッション開始（全ページ共通）
session_start();

// DB接続設定
$host = 'localhost';
$dbname = 'attendance_db';
$username = 'root'; // 環境に合わせて変更してください
$password = '';     // 環境に合わせて変更してください

try {
    $pdo = new PDO("mysql:host=$host;port=3307;dbname=$dbname;charset=utf8", $username, $password);
    // エラーモードを例外に設定
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('DB Connection Error:' . $e->getMessage());
}

// ログイン確認用関数
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }
}

// 権限確認（先生:1, 生徒:2 と仮定）
function checkTeacher() {
    if ($_SESSION['role'] != 1 && $_SESSION['role'] != 'teacher') {
        exit('Access Denied');
    }
}
?>