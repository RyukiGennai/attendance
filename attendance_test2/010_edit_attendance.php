<?php
session_start();
require_once '001_index.php';
$pdo = getDB();
$id = $_GET['id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT USER_ID FROM mst_user WHERE STUDENT_NUMBER = ?");
    $stmt->execute([$_POST['student_number']]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($u) {
        $pdo->prepare("UPDATE tbl_attendance_status SET USER_ID = ?, ATTENDANCE_STATUS = ? WHERE ATTENDANCE_ID = ?")->execute([$u['USER_ID'], $_POST['status'], $id]);
        header('Location: 009_attendance_list.php');
        exit;
    } else { $msg = "学生番号が見つかりません"; }
}

$stmt = $pdo->prepare("SELECT a.*, u.NAME, u.STUDENT_NUMBER, c.CLASS_NAME, c.DATE FROM tbl_attendance_status a LEFT JOIN mst_user u ON a.USER_ID = u.USER_ID LEFT JOIN tbl_class c ON a.CLASS_ID = c.CLASS_ID WHERE a.ATTENDANCE_ID = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8"><title>出席編集</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 flex justify-center">
    <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg">
        <h2 class="text-xl font-bold mb-6 border-b pb-2">出席情報の修正</h2>
        <?php if($msg): ?><p class="text-red-500 mb-4"><?= $msg ?></p><?php endif; ?>
        <form method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-600">学籍番号</label>
                <input type="text" name="student_number" value="<?= $data['STUDENT_NUMBER'] ?>" class="w-full border p-3 rounded font-mono" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600 text-right italic text-gray-400"><?= htmlspecialchars($data['NAME']) ?> さん (<?= htmlspecialchars($data['CLASS_NAME']) ?>)</label>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-600">状況</label>
                <select name="status" class="w-full border p-3 rounded">
                    <?php foreach(['出席','遅刻','欠席'] as $s): ?>
                        <option value="<?= $s ?>" <?= $data['ATTENDANCE_STATUS'] == $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-4 rounded-lg font-bold hover:bg-blue-700">更新する</button>
        </form>
        <a href="009_attendance_list.php" class="block text-center mt-6 text-gray-400 hover:underline">戻る</a>
    </div>
</body>
</html>