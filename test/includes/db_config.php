<?php
// データベース接続設定
define('DB_HOST', 'localhost');
define('DB_NAME', 'attendance_db'); // 作成したDB名
define('DB_USER', 'root');          // DBユーザー名
define('DB_PASS', 'root');              // DBパスワード

function get_db_connection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        exit("DB Connection Error: " . $e->getMessage());
    }
}
?>