<?php
session_start();
require_once 'db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    // ユーザー認証
    $stmt = $pdo->prepare("SELECT * FROM mst_user WHERE USER_ID = :uid");
    $stmt->bindValue(':uid', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['PASSWORD'] === $password) {
        $_SESSION['user_id'] = $user['USER_ID'];
        $_SESSION['name'] = $user['NAME'];
        $_SESSION['role'] = $user['ROLE'];

        // ロールによる分岐 (仕様: 0=出席フォーム, 1=ダッシュボード)
        if ($user['ROLE'] == 1) {
            header('Location: teacher_dashboard.php');
        } else {
            header('Location: student_dashboard.php'); // 出席フォーム画面
        }
        exit;
    } else {
        $error = 'ユーザーIDまたはパスワードが違います';
    }
}
require_once 'header.php';
?>
<div class="bg-white p-8 rounded shadow-md w-96">
    <h2 class="text-2xl font-bold mb-6 text-center">出席管理システム</h2>
    <?php if ($error): ?><p class="text-red-500 mb-4"><?= $error ?></p><?php endif; ?>
    <form method="post">
        <label class="block mb-2 font-bold">ユーザーID</label>
        <input type="text" name="user_id" class="w-full border p-2 mb-4 rounded" placeholder="ユーザーIDを入力" required>
        
        <label class="block mb-2 font-bold">パスワード</label>
        <input type="password" name="password" class="w-full border p-2 mb-6 rounded" placeholder="パスワードを入力" required>
        
        <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">ログイン</button>
    </form>
</div>
</body></html>