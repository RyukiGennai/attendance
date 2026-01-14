<?php
require_once 'db_connect.php';
$pdo = getDB();
$msg = '';
$code = $_GET['code'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_code = $_POST['code'];
    $stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE ATTENDANCE_CODE = ?");
    $stmt->execute([$input_code]);
    $class = $stmt->fetch();
    if ($class) {
        $stmt = $pdo->prepare("INSERT INTO tbl_attendance_status (USER_ID, CLASS_ID, ATTENDANCE_STATUS, TIMESTAMP) VALUES (?, ?, '出席', NOW())");
        $stmt->execute([$_SESSION['user_id'], $class['CLASS_ID']]);
        header("Location: 006_attendance_complete.php");
        exit;
    } else {
        $msg = "無効なコードです";
    }
}
require_once 'header.php';
?>

<div class="max-w-md w-full bg-white p-8 rounded shadow mx-auto mt-10">

    <div class="relative flex items-center justify-center mb-6">
        <h2 class="text-xl font-bold">出席送信</h2>

        <a href="logout.php" class="absolute right-0 text-red-500 hover:underline text-sm">
            ログアウト
        </a>
    </div>

    <p class="text-center mb-6 text-gray-600 border-b pb-2">
        <strong><?= htmlspecialchars($_SESSION['name'] ?? '学生') ?></strong>
    </p>

    <?php if ($msg): ?><p class="text-red-500 text-center mb-4"><?= $msg ?></p><?php endif; ?>

    <form method="post" class="space-y-4">
        <input type="text" name="code" value="<?= htmlspecialchars($code) ?>" placeholder="出席コード入力" class="w-full border p-4 text-center text-2xl font-bold" required>

        <button type="submit" class="w-full bg-blue-600 text-white p-4 rounded font-bold text-lg hover:bg-blue-700 transition">
            出席を送信する
        </button>
    </form>

    <a href="007_student_history.php" class="block text-center mt-6 text-blue-500 hover:underline">
        自分の出席履歴を見る
    </a>
</div>