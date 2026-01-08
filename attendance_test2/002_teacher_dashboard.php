<?php
// 1. 【準備】データベースとの接続設定を読み込みます。
// ここで「誰がログインしているか」というメモ（セッション）も一緒に確認します。
require_once 'db_connect.php';

// 2. 【見た目の準備】HTMLの頭の部分（デザインの設定など）を読み込みます。
require_once 'header.php';
?>

<div class="w-full max-w-2xl px-4 mx-auto">
    
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">教員ダッシュボード</h1>
        
        <a href="logout.php" class="text-red-500 hover:underline">ログアウト</a>
    </div>
    <div class="space-y-4">
        
        <a href="003_create_class.php" class="block w-full bg-blue-600 text-white text-center py-4 rounded text-lg font-bold hover:bg-blue-700">
            出席フォームを作成
        </a>

        <a href="008_realtime_status.php" class="block w-full bg-green-600 text-white text-center py-4 rounded text-lg font-bold hover:bg-green-700">
            リアルタイム出席状況
        </a>

        <a href="009_attendance_list.php" class="block w-full bg-gray-800 text-white text-center py-4 rounded text-lg font-bold hover:bg-gray-900">
            出席記録の一覧・編集
        </a>
        
    </div>
</div>