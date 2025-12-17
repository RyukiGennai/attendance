<?php
require 'db_connect.php';
checkAuth();
checkTeacher();

// 一覧取得
$sql = "SELECT 
            s.ATTENDANCE_ID, s.ATTENDANCE_STATUS, s.TIMESTAMP, s.COMMENT,
            u.NAME as studentName, u.STUDENT_NUMBER as studentId,
            c.CLASS_NAME, c.DATE
        FROM tbl_attendance_status s
        JOIN mst_user u ON s.USER_ID = u.USER_ID
        JOIN tbl_class c ON s.CLASS_ID = c.CLASS_ID
        ORDER BY c.DATE DESC, s.TIMESTAMP DESC";
$records = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>出席管理一覧</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    <nav class="bg-white shadow p-4 mb-6 flex justify-between items-center">
        <div class="font-bold text-lg">出席記録一覧</div>
        <a href="teacher_dashboard.php" class="text-sm text-gray-500 hover:text-gray-900">ダッシュボードへ戻る</a>
    </nav>

    <div class="max-w-7xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow overflow-hidden mt-4">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-gray-600">日付 / 授業</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">学生</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">ステータス</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">打刻時間</th>
                        <th class="p-4 text-sm font-semibold text-gray-600">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach($records as $r): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="p-4">
                            <div class="font-bold text-gray-800"><?= htmlspecialchars($r['CLASS_NAME']) ?></div>
                            <div class="text-xs text-gray-500"><?= htmlspecialchars($r['DATE']) ?></div>
                        </td>
                        <td class="p-4">
                            <div class="font-bold"><?= htmlspecialchars($r['studentName']) ?></div>
                            <div class="text-xs text-gray-500"><?= htmlspecialchars($r['studentId']) ?></div>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 text-xs font-bold rounded <?= $r['ATTENDANCE_STATUS']=='出席'?'bg-green-100 text-green-700':($r['ATTENDANCE_STATUS']=='遅刻'?'bg-yellow-100 text-yellow-700':'bg-red-100 text-red-700') ?>">
                                <?= htmlspecialchars($r['ATTENDANCE_STATUS']) ?>
                            </span>
                        </td>
                        <td class="p-4 text-sm text-gray-500">
                            <?= date('H:i:s', strtotime($r['TIMESTAMP'])) ?>
                        </td>
                        <td class="p-4">
                            <a href="edit_record.php?id=<?= $r['ATTENDANCE_ID'] ?>" class="text-indigo-600 hover:text-indigo-900 text-sm font-bold">編集</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if(empty($records)): ?>
                <div class="p-8 text-center text-gray-400">データがありません</div>
            <?php endif; ?>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>