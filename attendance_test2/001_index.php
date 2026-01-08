<?php
require_once 'db_connect.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM mst_user WHERE USER_ID = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if ($user && $user['PASSWORD'] === $password) {
        $_SESSION['user_id'] = $user['USER_ID']; 
        $_SESSION['name'] = $user['NAME'];       
        $_SESSION['role'] = $user['ROLE'];       
        if ($user['ROLE'] == 1) {
            header('Location: 002_teacher_dashboard.php');
        } else {
            header('Location: 005_student_dashboard.php');
        }
        exit;
        
    } else {
        $error = 'ユーザーIDまたはパスワードが違います';
    }
}

require_once 'header.php';
?>

<div class="bg-white p-8 rounded shadow-md w-96 mx-auto mt-20">
    <h2 class="text-2xl font-bold mb-6 text-center">出席管理システム</h2>
    
    <?php if ($error): ?><p class="text-red-500 mb-4"><?= $error ?></p><?php endif; ?>
    
    <form method="post" class="space-y-4">
        <div>
            <label class="block mb-1 font-bold">ユーザーID</label>
            <input type="text" name="user_id" class="w-full border p-2 rounded" required>
        </div>
        <div>
            <label class="block mb-1 font-bold">パスワード</label>
            <input type="password" name="password" class="w-full border p-2 rounded" required>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded font-bold hover:bg-blue-700">ログイン</button>
    </form>
</div>