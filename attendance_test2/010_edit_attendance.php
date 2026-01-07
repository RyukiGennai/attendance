<?php
require_once 'db_connect.php';
$pdo = getDB();
$id = $_GET['id'] ?? null;
$is_new = (isset($_GET['action']) && $_GET['action'] === 'new');
$msg = ''; $data = ['STUDENT_NUMBER' => '', 'ATTENDANCE_CODE' => '', 'ATTENDANCE_STATUS' => '出席'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s_num = $_POST['student_number'];
    $a_code = $_POST['attendance_code'];
    $status = $_POST['status'];

    $u = $pdo->prepare("SELECT USER_ID FROM mst_user WHERE STUDENT_NUMBER = ?"); $u->execute([$s_num]); $user = $u->fetch();
    $c = $pdo->prepare("SELECT CLASS_ID FROM tbl_class WHERE ATTENDANCE_CODE = ?"); $c->execute([$a_code]); $class = $c->fetch();

    if (!$user) { $msg = "学籍番号が見つかりません"; }
    elseif (!$class) { $msg = "無効な出席コードです"; }
    else {
        if ($is_new) {
            $pdo->prepare("INSERT INTO tbl_attendance_status (USER_ID, CLASS_ID, ATTENDANCE_STATUS, TIMESTAMP) VALUES (?, ?, ?, NOW())")->execute([$user['USER_ID'], $class['CLASS_ID'], $status]);
        } else {
            $pdo->prepare("UPDATE tbl_attendance_status SET USER_ID = ?, CLASS_ID = ?, ATTENDANCE_STATUS = ? WHERE ATTENDANCE_ID = ?")->execute([$user['USER_ID'], $class['CLASS_ID'], $status, $id]);
        }
        header('Location: 009_attendance_list.php'); exit;
    }
}
if ($id && !$is_new) {
    $stmt = $pdo->prepare("SELECT a.*, u.STUDENT_NUMBER, c.ATTENDANCE_CODE FROM tbl_attendance_status a JOIN mst_user u ON a.USER_ID = u.USER_ID JOIN tbl_class c ON a.CLASS_ID = c.CLASS_ID WHERE a.ATTENDANCE_ID = ?");
    $stmt->execute([$id]); $data = $stmt->fetch();
}
require_once 'header.php';
?>
<div class="max-w-lg w-full bg-white p-8 rounded shadow mx-auto mt-10">
    <h2 class="text-xl font-bold mb-6"><?= $is_new ? '新規追加' : '編集' ?></h2>
    <?php if($msg): ?><p class="text-red-500 mb-4 font-bold"><?= $msg ?></p><?php endif; ?>
    <form method="post" class="space-y-4">
        <div><label class="block font-bold">学籍番号</label><input type="text" name="student_number" value="<?= htmlspecialchars($data['STUDENT_NUMBER']) ?>" class="w-full border p-2 rounded" required></div>
        <div><label class="block font-bold">出席コード (6桁)</label><input type="text" name="attendance_code" value="<?= htmlspecialchars($data['ATTENDANCE_CODE']) ?>" class="w-full border p-2 rounded" required></div>
        <div><label class="block font-bold">状況</label><select name="status" class="w-full border p-2 rounded"><option value="出席" <?= $data['ATTENDANCE_STATUS']=='出席'?'selected':'' ?>>出席</option><option value="遅刻" <?= $data['ATTENDANCE_STATUS']=='遅刻'?'selected':'' ?>>遅刻</option><option value="欠席" <?= $data['ATTENDANCE_STATUS']=='欠席'?'selected':'' ?>>欠席</option></select></div>
        <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded font-bold">保存</button>
        <a href="009_attendance_list.php" class="block text-center text-gray-400 mt-2">キャンセル</a>
    </form>
</div>