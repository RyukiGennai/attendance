<?php
session_start();
require_once '001_index.php';
$pdo = getDB();
$stmt = $pdo->prepare("SELECT a.*, c.CLASS_NAME, c.DATE FROM tbl_attendance_status a JOIN tbl_class c ON a.CLASS_ID = c.CLASS_ID WHERE a.USER_ID = ? ORDER BY c.DATE DESC");
$stmt->execute([$_SESSION['user_id']]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8"><title>出席履歴</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">自分の出席履歴</h2>
            <a href="005_student_dashboard.php" class="text-blue-500 hover:underline">戻る</a>
        </div>
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-100 text-gray-600 text-sm uppercase">
                    <tr><th class="p-4">日付</th><th class="p-4">授業名</th><th class="p-4">状況</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($history as $row): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-4"><?= htmlspecialchars($row['DATE']) ?></td>
                        <td class="p-4 font-bold text-gray-700"><?= htmlspecialchars($row['CLASS_NAME']) ?></td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?= $row['ATTENDANCE_STATUS'] == '出席' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                <?= $row['ATTENDANCE_STATUS'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>