<?php
// 1. 【準備】データベースに接続するための設定を読み込みます。
// この中には「session_start()」という命令が入っていて、
// サーバーが覚えている「今ログインしているのは誰か」というメモ（セッション）を使えるようにします。
require_once 'db_connect.php';

// 2. 【見た目の準備】HTMLの頭の部分（デザインの設定など）を読み込みます。
require_once 'header.php';
?>

<div class="max-w-md w-full bg-white p-8 rounded shadow text-center mx-auto mt-20">
    
    <h2 class="text-2xl font-bold mb-4 text-green-600">出席完了</h2>
    <a href="005_student_dashboard.php" class="block w-full bg-blue-600 text-white p-3 rounded">トップへ戻る</a>
</div>