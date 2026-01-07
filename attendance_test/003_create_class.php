<?php
session_start();
require_once 'db_connect.php';
if ($_SESSION['role'] != 1) exit;

$msg = '';

// 作成ボタンが押されたときだけの処理にする
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $class_name = $_POST['class_name'];
    $date = $_POST['date'];
    $code = $_POST['attendance_code'];
    
    if (preg_match('/^[A-Z0-9]{6}$/', $code)) {
        // IPアドレスベースのURLに変更（スマホで読み取るため）
        $url = "http://". $_SERVER['SERVER_NAME'] ."/attendance_test/student_dashboard.php?code=" . $code;
        
        $stmt = $pdo->prepare("INSERT INTO tbl_class (CLASS_NAME, DATE, TIME, ATTENDANCE_CODE, URL, USER_ID) VALUES (?, ?, NOW(), ?, ?, ?)");
        $stmt->execute([$class_name, $date, $code, $url, $_SESSION['user_id']]);
        
        $class_id = $pdo->lastInsertId();
        header("Location: class_created.php?id=$class_id");
        exit;
    } else {
        $msg = "出席コードは半角英数字6桁にしてください";
    }
}
require_once 'header.php';
?>
<div class="w-full max-w-lg bg-white p-8 rounded shadow mx-auto">
    <h2 class="text-2xl font-bold mb-4 text-center">出席フォーム作成</h2>
    <p class="mb-4 text-center">教員名: <?= htmlspecialchars($_SESSION['name']) ?></p>
    <?php if($msg): ?><p class="text-red-500 text-center mb-2"><?= $msg ?></p><?php endif; ?>
    
    <form method="post">
        <label class="block mb-2 font-bold">授業名</label>
        <input type="text" name="class_name" class="w-full border p-2 mb-4 rounded" required>
        
        <label class="block mb-2 font-bold">日付</label>
        <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="w-full border p-2 mb-4 rounded" required>
        
        <label class="block mb-2 font-bold">出席コード (英数字6桁)</label>
        <div class="flex gap-2 mb-6">
            <input type="text" id="code_input" name="attendance_code" class="w-full border p-2 rounded text-center tracking-widest font-bold" maxlength="6" required>
            <button type="button" id="gen_btn" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300 whitespace-nowrap">自動生成</button>
        </div>
        
        <button type="submit" name="create" class="w-full bg-blue-600 text-white p-3 rounded font-bold hover:bg-blue-700">出席フォームを作成</button>
    </form>
    <a href="teacher_dashboard.php" class="block text-center mt-4 text-blue-500">戻る</a>
</div>

<script>
document.getElementById('gen_btn').addEventListener('click', function() {
    const chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    let result = '';
    for (let i = 0; i < 6; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('code_input').value = result;
});
</script>
</body></html>