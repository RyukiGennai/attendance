<?php
session_start();
require_once '001_index.php';
$pdo = getDB();
// 本日の最新授業を取得
$stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE DATE = CURRENT_DATE AND USER_ID = ? ORDER BY CLASS_ID DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

$attendees = [];
if ($class) {
    $stmt2 = $pdo->prepare("SELECT a.*, u.NAME, u.STUDENT_NUMBER FROM tbl_attendance_status a JOIN mst_user u ON a.USER_ID = u.USER_ID WHERE a.CLASS_ID = ? ORDER BY a.TIMESTAMP DESC");
    $stmt2->execute([$class['CLASS_ID']]);
    $attendees = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8"><title>リアルタイム状況</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>setTimeout(() => { window.location.reload(); }, 5000); // 5秒ごとに自動更新</script>
</head>
<body class="bg-slate-900 text-white min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-end mb-8 border-b border-slate-700 pb-4">
            <div>
                <h1 class="text-3xl font-black text-emerald-400 uppercase tracking-tighter">Live Status</h1>
                <p class="text-slate-400 font-bold"><?= $class ? htmlspecialchars($class['CLASS_NAME']) : '授業が作成されていません' ?></p>
            </div>
            <div class="text-right">
                <span class="text-5xl font-black text-white"><?= count($attendees) ?></span>
                <span class="text-slate-500 font-bold ml-1">名出席</span>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($attendees as $row): ?>
            <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex justify-between items-center animate-pulse-once">
                <div>
                    <div class="text-slate-400 text-xs font-mono mb-1"><?= $row['STUDENT_NUMBER'] ?></div>
                    <div class="text-lg font-bold"><?= htmlspecialchars($row['NAME']) ?></div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] text-slate-500 mb-1"><?= date('H:i:s', strtotime($row['TIMESTAMP'])) ?></div>
                    <span class="px-2 py-1 rounded text-xs font-bold <?= $row['ATTENDANCE_STATUS'] == '出席' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-yellow-500/10 text-yellow-400' ?>">
                        <?= $row['ATTENDANCE_STATUS'] ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-10 text-center">
            <a href="002_teacher_dashboard.php" class="text-slate-500 hover:text-white transition">ダッシュボードへ戻る</a>
        </div>
    </div>
</body>
</html>