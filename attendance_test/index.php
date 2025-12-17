<?php
require 'db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_POST['userid'];
    $pwd = $_POST['password'];

    // DBからユーザー検索
    $stmt = $pdo->prepare("SELECT * FROM mst_user WHERE USER_ID = ?");
    $stmt->execute([$uid]);
    $user = $stmt->fetch();

    // パスワード確認
    if ($user && $user['PASSWORD'] === $pwd) {
        // セッションに保存
        $_SESSION['user_id'] = $user['USER_ID'];
        $_SESSION['name'] = $user['NAME'];
        $_SESSION['role'] = $user['ROLE'];
        $_SESSION['student_number'] = $user['STUDENT_NUMBER'];

        // ロールで振り分け (1=先生, 2=生徒 と仮定)
        if ($user['ROLE'] == 1 || $user['ROLE'] == 'teacher') {
            header("Location: teacher_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit;
    } else {
        $error = 'IDまたはパスワードが間違っています';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - AttendancePro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-sm">
        <h1 class="text-2xl font-bold text-center mb-6 text-indigo-600">AttendancePro</h1>
        <?php if($error): ?>
            <p class="text-red-500 text-sm mb-4 text-center"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">ユーザーID</label>
                <input type="text" name="userid" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">パスワード</label>
                <input type="password" name="password" class="w-full p-2 border rounded" required>
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white p-2 rounded hover:bg-indigo-700">ログイン</button>
        </form>
    </div>
</body>
</html>