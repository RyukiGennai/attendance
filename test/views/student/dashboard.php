<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ここから既存のコードを続けてください
$user = $_SESSION['user'] ?? null;
?>
<div class="max-w-md mx-auto bg-white rounded-xl shadow-lg overflow-hidden fade-in">
    <div class="p-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-b">
        <h2 class="text-xl font-bold text-emerald-800 flex items-center gap-2"><i data-lucide="graduation-cap"></i> 学生ダッシュボード</h2>
        <div class="mt-4">
            <div class="text-lg font-bold"><?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES) ?> さん</div>
            <div class="text-sm text-gray-500">学籍番号: <?= htmlspecialchars($user['studentId'] ?? '', ENT_QUOTES) ?></div>
        </div>
        <div class="mt-2 inline-block px-2 py-1 bg-amber-50 text-amber-700 text-xs rounded border border-amber-200">
            <i data-lucide="alert-circle" class="inline w-3 h-3"></i> 提出期限: 作成から15分以内
        </div>
    </div>
    <div class="p-6">
        <form action="actions.php" method="post" class="space-y-6">
            <input type="hidden" name="action" value="submit_attendance">
            <div>
                <label class="block font-bold mb-2">出席コード</label>
                <input type="text" name="studentCode" class="w-full p-4 text-center text-2xl font-mono border-2 border-gray-200 rounded-lg tracking-widest uppercase focus:border-emerald-500 outline-none" placeholder="ABC123" maxlength="6" required>
            </div>
            <button type="submit" class="w-full py-4 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 shadow-lg flex items-center justify-center gap-2">
                <i data-lucide="check-circle"></i> 出席を送信
            </button>
        </form>
        <a href="html.php?page=student/history" class="w-full mt-4 py-2 text-gray-500 hover:text-gray-700 text-sm inline-block text-center">過去の履歴を見る</a>
    </div>
</div>