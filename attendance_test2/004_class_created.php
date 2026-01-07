<?php
require_once 'db_connect.php';
$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE CLASS_ID = ?");
$stmt->execute([$_GET['id']]);
$class = $stmt->fetch();
require_once 'header.php';
?>
<div class="max-w-lg w-full bg-white p-8 rounded shadow mx-auto text-center">
    <h2 class="text-2xl font-bold mb-6 text-green-600">作成完了</h2>
    <div class="mb-8 p-6 bg-blue-50 rounded border">
        <p class="text-sm text-blue-500 font-bold mb-2">出席コード</p>
        <p class="text-6xl font-mono font-bold text-blue-700"><?= $class['ATTENDANCE_CODE'] ?></p>
    </div>
    <div class="mb-8 text-left">
        <p class="text-sm font-bold text-gray-600 mb-1">学生用URL:</p>
        <input type="text" readonly value="<?= htmlspecialchars($class['URL']) ?>" class="w-full border p-2 bg-gray-50 text-sm">
    </div>
    <a href="002_teacher_dashboard.php" class="block w-full bg-gray-800 text-white p-4 rounded font-bold">戻る</a>
</div>