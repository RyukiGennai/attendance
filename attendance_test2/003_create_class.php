<?php
session_start();
require_once '001_index.php';
if ($_SESSION['role'] != 1) exit;
$pdo = getDB();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $code = $_POST['attendance_code'];
    if (preg_match('/^[A-Z0-9]{6}$/', $code)) {
        // スマホからのアクセスを考慮し、005へのURLを生成
        $url = "http://". $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . "/005_student_dashboard.php?code=" . $code;
        $stmt = $pdo->prepare("INSERT INTO tbl_class (CLASS_NAME, DATE, TIME, ATTENDANCE_CODE, URL, USER_ID) VALUES (?, ?, NOW(), ?, ?, ?)");
        $stmt->execute([$_POST['class_name'], $_POST['date'], $code, $url, $_SESSION['user_id']]);
        header("Location: 004_class_created.php?id=" . $pdo->lastInsertId());
        exit;
    } else { $msg = "出席コードは半角英数字6桁にしてください"; }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8"><title>フォーム作成</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 flex justify-center">
    <div class="max-w-lg w-full bg-white p-8 rounded-xl shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">出席フォーム作成</h2>
        <?php if ($msg): ?><p class="text-red-500 mb-4"><?= $msg ?></p><?php endif; ?>
        <form method="post" class="space-y-4">
            <div>
                <label class="block mb-1 font-bold">授業名</label>
                <input type="text" name="class_name" class="w-full border p-3 rounded" placeholder="例：プログラミング演習I" required>
            </div>
            <div>
                <label class="block mb-1 font-bold">日付</label>
                <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="w-full border p-3 rounded" required>
            </div>
            <div>
                <label class="block mb-1 font-bold">出席コード (6桁)</label>
                <div class="flex gap-2">
                    <input type="text" id="code" name="attendance_code" maxlength="6" class="w-full border p-3 rounded text-center font-mono font-bold uppercase tracking-widest" required>
                    <button type="button" onclick="document.getElementById('code').value = Math.random().toString(36).substring(2,8).toUpperCase()" class="bg-gray-200 px-4 rounded text-sm hover:bg-gray-300 transition">自動生成</button>
                </div>
            </div>
            <button type="submit" name="create" class="w-full bg-blue-600 text-white p-4 rounded-lg font-bold hover:bg-blue-700 shadow-md">作成する</button>
        </form>
        <a href="002_teacher_dashboard.php" class="block text-center mt-6 text-gray-500 hover:underline">戻る</a>
    </div>
</body>
</html>