<?php
session_start();
require_once '001_index.php';
$pdo = getDB();

// 本日の最新授業を取得
$stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE DATE = CURRENT_DATE AND USER_ID = ? ORDER BY CLASS_ID DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$class = $stmt->fetch();

$attendees = [];
if ($class) {
    $stmt2 = $pdo->prepare("SELECT a.*, u.NAME, u.STUDENT_NUMBER FROM tbl_attendance_status a JOIN mst_user u ON a.USER_ID = u.USER_ID WHERE a.CLASS_ID = ? ORDER BY a.TIMESTAMP DESC");
    $stmt2->execute([$class['CLASS_ID']]);
    $attendees = $stmt2->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>リアルタイム状況</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta http-equiv="refresh" content="5"> </head>
<body class="bg-gray-50 p-6">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">現在の出席状況 (計 <?= count($attendees) ?> 名)</h2>
            <a href="002_teacher_dashboard.php" class="text-blue-600 hover:underline">戻る</a>
        </div>

        <table class="w-full bg-white border-collapse">
            <thead>
                <tr class="bg-gray-200 text-sm">
                    <th class="border p-2">学籍番号</th>
                    <th class="border p-2">名前</th>
                    <th class="border p-2">時刻</th>
                    <th class="border p-2">状況</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendees as $row): ?>
                <tr class="text-center">
                    <td class="border p-2"><?= htmlspecialchars($row['STUDENT_NUMBER']) ?></td>
                    <td class="border p-2 font-bold"><?= htmlspecialchars($row['NAME']) ?></td>
                    <td class="border p-2"><?= date('H:i', strtotime($row['TIMESTAMP'])) ?></td>
                    <td class="border p-2 <?= $row['ATTENDANCE_STATUS'] === '出席' ? 'text-green-600' : 'text-red-600' ?>">
                        <?= htmlspecialchars($row['ATTENDANCE_STATUS']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>