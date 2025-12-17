<?php
require 'db_connect.php';
checkAuth();

$message = '';
$msgType = '';

// 出席送信処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputCode = $_POST['code'];
    $studentId = $_SESSION['user_id'];

    // 1. コードから授業を検索
    $stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE ATTENDANCE_CODE = ? ORDER BY DATE DESC LIMIT 1");
    $stmt->execute([$inputCode]);
    $class = $stmt->fetch();

    if (!$class) {
        $message = '無効なコードです';
        $msgType = 'red';
    } else {
        // 2. 既に出席済みかチェック
        $check = $pdo->prepare("SELECT * FROM tbl_attendance_status WHERE USER_ID = ? AND CLASS_ID = ?");
        $check->execute([$studentId, $class['CLASS_ID']]);
        
        if ($check->rowCount() > 0) {
            $message = 'すでに送信済みです';
            $msgType = 'yellow';
        } else {
            // 3. 遅刻判定 (日付と時間を結合して計算)
            $classStart = strtotime($class['DATE'] . ' ' . $class['TIME']);
            $now = time();
            $diffMinutes = ($now - $classStart) / 60;

            // 15分以上で遅刻判定
            if ($diffMinutes > 15) {
                $status = '遅刻';
            } else {
                $status = '出席';
            }

            // 4. 登録
            $ins = $pdo->prepare("INSERT INTO tbl_attendance_status (ATTENDANCE_STATUS, USER_ID, CLASS_ID, TIMESTAMP, COMMENT) VALUES (?, ?, ?, NOW(), '')");
            $ins->execute([$status, $studentId, $class['CLASS_ID']]);

            // 完了画面へリダイレクト
            header("Location: submit_success.php?status=" . urlencode($status) . "&class=" . urlencode($class['CLASS_NAME']));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>学生ダッシュボード</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    <nav class="bg-white shadow p-4 flex justify-between">
        <span class="font-bold text-green-600">AttendancePro (学生)</span>
        <div><?= htmlspecialchars($_SESSION['name']) ?> <a href="logout.php" class="text-sm text-gray-500 ml-2">ログアウト</a></div>
    </nav>

    <div class="max-w-md mx-auto mt-10 p-6">
        <div class="bg-white p-8 rounded-2xl shadow-lg text-center">
            <h2 class="text-2xl font-bold mb-2">出席コード入力</h2>
            
            <?php if($message): ?>
                <div class="bg-<?= $msgType ?>-100 text-<?= $msgType ?>-700 p-3 rounded mb-4">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="text" name="code" class="w-full text-center text-4xl font-mono border-2 rounded-xl py-4 mb-4" placeholder="0000" maxlength="4" required value="<?= isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '' ?>">
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-xl font-bold hover:bg-indigo-700">出席を送信</button>
            </form>
        </div>
        
        <div class="mt-8 text-center">
            <a href="student_history.php" class="text-indigo-600 font-bold hover:underline">あなたの出席履歴を見る &rarr;</a>
        </div>
    </div>
</body>
</html>