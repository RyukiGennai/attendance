<?php
session_start();
require_once 'db_connect.php';
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE CLASS_ID = ?");
$stmt->execute([$id]);
$class = $stmt->fetch();

// QRコードを生成する外部APIのURLを作成（URLエンコードが重要）
$qr_api_url = "https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=" . urlencode($class['URL']) . "&choe=UTF-8";

require_once 'header.php';
?>
<div class="w-full max-w-lg bg-white p-8 rounded shadow text-center mx-auto">
    <h2 class="text-2xl font-bold mb-6">出席フォームが作成されました</h2>
    <p class="mb-2">作成者: <?= htmlspecialchars($_SESSION['name']) ?></p>
    
    <div class="my-6 bg-blue-50 p-4 rounded border border-blue-200">
        <p class="text-sm text-gray-500 mb-1">出席コード</p>
        <p class="text-5xl font-bold tracking-widest text-blue-600"><?= $class['ATTENDANCE_CODE'] ?></p>
    </div>
    
    <div class="mb-6 bg-gray-50 p-4 rounded">
        <p class="text-sm font-bold text-gray-700 mb-4">学生配布用 QRコード</p>
        <img src="<?= $qr_api_url ?>" alt="QR Code" class="mx-auto shadow bg-white p-2 rounded">
        <p class="text-xs text-gray-500 mt-2">※このQRコードをスクリーンに投影するなどして、学生に読み取らせてください。</p>
    </div>

    <div class="mb-8">
        <p class="text-sm text-gray-500 mb-2">共有URL</p>
        <input type="text" value="<?= $class['URL'] ?>" class="w-full text-center border p-3 text-sm rounded bg-gray-100" readonly onclick="this.select();">
    </div>
    
    <a href="teacher_dashboard.php" class="block w-full bg-gray-600 text-white py-3 rounded font-bold hover:bg-gray-700">完了（ダッシュボードへ）</a>
</div>
</body></html>