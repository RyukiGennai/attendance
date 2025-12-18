<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ここから既存のコードを続けてください

// Show last created form
$lastId = $_SESSION['last_created_form_id'] ?? null;
$form = null;
if ($lastId) {
    foreach (get_attendance_forms() as $f) {
        if ($f['id'] === $lastId) { $form = $f; break; }
    }
}
?>
<div class="max-w-md mx-auto bg-white rounded-xl shadow-xl p-8 text-center fade-in">
    <div class="inline-block p-3 bg-green-100 rounded-full mb-4">
        <i data-lucide="check-circle" class="h-8 w-8 text-green-600"></i>
    </div>
    <h2 class="text-xl font-bold text-green-700 mb-2">作成完了</h2>
    <p class="text-gray-500 mb-6">生徒に以下のコードを共有してください</p>
    <div class="bg-indigo-50 p-6 rounded-xl border-2 border-indigo-200 mb-6">
        <span class="text-4xl font-mono font-bold text-indigo-700 tracking-widest"><?= htmlspecialchars($form['code'] ?? '', ENT_QUOTES) ?></span>
    </div>
    <a href="html.php?page=teacher/dashboard" class="w-full py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 inline-block text-center">ダッシュボードへ戻る</a>
</div>