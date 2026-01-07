<?php
session_start();
require_once '001_index.php';

$id = $_GET['id'] ?? null;
$is_new = (isset($_GET['action']) && $_GET['action'] === 'new');
$msg = '';
$data = ['STUDENT_NUMBER' => '', 'CLASS_ID' => '', 'ATTENDANCE_STATUS' => '出席'];

// 保存処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s_num = $_POST['student_number'];
    $class_id = $_POST['class_id'];
    $status = $_POST['status'];

    // 学籍番号からユーザーIDを検索
    $stmt = $pdo->prepare("SELECT USER_ID FROM mst_user WHERE STUDENT_NUMBER = ?");
    $stmt->execute([$s_num]);
    $user = $stmt->fetch();

    if (!$user) {
        $msg = "エラー：学籍番号「{$s_num}」が見つかりません。";
    } else {
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

// 既存データの取得
if ($id && !$is_new) {
    $stmt = $pdo->prepare("SELECT a.*, u.STUDENT_NUMBER FROM tbl_attendance_status a JOIN mst_user u ON a.USER_ID = u.USER_ID WHERE a.ATTENDANCE_ID = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
}

// 授業リストの取得（プルダウン用）
$classes = $pdo->query("SELECT CLASS_ID, CLASS_NAME, DATE FROM tbl_class ORDER BY DATE DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8"><title>出席情報の編集</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 flex justify-center">
    <div class="max-w-lg w-full bg-white p-8 rounded-xl shadow-lg">
        <h2 class="text-xl font-bold mb-6"><?= $is_new ? '新規記録の追加' : '出席記録の編集' ?></h2>
        
        <?php if($msg): ?><p class="text-red-500 mb-4 font-bold"><?= $msg ?></p><?php endif; ?>

        <form method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-bold mb-1">学籍番号</label>
                <input type="text" name="student_number" value="<?= htmlspecialchars($data['STUDENT_NUMBER']) ?>" class="w-full border p-2 rounded" placeholder="例: STU001" required>
                <p class="text-xs text-gray-400">※保存時に名簿から自動で名前を紐づけます</p>
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">対象の授業</label>
                <select name="class_id" class="w-full border p-2 rounded" required>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['CLASS_ID'] ?>" <?= $data['CLASS_ID'] == $c['CLASS_ID'] ? 'selected' : '' ?>>
                            [<?= $c['DATE'] ?>] <?= htmlspecialchars($c['CLASS_NAME']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">出席状況</label>
                <select name="status" class="w-full border p-2 rounded">
                    <option value="出席" <?= $data['ATTENDANCE_STATUS'] == '出席' ? 'selected' : '' ?>>出席</option>
                    <option value="遅刻" <?= $data['ATTENDANCE_STATUS'] == '遅刻' ? 'selected' : '' ?>>遅刻</option>
                    <option value="欠席" <?= $data['ATTENDANCE_STATUS'] == '欠席' ? 'selected' : '' ?>>欠席</option>
                </select>
            </div>

            <div class="pt-4 space-y-2">
                <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded font-bold hover:bg-blue-700">保存する</button>
                <a href="009_attendance_list.php" class="block text-center text-gray-500 hover:underline">キャンセル</a>
            </div>
        </form>
    </div>
</body>
</html>