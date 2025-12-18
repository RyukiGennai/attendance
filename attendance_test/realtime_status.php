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
<script>
    setTimeout(function(){
        window.location.reload();
    }, 5000);
</script>

<div class="w-full max-w-4xl px-4 mx-auto">
    <div class="flex justify-between items-end mb-4">
        <div>
            <h2 class="text-2xl font-bold">リアルタイム出席状況</h2>
            <p class="text-gray-600">担当: <?= htmlspecialchars($_SESSION['name']) ?></p>
        </div>
        <div class="text-right text-sm text-gray-500">
            <p>最終更新: <?= date('H:i:s') ?></p>
            <p class="text-xs">（5秒ごとに自動更新）</p>
        </div>
    </div>
    
    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded mb-6 flex justify-between items-center shadow-sm">
        <div>
            <p class="font-bold text-lg text-yellow-800">現在の授業: <?= $class ? htmlspecialchars($class['CLASS_NAME']) : '本日の授業なし' ?></p>
            <?php if($class): ?>
                <p class="text-sm text-yellow-700">
                    開始時刻: <span class="font-bold"><?= date('H:i', strtotime($class['TIME'])) ?></span> 
                    (出席コード: <?= $class['ATTENDANCE_CODE'] ?>)
                </p>
            <?php endif; ?>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600">現在の出席人数</p>
            <p class="text-4xl font-bold text-blue-600"><?= count($attendees) ?><span class="text-base font-normal text-gray-500 ml-1">名</span></p>
        </div>
    </div>

    <table class="w-full bg-white shadow rounded overflow-hidden">
        <thead class="bg-gray-200 text-gray-700">
            <tr>
                <th class="p-3 w-16">No</th>
                <th class="p-3">学籍番号</th>
                <th class="p-3">名前</th>
                <th class="p-3">出席時刻</th>
                <th class="p-3">状況</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($attendees)): ?>
                <tr><td colspan="5" class="p-8 text-center text-gray-400">まだ出席者はいません</td></tr>
            <?php else: ?>
                <?php foreach ($attendees as $i => $row): ?>
                <?php 
                    // 状況によって色を変える
                    $statusColor = 'text-green-600';
                    if ($row['ATTENDANCE_STATUS'] == '遅刻') $statusColor = 'text-yellow-600 font-bold';
                    if ($row['ATTENDANCE_STATUS'] == '欠席') $statusColor = 'text-red-600 font-bold';
                ?>
                <tr class="border-b text-center hover:bg-gray-50 transition">
                    <td class="p-3 text-gray-500"><?= $i + 1 ?></td>
                    <td class="p-3 font-mono"><?= htmlspecialchars($row['STUDENT_NUMBER']) ?></td>
                    <td class="p-3 font-bold"><?= htmlspecialchars($row['NAME']) ?></td>
                    <td class="p-3 text-gray-600"><?= date('H:i:s', strtotime($row['TIMESTAMP'])) ?></td>
                    <td class="p-3 <?= $statusColor ?>"><?= htmlspecialchars($row['ATTENDANCE_STATUS']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="mt-6 text-center">
        <a href="teacher_dashboard.php" class="inline-block bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 transition">戻る</a>
    </div>
</div>
</body></html>