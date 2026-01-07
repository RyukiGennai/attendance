<?php
session_start();
require_once 'header.php';
?>
<div class="w-full max-w-md px-4 mb-4 text-right">
    <a href="logout.php" class="text-red-500 hover:underline text-sm">ログアウト</a>
</div>

<div class="w-full max-w-md bg-white p-8 rounded shadow text-center mx-auto">
    <h2 class="text-2xl font-bold mb-4 text-green-600">出席が完了しました</h2>
    <p class="mb-8">名前: <?= htmlspecialchars($_SESSION['name']) ?></p>
    <a href="student_dashboard.php" class="block w-full bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">トップへ戻る</a>
</div>
</body></html>