<?php
session_start();
require_once 'db_connect.php';

$stmt = $pdo->prepare("SELECT a.*, c.CLASS_NAME, c.DATE 
                       FROM tbl_attendance_status a 
                       JOIN tbl_class c ON a.CLASS_ID = c.CLASS_ID 
                       WHERE a.USER_ID = ? ORDER BY c.DATE DESC");
$stmt->execute([$_SESSION['user_id']]);
$history = $stmt->fetchAll();

require_once 'header.php';
?>
<div class="w-full max-w-2xl px-4 mb-2 flex justify-end">
    <a href="logout.php" class="text-red-500 hover:underline text-sm">ログアウト</a>
</div>

<div class="w-full max-w-2xl px-4 mx-auto">
    <div class="flex justify-between items-end mb-4">
        <div>
            <h2 class="text-xl font-bold">自分の出席履歴</h2>
            <p class="text-sm text-gray-500"><?= htmlspecialchars($_SESSION['name']) ?> さん</p>
        </div>
    </div>
    
    <table class="w-full bg-white shadow rounded overflow-hidden mb-6">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2">日付</th>
                <th class="p-2">授業名</th>
                <th class="p-2">状況</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $row): ?>
            <tr class="border-b text-center">
                <td class="p-3"><?= htmlspecialchars($row['DATE']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['CLASS_NAME']) ?></td>
                <td class="p-3 font-bold text-blue-600"><?= htmlspecialchars($row['ATTENDANCE_STATUS']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="student_dashboard.php" class="block text-center text-blue-500">戻る</a>
</div>
</body></html>