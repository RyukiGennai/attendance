<?php
session_start();
require_once 'db_connect.php';
if ($_SESSION['role'] != 0) exit;

$stmt = $pdo->prepare("SELECT * FROM mst_user WHERE USER_ID = ?");
$stmt->execute([$_SESSION['user_id']]);
$me = $stmt->fetch();

$msg = '';
$default_code = $_GET['code'] ?? ''; // URLからコード取得

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_code = $_POST['code'];
    if (!preg_match('/^[A-Z0-9]{6}$/', $input_code)) {
        $msg = "コードは半角英数字6桁です";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE ATTENDANCE_CODE = ?");
        $stmt->execute([$input_code]);
        $class = $stmt->fetch();
        
        if ($class) {
            $check = $pdo->prepare("SELECT * FROM tbl_attendance_status WHERE USER_ID = ? AND CLASS_ID = ?");
            $check->execute([$me['USER_ID'], $class['CLASS_ID']]);
            
            if ($check->rowCount() > 0) {
                $msg = "すでに出席済みです";
            } else {
                $ins = $pdo->prepare("INSERT INTO tbl_attendance_status (ATTENDANCE_STATUS, USER_ID, CLASS_ID, TIMESTAMP) VALUES ('出席', ?, ?, NOW())");
                $ins->execute([$me['USER_ID'], $class['CLASS_ID']]);
                header('Location: attendance_complete.php');
                exit;
            }
        } else {
            $msg = "無効な出席コードです";
        }
    }
}
require_once 'header.php';
?>
<div class="w-full max-w-md px-4 mb-4 text-right">
    <a href="logout.php" class="text-red-500 hover:underline text-sm">ログアウト</a>
</div>

<div class="w-full max-w-md bg-white p-6 rounded shadow mx-auto">
    <h2 class="text-xl font-bold mb-4 text-center">出席管理システム</h2>
    
    <div class="bg-gray-50 p-4 mb-6 rounded text-center">
        <p class="text-lg font-bold"><?= htmlspecialchars($me['NAME']) ?></p>
        <p class="text-gray-500">学籍番号: <?= htmlspecialchars($me['STUDENT_NUMBER']) ?></p>
    </div>
    
    <?php if($msg): ?><p class="text-red-500 text-center mb-2 font-bold"><?= $msg ?></p><?php endif; ?>
    
    <form method="post">
        <label class="block mb-2 font-bold">出席コード</label>
        <input type="text" name="code" value="<?= htmlspecialchars($default_code) ?>" class="w-full border p-3 text-center text-xl mb-4 rounded tracking-widest" placeholder="6桁のコード" maxlength="6" required>
        
        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded font-bold hover:bg-blue-700">出席を送信</button>
    </form>
    
    <a href="student_history.php" class="block w-full bg-gray-500 text-white text-center py-2 rounded mt-4 hover:bg-gray-600">過去の出席履歴</a>
</div>
</body></html>