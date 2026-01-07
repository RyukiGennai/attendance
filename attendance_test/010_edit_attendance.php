<?php
session_start();
require_once 'db_connect.php';

$id = $_GET['id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 学籍番号からユーザーIDを取得して更新
    $s_num = $_POST['student_number'];
    $stmt = $pdo->prepare("SELECT USER_ID FROM mst_user WHERE STUDENT_NUMBER = ?");
    $stmt->execute([$s_num]);
    $u = $stmt->fetch();
    
    if ($u) {
        $user_id = $u['USER_ID'];
        $status = $_POST['status'];
        
        $pdo->prepare("UPDATE tbl_attendance_status SET USER_ID = ?, ATTENDANCE_STATUS = ? WHERE ATTENDANCE_ID = ?")
            ->execute([$user_id, $status, $id]);
        header('Location: attendance_list.php');
        exit;
    } else {
        $msg = "その学籍番号の学生は見つかりません";
    }
}

// データ取得
$stmt = $pdo->prepare("SELECT a.*, u.NAME, u.STUDENT_NUMBER, c.CLASS_NAME, c.DATE 
                       FROM tbl_attendance_status a 
                       LEFT JOIN mst_user u ON a.USER_ID = u.USER_ID 
                       LEFT JOIN tbl_class c ON a.CLASS_ID = c.CLASS_ID 
                       WHERE a.ATTENDANCE_ID = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

require_once 'header.php';
?>
<div class="w-full max-w-lg bg-white p-8 rounded shadow">
    <h2 class="text-xl font-bold mb-4">出席の編集</h2>
    <p class="mb-4 text-sm text-gray-500">出席記録の内容を変更できます</p>
    <?php if($msg): ?><p class="text-red-500"><?= $msg ?></p><?php endif; ?>

    <form method="post">
        <label class="block mb-2">学籍番号</label>
        <input type="text" name="student_number" value="<?= $data['STUDENT_NUMBER'] ?? '' ?>" class="w-full border p-2 mb-4" required>
        
        <label class="block mb-2">名前 (自動表示)</label>
        <div class="p-2 bg-gray-100 mb-4"><?= $data['NAME'] ?? '---' ?></div>
        
        <label class="block mb-2">授業名</label>
        <div class="p-2 bg-gray-100 mb-4"><?= $data['CLASS_NAME'] ?? '(未設定)' ?></div>
        
        <label class="block mb-2">日付</label>
        <div class="p-2 bg-gray-100 mb-4"><?= $data['DATE'] ?? '(未設定)' ?></div>
        
        <label class="block mb-2">出席状況</label>
        <select name="status" class="w-full border p-2 mb-6">
            <?php foreach(['出席','遅刻','早退','欠席'] as $s): ?>
                <option value="<?= $s ?>" <?= ($data['ATTENDANCE_STATUS'] == $s) ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded mb-2">更新する</button>
    </form>
    <a href="attendance_list.php" class="block text-center text-gray-500">戻る</a>
</div>
</body></html>