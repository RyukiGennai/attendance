<?php
require 'db_connect.php';
checkAuth();
$classId = $_GET['id'];
// 授業情報の取得
$stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE CLASS_ID = ?");
$stmt->execute([$classId]);
$class = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>リアルタイム状況</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold mb-4"><?= htmlspecialchars($class['CLASS_NAME']) ?> - 出席状況</h2>
        <div class="bg-white rounded shadow p-4 mb-4 flex justify-between">
            <div class="text-3xl font-bold" id="count">0名</div>
            <a href="teacher_dashboard.php" class="text-gray-500 hover:text-gray-900">ダッシュボードへ戻る</a>
        </div>
        
        <div id="list" class="grid grid-cols-1 md:grid-cols-2 gap-3">
            </div>
    </div>

    <script>
        const classId = <?= json_encode($classId) ?>;
        
        function fetchStatus() {
            fetch(`api_get_attendance.php?class_id=${classId}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('count').textContent = data.length + "名";
                    const list = document.getElementById('list');
                    list.innerHTML = data.map(row => `
                        <div class="bg-white p-3 rounded shadow flex justify-between items-center animate-pulse-once">
                            <div>
                                <div class="font-bold">${row.studentName}</div>
                                <div class="text-xs text-gray-400">${row.studentId}</div>
                            </div>
                            <span class="px-2 py-1 rounded text-xs ${row.ATTENDANCE_STATUS === '出席' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}">
                                ${row.ATTENDANCE_STATUS}
                            </span>
                        </div>
                    `).join('');
                });
        }

        // 3秒ごとに更新
        setInterval(fetchStatus, 3000);
        fetchStatus();
    </script>
</body>
</html>