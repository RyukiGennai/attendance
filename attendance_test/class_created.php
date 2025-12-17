<?php
session_start();
require_once 'db_connect.php';
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE CLASS_ID = ?");
$stmt->execute([$id]);
$class = $stmt->fetch();
require_once 'header.php';
?>
<div class="w-full max-w-lg bg-white p-8 rounded shadow text-center">
    <h2 class="text-2xl font-bold mb-6">出席フォームが作成されました</h2>
    <p class="mb-2">作成者: <?= htmlspecialchars($_SESSION['name']) ?></p>
    
    <div class="my-6 bg-gray-50 p-4 rounded">
        <p class="text-sm text-gray-500">出席コード</p>
        <p class="text-4xl font-bold tracking-widest text-blue-600"><?= $class['ATTENDANCE_CODE'] ?></p>
    </div>
    
    <div class="mb-6">
        <p class="text-sm text-gray-500">共有URL</p>
        <input type="text" value="<?= $class['URL'] ?>" class="w-full text-center border p-2 text-sm" readonly>
    </div>
    
    <a href="teacher_dashboard.php" class="block w-full bg-blue-600 text-white py-2 rounded font-bold">完了（ダッシュボードへ）</a>
</div>
</body></html>