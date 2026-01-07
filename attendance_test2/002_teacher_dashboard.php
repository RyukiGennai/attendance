<?php
session_start();
require_once '001_index.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) { header('Location: 001_index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8"><title>教員ダッシュボード</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow">
        <div class="flex justify-between items-center mb-10 border-b pb-4">
            <h1 class="text-2xl font-bold text-gray-800">教員メニュー</h1>
            <a href="001_index.php?action=logout" class="text-red-500 font-semibold hover:underline">ログアウト</a>
        </div>
        <p class="mb-8 text-lg text-gray-600">ようこそ、<span class="font-bold text-black"><?= htmlspecialchars($_SESSION['name']) ?></span> 先生</p>
        <div class="grid gap-6">
            <a href="003_create_class.php" class="bg-blue-600 text-white p-5 rounded-lg text-center font-bold hover:bg-blue-700 shadow-md transition">出席フォーム新規作成</a>
            <a href="008_realtime_status.php" class="bg-emerald-600 text-white p-5 rounded-lg text-center font-bold hover:bg-emerald-700 shadow-md transition">リアルタイム出席状況</a>
            <a href="009_attendance_list.php" class="bg-slate-600 text-white p-5 rounded-lg text-center font-bold hover:bg-slate-700 shadow-md transition">出席データ管理・編集</a>
        </div>
    </div>
</body>
</html>