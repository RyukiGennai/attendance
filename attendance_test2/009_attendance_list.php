<?php
session_start();
require_once '001_index.php';
$pdo = getDB();

if (isset($_POST['delete'])) {
    $pdo->prepare("DELETE FROM tbl_attendance_status WHERE ATTENDANCE_ID = ?")->execute([$_POST['id']]);
}

$sql = "SELECT a.*, u.NAME, u.STUDENT_NUMBER, c.CLASS_NAME, c.DATE FROM tbl_attendance_status a 
        LEFT JOIN mst_user u ON a.USER_ID = u.USER_ID 
        LEFT JOIN tbl_class c ON a.CLASS_ID = c.CLASS_ID ORDER BY a.TIMESTAMP DESC";
$list = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8"><title>出席管理一覧</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">出席記録一覧</h2>
            <a href="002_teacher_dashboard.php" class="bg-gray-500 text-white px-4 py-2 rounded">戻る</a>
        </div>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-800 text-white">
                    <tr><th class="p-4">日付</th><th class="p-4">授業</th><th class="p-4">学生番号</th><th class="p-4">名前</th><th class="p-4">状況</th><th class="p-4">操作</th></tr>
                </thead>
                <tbody class="divide-y text-center">
                    <?php foreach ($list as $row): ?>
                    <tr class="hover:bg-blue-50">
                        <td class="p-3"><?= $row['DATE'] ?></td>
                        <td class="p-3 font-bold"><?= $row['CLASS_NAME'] ?></td>
                        <td class="p-3 font-mono text-gray-500"><?= $row['STUDENT_NUMBER'] ?></td>
                        <td class="p-3"><?= $row['NAME'] ?></td>
                        <td class="p-3"><?= $row['ATTENDANCE_STATUS'] ?></td>
                        <td class="p-3 flex justify-center gap-2">
                            <a href="010_edit_attendance.php?id=<?= $row['ATTENDANCE_ID'] ?>" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">編集</a>
                            <form method="post" onsubmit="return confirm('本当に削除しますか？')">
                                <input type="hidden" name="id" value="<?= $row['ATTENDANCE_ID'] ?>">
                                <button type="submit" name="delete" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">削除</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>