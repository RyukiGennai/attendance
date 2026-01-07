<?php
require_once '001_index.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 0) { header('Location: 001_index.php'); exit; }
$pdo = getDB();
$msg = '';
$default_code = $_GET['code'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE ATTENDANCE_CODE = ?");
    $stmt->execute([$_POST['code']]);
    $class = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($class) {
        $check = $pdo->prepare("SELECT * FROM tbl_attendance_status WHERE USER_ID = ? AND CLASS_ID = ?");
        $check->execute([$_SESSION['user_id'], $class['CLASS_ID']]);
        if ($check->rowCount() > 0) { $msg = "すでに送信済みです"; }
        else {
            $diff = time() - strtotime($class['TIME']); // 授業作成時刻からの経過時間
            if ($diff <= 600) { $status = '出席'; } // 10分以内
            elseif ($diff <= 1800) { $status = '遅刻'; } // 30分以内
            else { $status = '欠席'; }

            $stmt = $pdo->prepare("INSERT INTO tbl_attendance_status (USER_ID, CLASS_ID, ATTENDANCE_STATUS, TIMESTAMP) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$_SESSION['user_id'], $class['CLASS_ID'], $status]);
            header("Location: 006_attendance_complete.php?status=" . urlencode($status));
            exit;
        }
    } else { $msg = "出席コードが正しくありません"; }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8"><title>出席入力</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-50 p-6 flex justify-center min-h-screen items-center">
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-xl">
        <div class="text-right mb-4">
            <a href="001_index.php?action=logout" class="text-xs text-red-400">ログアウト</a>
        </div>
        <h2 class="text-xl font-bold mb-6 text-center text-gray-700">出席送信画面</h2>
        <div class="bg-gray-50 p-4 rounded-lg mb-6 border text-center">
            <p class="font-bold text-lg"><?= htmlspecialchars($_SESSION['name']) ?></p>
        </div>
        <?php if($msg): ?><p class="text-red-500 text-center mb-4 font-bold"><?= $msg ?></p><?php endif; ?>
        <form method="post" class="space-y-6">
            <div>
                <label class="block text-sm text-gray-500 mb-2 text-center">出席コードを入力</label>
                <input type="text" name="code" value="<?= htmlspecialchars($default_code) ?>" maxlength="6" class="w-full border-2 border-blue-100 p-4 rounded-xl text-center text-3xl font-black uppercase tracking-widest focus:border-blue-500 focus:outline-none" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-4 rounded-xl font-bold text-lg hover:bg-blue-700 shadow-lg">出席を送信する</button>
        </form>
        <a href="007_student_history.php" class="block text-center mt-8 text-blue-500 text-sm hover:underline">自分の出席履歴を確認する</a>
    </div>
</body>
</html>