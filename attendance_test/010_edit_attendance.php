<?php
require_once 'db_connect.php';
$id = $_GET['id'] ?? null;
$is_new = (isset($_GET['action']) && $_GET['action'] === 'new');
$msg = '';
$data = ['STUDENT_NUMBER' => '', 'DATE' => date('Y-m-d'), 'CLASS_NAME' => '', 'ATTENDANCE_STATUS' => '出席'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s_num = $_POST['student_number'];
    $date = $_POST['date'];
    $c_name = $_POST['class_name'];
    $status = $_POST['status'];
    $u_stmt = $pdo->prepare(
        "SELECT USER_ID FROM mst_user WHERE STUDENT_NUMBER = ?"
    );
    $u_stmt->execute([$s_num]);
    $user = $u_stmt->fetch();

    if (!$user) {
        $msg = "エラー：学籍番号「{$s_num}」が見つかりません。名簿を確認してください。";
    } else {
        $c_stmt = $pdo->prepare(
            "SELECT CLASS_ID FROM tbl_class WHERE DATE = ? AND CLASS_NAME = ?"
        );
        $c_stmt->execute([$date, $c_name]);
        $class = $c_stmt->fetch();
        if (!$class) {
            $temp_code = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
            $ins_c = $pdo->prepare(
                "INSERT INTO tbl_class (CLASS_NAME, DATE, TIME, ATTENDANCE_CODE, USER_ID) VALUES (?, ?, NOW(), ?, ?)"
            );
            $ins_c->execute([$c_name, $date, $temp_code, $_SESSION['user_id']]);
            $class_id = $pdo->lastInsertId();
        } else {
            $class_id = $class['CLASS_ID'];
        }
        if ($is_new) {
            $sql = "INSERT INTO tbl_attendance_status (USER_ID, CLASS_ID, ATTENDANCE_STATUS, TIMESTAMP) VALUES (?, ?, ?, NOW())";
            $pdo->prepare($sql)->execute([$user['USER_ID'], $class_id, $status]);
        } else {
            $sql = "UPDATE tbl_attendance_status SET USER_ID = ?, CLASS_ID = ?, ATTENDANCE_STATUS = ? WHERE ATTENDANCE_ID = ?";
            $pdo->prepare($sql)->execute([$user['USER_ID'], $class_id, $status, $id]);
        }
        header('Location: 009_attendance_list.php');
        exit;
    }
}
if ($id && !$is_new) {
    $stmt = $pdo->prepare(
        "SELECT a.*, u.STUDENT_NUMBER, c.CLASS_NAME, c.DATE 
    FROM tbl_attendance_status a 
    JOIN mst_user u ON a.USER_ID = u.USER_ID 
    JOIN tbl_class c ON a.CLASS_ID = c.CLASS_ID 
    WHERE a.ATTENDANCE_ID = ?"
    );
    $stmt->execute([$id]);
    $data = $stmt->fetch();
}
require_once 'header.php';
?>

<div class="max-w-lg mx-auto bg-white p-8 rounded shadow-lg mt-10">
    <h2 class="text-xl font-bold mb-6"><?= $is_new ? '新規記録の追加' : '出席記録の編集' ?></h2>

    <?php if ($msg): ?><p class="text-red-500 mb-4 font-bold"><?= $msg ?></p><?php endif; ?>

    <form method="post" class="space-y-4">
        <div>
            <label class="block font-bold mb-1">学籍番号</label>
            <input type="text" name="student_number" value="<?= htmlspecialchars($data['STUDENT_NUMBER']) ?>" class="w-full border p-2 rounded" placeholder="例: K000C0000" required>
        </div>

        <div>
            <label class="block font-bold mb-1">日付</label>
            <input type="date" name="date" value="<?= htmlspecialchars($data['DATE']) ?>" class="w-full border p-2 rounded" required>
        </div>

        <div>
            <label class="block font-bold mb-1">授業名</label>
            <input type="text" name="class_name" value="<?= htmlspecialchars($data['CLASS_NAME']) ?>" class="w-full border p-2 rounded" placeholder="例: プログラミング演習" required>
        </div>

        <div>
            <label class="block font-bold mb-1">状況</label>
            <select name="status" class="w-full border p-2 rounded">
                <option value="出席" <?= $data['ATTENDANCE_STATUS'] == '出席' ? 'selected' : '' ?>>出席</option>
                <option value="遅刻" <?= $data['ATTENDANCE_STATUS'] == '遅刻' ? 'selected' : '' ?>>遅刻</option>
                <option value="欠席" <?= $data['ATTENDANCE_STATUS'] == '欠席' ? 'selected' : '' ?>>欠席</option>
            </select>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded font-bold hover:bg-blue-700">保存する</button>
            <a href="009_attendance_list.php" class="block text-center text-gray-400 mt-4 hover:underline">キャンセル</a>
        </div>
    </form>
</div>