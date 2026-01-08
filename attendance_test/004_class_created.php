<?php
require_once 'db_connect.php';
$stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE CLASS_ID = ?");
$stmt->execute([$_GET['id']]);
$class = $stmt->fetch();
require_once 'header.php';
?>

<div class="max-w-lg bg-white p-8 rounded shadow mx-auto text-center mt-10">
    
    <h2 class="text-2xl font-bold mb-6 text-green-600">授業を作成しました</h2>
    
    <p class="text-gray-500 mb-4">学生に以下のコードを共有してください</p>

    <div class="mb-8 p-6 bg-blue-50 rounded border border-blue-200">
        <p class="text-sm text-blue-500 font-bold mb-2">出席コード</p>
        
        <p class="text-6xl font-mono font-bold text-blue-700"><?= htmlspecialchars($class['ATTENDANCE_CODE']) ?></p>
    </div>

    <a href="002_teacher_dashboard.php" class="block w-full bg-gray-800 text-white p-4 rounded font-bold">
        ダッシュボードへ戻る
    </a>
</div>