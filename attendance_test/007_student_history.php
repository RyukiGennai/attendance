<?php
require_once 'db_connect.php';
$pdo = getDB();
$stmt = $pdo->prepare(
    "SELECT a.*, c.CLASS_NAME, c.DATE,
    FROM tbl_attendance_status a ,
    JOIN tbl_class c ON a.CLASS_ID = c.CLASS_ID ,
    WHERE a.USER_ID = ? ,
    ORDER BY c.DATE DESC"
);
$stmt->execute([$_SESSION['user_id']]);
$history = $stmt->fetchAll();
require_once 'header.php';
?>

<div class="max-w-2xl w-full mx-auto p-4">
    <h2 class="text-2xl font-bold mb-6">自分の出席履歴</h2>

    <table class="w-full bg-white rounded shadow">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-3">日付</th>
                <th class="p-3">授業</th>
                <th class="p-3">状況</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $row): ?>
                <tr class="border-b text-center">
                    <td class="p-3"><?= $row['DATE'] ?></td>

                    <td class="p-3"><?= $row['CLASS_NAME'] ?></td>

                    <td class="p-3 text-green-600 font-bold"><?= $row['ATTENDANCE_STATUS'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="005_student_dashboard.php" class="block text-center mt-6">戻る</a>
</div>