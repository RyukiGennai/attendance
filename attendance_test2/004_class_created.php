<?php
session_start();
require_once '001_index.php';
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE CLASS_ID = ?");
$stmt->execute([$id]);
$class = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8"><title>作成完了</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 flex justify-center">
    <div class="max-w-lg w-full bg-white p-8 rounded-xl shadow-lg text-center">
        <h2 class="text-2xl font-bold mb-6 text-green-600">出席フォームを作成しました</h2>
        
        <div class="mb-8 p-6 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-sm text-blue-500 font-bold mb-2">出席コード</p>
            <p class="text-6xl font-mono font-bold tracking-tighter text-blue-700"><?= $class['ATTENDANCE_CODE'] ?></p>
        </div>

        <div class="mb-8 text-left">
            <p class="text-sm font-bold text-gray-600 mb-1">学生用URL:</p>
            <input type="text" readonly value="<?= htmlspecialchars($class['URL']) ?>" class="w-full border p-2 bg-gray-50 text-sm rounded">
        </div>

        <a href="002_teacher_dashboard.php" class="inline-block w-full bg-gray-800 text-white p-4 rounded-lg font-bold hover:bg-gray-900 transition">ダッシュボードに戻る</a>
    </div>
</body>
</html>