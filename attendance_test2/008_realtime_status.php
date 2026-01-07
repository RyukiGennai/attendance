<?php
require_once 'db_connect.php';
$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE DATE = CURRENT_DATE AND USER_ID = ? ORDER BY CLASS_ID DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id'] ?? '']);
$class = $stmt->fetch();
$list = [];
if ($class) {
    $stmt2 = $pdo->prepare("SELECT a.*, u.NAME, u.STUDENT_NUMBER FROM tbl_attendance_status a JOIN mst_user u ON a.USER_ID = u.USER_ID WHERE a.CLASS_ID = ? ORDER BY a.TIMESTAMP DESC");
    $stmt2->execute([$class['CLASS_ID']]);
    $list = $stmt2->fetchAll();
}
require_once 'header.php';
?>
<meta http-equiv="refresh" content="5">
<div class="max-w-3xl mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">現在の出席状況 (計 <?= count($list) ?> 名)</h2>
        <a href="002_teacher_dashboard.php" class="text-blue-600">戻る</a>
    </div>
    <table class="w-full bg-white border">
        <tr class="bg-gray-100">
            <th class="border p-2">学籍番号</th><th class="border p-2">名前</th><th class="border p-2">時刻</th><th class="border p-2">状況</th>
        </tr>
        <?php foreach ($list as $row): ?>
        <tr class="text-center">
            <td class="border p-2"><?= $row['STUDENT_NUMBER'] ?></td>
            <td class="border p-2 font-bold"><?= $row['NAME'] ?></td>
            <td class="border p-2"><?= date('H:i', strtotime($row['TIMESTAMP'])) ?></td>
            <td class="border p-2 text-green-600"><?= $row['ATTENDANCE_STATUS'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>