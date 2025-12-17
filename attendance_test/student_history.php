<?php
require 'db_connect.php';
checkAuth();

$userId = $_SESSION['user_id'];

// 自分の履歴のみ取得
$sql = "SELECT s.*, c.CLASS_NAME, c.DATE 
        FROM tbl_attendance_status s
        JOIN tbl_class c ON s.CLASS_ID = c.CLASS_ID
        WHERE s.USER_ID = ?
        ORDER BY c.DATE DESC, s.TIMESTAMP DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$myRecords = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>出席履歴</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    <div class="max-w-md mx-auto mt-10 bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex items-center gap-2">
            <i data-lucide="history" class="text-indigo-600"></i> <span class="font-bold">あなたの出席履歴</span>
        </div>
        <div class="p-0">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-xs text-gray-500">
                        <th class="p-3 font-medium">日付</th>
                        <th class="p-3 font-medium">授業</th>
                        <th class="p-3 font-medium">状況</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($myRecords)): ?>
                        <tr><td colspan="3" class="p-8 text-center text-gray-400 text-sm">履歴がありません</td></tr>
                    <?php else: ?>
                        <?php foreach($myRecords as $r): ?>
                        <tr>
                            <td class="p-3 text-sm text-gray-600"><?= htmlspecialchars($r['DATE']) ?></td>
                            <td class="p-3 text-sm font-bold text-gray-800"><?= htmlspecialchars($r['CLASS_NAME']) ?></td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs font-bold <?= $r['ATTENDANCE_STATUS']=='出席'?'bg-green-100 text-green-700':($r['ATTENDANCE_STATUS']=='遅刻'?'bg-yellow-100 text-yellow-700':'bg-red-100 text-red-700') ?>">
                                    <?= htmlspecialchars($r['ATTENDANCE_STATUS']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-gray-50 border-t">
            <a href="student_dashboard.php" class="block text-center w-full py-2 border bg-white rounded hover:bg-gray-50 transition">ダッシュボードへ戻る</a>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>