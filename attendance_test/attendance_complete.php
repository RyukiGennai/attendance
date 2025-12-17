<?php
session_start();
require_once 'header.php';
?>
<div class="w-full max-w-md bg-white p-8 rounded shadow text-center">
    <h2 class="text-2xl font-bold mb-4 text-green-600">出席が完了しました</h2>
    <p class="mb-8">名前: <?= htmlspecialchars($_SESSION['name']) ?></p>
    <a href="student_dashboard.php" class="bg-blue-600 text-white px-6 py-2 rounded">トップへ戻る</a>
</div>
</body></html>