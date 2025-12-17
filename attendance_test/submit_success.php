<?php
require 'db_connect.php';
checkAuth();

$status = isset($_GET['status']) ? $_GET['status'] : '完了';
$className = isset($_GET['class']) ? $_GET['class'] : '';

// ステータスに応じた色分け
$badgeClass = ($status === '出席') 
    ? "bg-green-100 text-green-700" 
    : "bg-yellow-100 text-yellow-700";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>送信完了</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4 text-center py-12 bg-white rounded-xl shadow-xl">
        <div class="inline-block p-4 bg-green-100 rounded-full mb-6">
            <i data-lucide="check-circle" class="h-12 w-12 text-green-600"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">送信完了</h2>
        <p class="text-gray-500 mb-6">出席データが記録されました</p>
        
        <div class="bg-gray-50 p-4 rounded-lg mx-8 mb-8 text-left border">
            <div class="mb-2 text-sm text-gray-500">授業名:</div>
            <div class="font-bold text-lg mb-4"><?= htmlspecialchars($className) ?></div>
            <div class="flex justify-between items-center border-t pt-2">
                <div class="text-sm text-gray-500">ステータス:</div>
                <span class="px-3 py-1 rounded-full text-sm font-bold <?= $badgeClass ?>">
                    <?= htmlspecialchars($status) ?>
                </span>
            </div>
        </div>

        <a href="student_dashboard.php" class="px-8 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition">トップに戻る</a>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>