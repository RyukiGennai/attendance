<?php
session_start();
require_once 'db_connect.php';

// 削除処理
if (isset($_POST['delete'])) {
    $pdo->prepare("DELETE FROM tbl_attendance_status WHERE ATTENDANCE_ID = ?")->execute([$_POST['id']]);
}
// 追加ボタン処理 (ダミー作成して編集へ)
if (isset($_POST['add_new'])) {
    // 便宜上、最初の授業IDで空レコード作成
    $stmt = $pdo->prepare("INSERT INTO tbl_attendance_status (USER_ID, CLASS_ID, ATTENDANCE_STATUS) VALUES ('', 0, '出席')");
    $stmt->execute();
    $new_id = $pdo->lastInsertId();
    header("Location: edit_attendance.php?id=$new_id");
    exit;
}

// 検索条件
$date_filter = $_GET['date'] ?? '';
$name_filter = $_GET['name'] ?? '';

$sql = "SELECT a.*, u.NAME, u.STUDENT_NUMBER, c.CLASS_NAME, c.DATE 
        FROM tbl_attendance_status a 
        LEFT JOIN mst_user u ON a.USER_ID = u.USER_ID 
        LEFT JOIN tbl_class c ON a.CLASS_ID = c.CLASS_ID 
        WHERE 1=1";
$params = [];

if ($date_filter) { $sql .= " AND c.DATE = ?"; $params[] = $date_filter; }
if ($name_filter) { $sql .= " AND u.NAME LIKE ?"; $params[] = "%$name_filter%"; }

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$list = $stmt->fetchAll();

require_once 'header.php';
?>
<div class="w-full max-w-5xl px-4">
    <h2 class="text-2xl font-bold mb-4">出席リスト管理</h2>
    <p class="mb-4">担当: <?= htmlspecialchars($_SESSION['name']) ?></p>

    <form class="bg-white p-4 rounded shadow mb-6 flex gap-4 items-end">
        <div><label>日付</label><input type="date" name="date" value="<?= $date_filter ?>" class="border p-1 block"></div>
        <div><label>名前</label><input type="text" name="name" value="<?= $name_filter ?>" class="border p-1 block" placeholder="学生名"></div>
        <button class="bg-blue-500 text-white px-4 py-1 rounded">絞り込み</button>
    </form>

    <table class="w-full bg-white shadow rounded text-sm">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2">日付</th>
                <th class="p-2">授業名</th>
                <th class="p-2">学籍番号</th>
                <th class="p-2">名前</th>
                <th class="p-2">状況</th>
                <th class="p-2">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $row): ?>
            <tr class="border-b text-center">
                <td class="p-2"><?= $row['DATE'] ?></td>
                <td class="p-2"><?= $row['CLASS_NAME'] ?></td>
                <td class="p-2"><?= $row['STUDENT_NUMBER'] ?></td>
                <td class="p-2"><?= $row['NAME'] ?></td>
                <td class="p-2"><?= $row['ATTENDANCE_STATUS'] ?></td>
                <td class="p-2 flex justify-center gap-2">
                    <a href="edit_attendance.php?id=<?= $row['ATTENDANCE_ID'] ?>" class="bg-green-500 text-white px-2 py-1 rounded">変更</a>
                    <form method="post" onsubmit="return confirm('削除しますか？')">
                        <input type="hidden" name="id" value="<?= $row['ATTENDANCE_ID'] ?>">
                        <button type="submit" name="delete" class="bg-red-500 text-white px-2 py-1 rounded">削除</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="mt-4 flex justify-between">
        <a href="teacher_dashboard.php" class="text-blue-500">戻る</a>
        <form method="post"><button name="add_new" class="bg-blue-600 text-white px-4 py-2 rounded">＋ 追加</button></form>
    </div>
</div>
</body></html>