<?php
require_once '001_index.php';

// 削除処理
if (isset($_POST['delete'])) {
    $pdo->prepare("DELETE FROM tbl_attendance_status WHERE ATTENDANCE_ID = ?")->execute([$_POST['id']]);
}

$sql = "SELECT a.*, u.NAME, u.STUDENT_NUMBER, c.CLASS_NAME, c.DATE 
        FROM tbl_attendance_status a 
        LEFT JOIN mst_user u ON a.USER_ID = u.USER_ID 
        LEFT JOIN tbl_class c ON a.CLASS_ID = c.CLASS_ID 
        ORDER BY c.DATE DESC, a.TIMESTAMP DESC";
$list = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8"><title>出席記録一覧</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">出席記録一覧</h2>
            <div class="space-x-2">
                <a href="010_edit_attendance.php?action=new" class="bg-green-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-green-700">+ 新規記録を追加</a>
                <a href="002_teacher_dashboard.php" class="text-gray-500 border px-4 py-2 rounded hover:bg-gray-50">戻る</a>
            </div>
        </div>

        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-3">日付</th>
                    <th class="p-3">授業名</th>
                    <th class="p-3">学籍番号</th>
                    <th class="p-3">名前</th>
                    <th class="p-3">状況</th>
                    <th class="p-3">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $row): ?>
                <tr class="border-b text-center hover:bg-gray-50">
                    <td class="p-3"><?= htmlspecialchars($row['DATE']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($row['CLASS_NAME']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($row['STUDENT_NUMBER']) ?></td>
                    <td class="p-3 font-bold"><?= htmlspecialchars($row['NAME']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($row['ATTENDANCE_STATUS']) ?></td>
                    <td class="p-3 flex justify-center gap-2">
                        <a href="010_edit_attendance.php?id=<?= $row['ATTENDANCE_ID'] ?>" class="text-blue-600 border border-blue-600 px-3 py-1 rounded text-sm hover:bg-blue-50">編集</a>
                        <form method="post" onsubmit="return confirm('本当に削除しますか？')">
                            <input type="hidden" name="id" value="<?= $row['ATTENDANCE_ID'] ?>">
                            <button type="submit" name="delete" class="text-red-600 border border-red-600 px-3 py-1 rounded text-sm hover:bg-red-50">削除</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>