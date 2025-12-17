<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ここから既存のコードを続けてください
// Form to create attendance
$defaultDate = date('Y-m-d');
$generated = generate_code();
?>
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-6 fade-in">
    <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-blue-600">
        <i data-lucide="plus-circle"></i> 出席フォーム作成
    </h2>
    <form action="actions.php" method="post" class="space-y-6">
        <input type="hidden" name="action" value="create_form">
        <div>
            <label class="block text-sm font-medium mb-1">授業名</label>
            <input type="text" name="className" value="情報システム基礎" class="w-full p-2 border rounded" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">日付</label>
            <input type="date" name="date" value="<?= htmlspecialchars($defaultDate, ENT_QUOTES) ?>" class="w-full p-2 border rounded" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">出席コード</label>
            <div class="flex gap-2">
                <input type="text" name="generatedCode" value="<?= htmlspecialchars($generated, ENT_QUOTES) ?>" readonly class="w-full p-2 border rounded bg-gray-50 font-mono tracking-widest">
                <button type="button" onclick="location.reload()" class="px-4 py-2 border rounded hover:bg-gray-50"><i data-lucide="sparkles" class="h-4 w-4"></i></button>
            </div>
        </div>
        <div class="flex justify-between pt-4 border-t">
            <a href="html.php?page=teacher/dashboard" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">戻る</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">作成する</button>
        </div>
    </form>
</div>