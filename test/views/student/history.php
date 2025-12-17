<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ここから既存のコードを続けてください
$user = $_SESSION['user'] ?? null;
$myId = $user['studentId'] ?? null;
$myRecords = array_filter(get_records(), function($r) use($myId){
    return ($r['studentId'] ?? '') === $myId;
});
?>
<div class="max-w-md mx-auto bg-white rounded-xl shadow-lg fade-in">
    <div class="p-4 border-b bg-gray-50 flex items-center gap-2">
        <i data-lucide="history"></i> <span class="font-bold">出席履歴</span>
    </div>
    <div class="p-4">
        <table class="w-full text-left">
            <thead><tr class="text-sm text-gray-500"><th class="p-2">日付</th><th class="p-2">授業</th><th class="p-2">状況</th></tr></thead>
            <tbody>
                <?php if (empty($myRecords)): ?>
                    <tr><td colspan="3" class="py-8 text-center text-gray-400">履歴がありません</td></tr>
                <?php else: foreach ($myRecords as $r): ?>
                    <tr class="border-b">
                        <td class="p-3 text-sm"><?= htmlspecialchars($r['date'], ENT_QUOTES) ?></td>
                        <td class="p-3 text-sm"><?= htmlspecialchars($r['className'], ENT_QUOTES) ?></td>
                        <td class="p-3"><span class="px-2 py-1 rounded text-xs bg-gray-100"><?= htmlspecialchars($r['status'], ENT_QUOTES) ?></span></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
        <a href="html.php?page=student/dashboard" class="w-full mt-6 py-2 border rounded hover:bg-gray-50 inline-block text-center">戻る</a>
    </div>
</div>