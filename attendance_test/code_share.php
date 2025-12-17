<?php
require 'db_connect.php';
checkAuth();

$classId = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE CLASS_ID = ?");
$stmt->execute([$classId]);
$class = $stmt->fetch();

if (!$class) exit('Class not found');

// 学生用のURL (QRコード用)
$studentUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/student_dashboard.php?code=" . $class['ATTENDANCE_CODE'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>コード共有</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 h-screen flex flex-col items-center justify-center text-center">
    <h2 class="text-xl text-gray-500 mb-2"><?= htmlspecialchars($class['CLASS_NAME']) ?></h2>
    <div class="text-6xl font-mono font-bold tracking-widest text-indigo-700 mb-8">
        <?= htmlspecialchars($class['ATTENDANCE_CODE']) ?>
    </div>
    
    <div class="bg-white p-4 rounded-xl shadow mb-8">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($studentUrl) ?>" alt="QR">
    </div>

    <a href="live_status.php?id=<?= $classId ?>" class="bg-emerald-600 text-white px-8 py-3 rounded-lg font-bold shadow hover:bg-emerald-700">
        リアルタイム状況を見る
    </a>
    <a href="teacher_dashboard.php" class="block mt-4 text-gray-500 hover:text-gray-900">ダッシュボードへ戻る</a>
</body>
</html>