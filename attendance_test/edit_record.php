<?php
require 'db_connect.php';
checkAuth();
checkTeacher();

$id = isset($_GET['id']) ? $_GET['id'] : null;

// 更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $comment = $_POST['comment'];
    $recId = $_POST['record_id'];

    $sql = "UPDATE tbl_attendance_status SET ATTENDANCE_STATUS = ?, COMMENT = ? WHERE ATTENDANCE_ID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status, $comment, $recId]);

    header("Location: attendance_list.php");
    exit;
}

// データ取得
if ($id) {
    $sql = "SELECT s.*, u.NAME, c.CLASS_NAME, c.DATE 
            FROM tbl_attendance_status s
            JOIN mst_user u ON s.USER_ID = u.USER_ID
            JOIN tbl_class c ON s.CLASS_ID = c.CLASS_ID
            WHERE s.ATTENDANCE_ID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $record = $stmt->fetch();
}

if (!$record) exit('Record not found');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>記録編集</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-lg">
        <h2 class="text-xl font-bold mb-6 border-b pb-2">出席データの修正</h2>
        
        <div class="mb-6 grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500 block">授業</span>
                <span class="font-bold"><?= htmlspecialchars($record['CLASS_NAME']) ?></span>
            </div>
            <div>
                <span class="text-gray-500 block">学生名</span>
                <span class="font-bold"><?= htmlspecialchars($record['NAME']) ?></span>
            </div>
        </div>

        <form method="POST">
            <input type="hidden" name="record_id" value="<?= $record['ATTENDANCE_ID'] ?>">
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">ステータス</label>
                <select name="status" class="w-full p-2 border rounded bg-gray-50">
                    <option value="出席" <?= $record['ATTENDANCE_STATUS']=='出席'?'selected':'' ?>>出席</option>
                    <option value="遅刻" <?= $record['ATTENDANCE_STATUS']=='遅刻'?'selected':'' ?>>遅刻</option>
                    <option value="欠席" <?= $record['ATTENDANCE_STATUS']=='欠席'?'selected':'' ?>>欠席</option>
                    <option value="早退" <?= $record['ATTENDANCE_STATUS']=='早退'?'selected':'' ?>>早退</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">コメント</label>
                <textarea name="comment" class="w-full p-2 border rounded bg-gray-50" rows="3"><?= htmlspecialchars($record['COMMENT']) ?></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">更新保存</button>
                <a href="attendance_list.php" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded text-center hover:bg-gray-300">キャンセル</a>
            </div>
        </form>
    </div>
</body>
</html>