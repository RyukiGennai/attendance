<?php
session_start();
require_once '001_index.php';
$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE CLASS_ID = ?");
$stmt->execute([$_GET['id']]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

$qr_url = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($class['URL']) . "&choe=UTF-8";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8"><title>作成完了</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 flex justify-center items-center min-h-screen">
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-xl text-center">
        <h2 class="text-xl font-bold text-green-600 mb-4">出席フォームが有効になりました</h2>
        <div class="bg-blue-50 p-6 rounded-xl mb-6">
            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Attendance Code</p>
            <p class="text-5xl font-black text-blue-700 tracking-tighter"><?= $class['ATTENDANCE_CODE'] ?></p>
        </div>
        <div class="mb-6 flex justify-center">
            <img src="<?= $qr_url ?>" alt="QR Code" class="border p-2 bg-white shadow-sm">
        </div>
        <div class="space-y-3">
            <a href="008_realtime_status.php" class="block w-full bg-emerald-600 text-white p-3 rounded-lg font-bold">リアルタイム状況を見る</a>
            <a href="002_teacher_dashboard.php" class="block w-full bg-gray-100 text-gray-600 p-3 rounded-lg font-bold border hover:bg-gray-200 transition">戻る</a>
        </div>
    </div>
</body>
</html>