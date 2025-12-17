<?php
require 'db_connect.php';
checkAuth();
checkTeacher();

// 授業作成処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $className = $_POST['className'];
    $date = $_POST['classDate']; // YYYY-MM-DD
    $time = date('H:i:s');       // 現在時刻
    $code = rand(1000, 9999);    // 4桁のランダムコード
    $userId = $_SESSION['user_id'];
    
    // QR用URL (実際のサーバーURLに合わせて変更してください)
    $url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/student_dashboard.php?code=" . $code;

    $sql = "INSERT INTO tbl_class (CLASS_NAME, DATE, TIME, ATTENDANCE_CODE, URL, USER_ID) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$className, $date, $time, $code, $url, $userId]);
    
    $newId = $pdo->lastInsertId();
    header("Location: code_share.php?id=" . $newId);
    exit;
}

// 過去の授業一覧取得
$stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE USER_ID = ? ORDER BY DATE DESC, TIME DESC");
$stmt->execute([$_SESSION['user_id']]);
$classes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>教員ダッシュボード</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    <nav class="bg-white shadow p-4 flex justify-between">
        <span class="font-bold text-indigo-600">AttendancePro (教員)</span>
        <div><?= htmlspecialchars($_SESSION['name']) ?> さん | <a href="logout.php" class="text-red-500">ログアウト</a></div>
    </nav>

    <div class="max-w-6xl mx-auto p-6">
        <div class="bg-white p-6 rounded-xl shadow mb-8">
            <h2 class="font-bold mb-4">新しい授業を開始</h2>
            <form method="POST" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-xs text-gray-500">授業名</label>
                    <input type="text" name="className" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-500">日付</label>
                    <input type="date" name="classDate" value="<?= date('Y-m-d') ?>" class="p-2 border rounded" required>
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded">発行</button>
            </form>
        </div>

        <div class="flex justify-between items-center mb-4">
            <h2 class="font-bold">作成した授業</h2>
            <a href="attendance_list.php" class="text-indigo-600 text-sm hover:underline">全ての出席履歴を見る &rarr;</a>
        </div>

        <div class="grid gap-4">
            <?php foreach($classes as $c): ?>
            <div class="bg-white p-4 rounded shadow border-l-4 border-indigo-500 flex justify-between items-center">
                <div>
                    <div class="text-sm text-gray-500"><?= htmlspecialchars($c['DATE']) ?> <?= htmlspecialchars($c['TIME']) ?></div>
                    <div class="font-bold text-lg"><?= htmlspecialchars($c['CLASS_NAME']) ?></div>
                    <div class="text-xs text-gray-400">Code: <?= htmlspecialchars($c['ATTENDANCE_CODE']) ?></div>
                </div>
                <div class="flex gap-2">
                    <a href="live_status.php?id=<?= $c['CLASS_ID'] ?>" class="px-4 py-2 bg-emerald-100 text-emerald-700 rounded text-sm">状況確認</a>
                    <a href="code_share.php?id=<?= $c['CLASS_ID'] ?>" class="px-4 py-2 bg-gray-100 text-gray-700 rounded text-sm">コード表示</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>