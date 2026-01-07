<?php
session_start();

// --- 共通DB接続設定 ---
function getDB() {
    $host = 'localhost'; $dbname = 'attendance_db'; $user = 'root'; $pass = 'root';
    try {
        $pdo = new PDO("mysql:host=$host;port=3307;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) { exit('DB接続エラー'); }
}

// ログアウト処理
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: 001_index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM mst_user WHERE USER_ID = ?");
    $stmt->execute([$_POST['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['PASSWORD'] === $_POST['password']) {
        $_SESSION['user_id'] = $user['USER_ID'];
        $_SESSION['name'] = $user['NAME'];
        $_SESSION['role'] = $user['ROLE'];
        header('Location: ' . ($user['ROLE'] == 1 ? '002_teacher_dashboard.php' : '005_student_dashboard.php'));
        exit;
    } else { $error = 'IDまたはパスワードが違います'; }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - 出席管理</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">出席管理システム</h2>
        <?php if ($error): ?><p class="text-red-500 mb-4 text-center"><?= $error ?></p><?php endif; ?>
        <form method="post">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">ユーザーID</label>
                <input type="text" name="user_id" class="w-full border p-3 rounded focus:outline-blue-500" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">パスワード</label>
                <input type="password" name="password" class="w-full border p-3 rounded focus:outline-blue-500" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-lg font-bold hover:bg-blue-700 transition">ログイン</button>
        </form>
    </div>
</body>
</html>