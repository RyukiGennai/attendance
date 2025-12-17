<?php
session_start();
require_once 'db_connect.php';
if ($_SESSION['role'] != 1) exit;

$msg = '';
$code = '';

// 自動生成ボタンまたは作成ボタン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['generate'])) {
        // 英数字6桁生成
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = substr(str_shuffle(str_repeat($chars, 6)), 0, 6);
    } elseif (isset($_POST['create'])) {
        $class_name = $_POST['class_name'];
        $date = $_POST['date'];
        $code = $_POST['attendance_code'];
        
        if (preg_match('/^[A-Z0-9]{6}$/', $code)) {
            $url = "http://localhost/attendance_test/student_dashboard.php?code=" . $code;
            
            $stmt = $pdo->prepare("INSERT INTO tbl_class (CLASS_NAME, DATE, TIME, ATTENDANCE_CODE, URL, USER_ID) VALUES (?, ?, NOW(), ?, ?, ?)");
            $stmt->execute([$class_name, $date, $code, $url, $_SESSION['user_id']]);
            
            // 完了画面へ遷移 (IDを渡す)
            $class_id = $pdo->lastInsertId();
            header("Location: class_created.php?id=$class_id");
            exit;
        } else {
            $msg = "出席コードは半角英数字6桁にしてください";
        }
    }
}
require_once 'header.php';
?>
<div class="w-full max-w-lg bg-white p-8 rounded shadow">
    <h2 class="text-2xl font-bold mb-4 text-center">出席フォーム作成</h2>
    <p class="mb-4 text-center">教員名: <?= htmlspecialchars($_SESSION['name']) ?></p>
    <?php if($msg): ?><p class="text-red-500"><?= $msg ?></p><?php endif; ?>
    
    <form method="post">
        <label class="block mb-2">授業名</label>
        <input type="text" name="class_name" class="w-full border p-2 mb-4" required>
        
        <label class="block mb-2">日付</label>
        <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="w-full border p-2 mb-4" required>
        
        <label class="block mb-2">出席コード (英数字6桁)</label>
        <div class="flex gap-2 mb-6">
            <input type="text" name="attendance_code" value="<?= $code ?>" class="w-full border p-2" maxlength="6" required>
            <button type="submit" name="generate" class="bg-gray-200 px-4 py-2 rounded">自動生成</button>
        </div>
        
        <button type="submit" name="create" class="w-full bg-blue-600 text-white p-3 rounded font-bold">出席フォームを作成</button>
    </form>
    <a href="teacher_dashboard.php" class="block text-center mt-4 text-blue-500">戻る</a>
</div>
</body></html>