<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: index.php');
    exit;
}
require_once 'header.php';
?>
<div class="w-full max-w-2xl px-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">出席管理システム</h1>
        <a href="logout.php" class="text-red-500 hover:underline">ログアウト</a>
    </div>

    <p class="mb-8 text-xl">ようこそ、<?= htmlspecialchars($_SESSION['name']) ?> 先生</p>

    <div class="space-y-4">
        <h2 class="text-xl font-bold border-b pb-2">教員ダッシュボード</h2>
        
        <a href="create_class.php" class="block w-full bg-blue-600 text-white text-center py-4 rounded text-lg font-bold hover:bg-blue-700">
            出席フォームを新規作成
        </a>
        
        <a href="realtime_status.php" class="block w-full bg-green-600 text-white text-center py-4 rounded text-lg font-bold hover:bg-green-700">
            リアルタイム出席状況
        </a>
        
        <a href="attendance_list.php" class="block w-full bg-gray-600 text-white text-center py-4 rounded text-lg font-bold hover:bg-gray-700">
            出席リストの確認・編集
        </a>
    </div>
</div>
</body></html>