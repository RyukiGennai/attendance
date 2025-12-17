<?php
session_start();
require_once 'db_connect.php';
// 本日の最新の授業を取得
$stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE DATE = CURRENT_DATE AND USER_ID = ? ORDER BY CLASS_ID DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$class = $stmt->fetch();

$attendees = [];
if ($class) {
    $stmt2 = $pdo->prepare("SELECT a.*, u.NAME, u.STUDENT_NUMBER FROM tbl_attendance_status a JOIN mst_user u ON a.USER_ID = u.USER_ID WHERE a.CLASS_ID = ? ORDER BY a.TIMESTAMP DESC");
    $stmt2->execute([$class['CLASS_ID']]);
    $attendees = $stmt2->fetchAll();
}
require_once 'header.php';
?>
<div class="w-full max-w-4xl px-4">
    <h2 class="text-2xl font-bold mb-2">リアルタイム出席状況</h2>
    <p class="mb-4">担当: <?= htmlspecialchars($_SESSION['name']) ?></p>
    
    <div class="bg-yellow-100 p-4 rounded mb-4">
        <p class="font-bold">現在の授業: <?= $class ? $class['CLASS_NAME'] : '本日の授業なし' ?></p>
        <p class="text-sm">このリストは1日ごとに自動更新されます</p>
    </div>

    <p class="text-right font-bold mb-2">出席人数: <?= count($attendees) ?>名</p>

    <table class="w-full bg-white shadow rounded overflow-hidden">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2">No</th>
                <th class="p-2">学籍番号</th>
                <th class="p-2">名前</th>
                <th class="p-2">出席時刻</th>
                <th class="p-2">状況</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attendees as $i => $row): ?>
            <tr class="border-b text-center">
                <td class="p-2"><?= $i + 1 ?></td>
                <td class="p-2"><?= $row['STUDENT_NUMBER'] ?></td>
                <td class="p-2"><?= $row['NAME'] ?></td>
                <td class="p-2"><?= date('H:i', strtotime($row['TIMESTAMP'])) ?></td>
                <td class="p-2"><?= $row['ATTENDANCE_STATUS'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="teacher_dashboard.php" class="block mt-6 text-center text-blue-500">戻る</a>
</div>
</body></html>