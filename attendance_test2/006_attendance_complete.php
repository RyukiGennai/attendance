<?php
session_start();
require_once '001_index.php';
$pdo = getDB();
$status = $_GET['status'] ?? '完了';
$status_color = ($status === '出席') ? 'text-green-600' : ($status === '遅刻' ? 'text-yellow-600' : 'text-red-600');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8"><title>送信完了</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="bg-white p-10 rounded-2xl shadow-xl text-center max-w-sm w-full mx-4">
        <div class="mb-6 inline-block p-4 bg-green-50 rounded-full">
            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h2 class="text-2xl font-black text-gray-800 mb-2">送信完了</h2>
        <p class="text-gray-500 mb-6 text-sm">出席データが記録されました</p>
        <div class="bg-gray-50 p-6 rounded-xl mb-8">
            <p class="text-xs text-gray-400 mb-1">判定結果</p>
            <p class="text-3xl font-bold <?= $status_color ?>"><?= htmlspecialchars($status) ?></p>
        </div>
        <a href="005_student_dashboard.php" class="block w-full bg-blue-600 text-white p-4 rounded-xl font-bold hover:bg-blue-700 shadow-md">トップページへ</a>
    </div>
</body>
</html>