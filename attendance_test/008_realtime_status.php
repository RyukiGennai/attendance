<?php
require_once 'db_connect.php';
$stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE DATE = CURRENT_DATE ORDER BY CLASS_ID DESC LIMIT 1");
$stmt->execute();
$class = $stmt->fetch();
$list = [];
if ($class) {
    $stmt2 = $pdo->prepare(
        "SELECT a.*, u.NAME, u.STUDENT_NUMBER, c.CLASS_NAME
    FROM tbl_attendance_status a
    JOIN mst_user u ON a.USER_ID = u.USER_ID
    JOIN tbl_class c ON a.CLASS_ID = c.CLASS_ID
    WHERE a.CLASS_ID = ? ORDER BY a.TIMESTAMP DESC"
    );
    $stmt2->execute([$class['CLASS_ID']]);
    $list = $stmt2->fetchAll();
}
require_once 'header.php';
?>

<meta http-equiv="refresh" content="5">

<div class="max-w-4xl mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">リアルタイム出席状況 (計 <?= count($list) ?> 名)</h2>
        <a href="002_teacher_dashboard.php" class="text-blue-600 hover:underline">戻る</a>
    </div>

    <table class="w-full bg-white border-collapse shadow">
        <thead>
            <tr class="bg-gray-100 border-b">
                <th class="p-3 border">授業名</th>
                <th class="p-3 border">学籍番号</th>
                <th class="p-3 border">名前</th>
                <th class="p-3 border">状況</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $row): ?>
            <tr class="text-center border-b">
                <td class="p-3 border"><?= htmlspecialchars($row['CLASS_NAME']) ?></td>
                <td class="p-3 border"><?= htmlspecialchars($row['STUDENT_NUMBER']) ?></td>
                <td class="p-3 border font-bold"><?= htmlspecialchars($row['NAME']) ?></td>
                <td class="p-3 border text-green-600 font-bold"><?= htmlspecialchars($row['ATTENDANCE_STATUS']) ?></td>
            </tr>
            <?php endforeach; ?>

            <?php if(!$list): ?>
                <tr><td colspan="4" class="p-10 text-center text-gray-400">本日の出席データはありません</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>