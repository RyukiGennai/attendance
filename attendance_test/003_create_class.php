<?php
require_once 'db_connect.php';
$pdo = getDB();
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['attendance_code'];
    if (preg_match('/^[A-Za-z0-9]{6}$/', $code)) {
        $url = "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . "/005_student_dashboard.php?code=" . $code;
        $stmt = $pdo->prepare("INSERT INTO tbl_class (CLASS_NAME, DATE, TIME, ATTENDANCE_CODE, URL, USER_ID) VALUES (?, ?, NOW(), ?, ?, ?)");
        $stmt->execute([$_POST['class_name'], $_POST['date'], $code, $url, $_SESSION['user_id']]);
        header("Location: 004_class_created.php?id=" . $pdo->lastInsertId());
        exit;
    } else {
        $msg = "コードは英数字6桁にしてください";
    }
}

require_once 'header.php';
?>

<div class="max-w-lg w-full bg-white p-8 rounded shadow-lg mx-auto">
    <h2 class="text-2xl font-bold mb-6">出席フォーム作成</h2>

    <?php if ($msg): ?><p class="text-red-500 mb-4"><?= $msg ?></p><?php endif; ?>

    <form method="post" class="space-y-4">
        <input type="text" name="class_name" placeholder="授業名" class="w-full border p-3 rounded" required>

        <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="w-full border p-3 rounded" required>

        <div class="flex gap-2">
            <input type="text" id="code" name="attendance_code" placeholder="出席コード" maxlength="6" class="w-full border p-3 rounded" required>

            <button type="button" onclick="document.getElementById('code').value = Math.random().toString(36).substring(2,8)" class="bg-gray-200 px-4 rounded">
                生成
            </button>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white p-4 rounded font-bold">作成</button>
    </form>

    <a href="002_teacher_dashboard.php" class="block text-center mt-6 text-gray-500">戻る</a>
</div>