<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ここから既存のコードを続けてください
$forms = get_attendance_forms();
$active = end($forms);
?>
<div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in">
    <div class="p-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-b flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold flex items-center gap-2 text-emerald-800">
                <span class="relative flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span></span>
                リアルタイム出席状況
            </h2>
            <p class="text-sm text-gray-500 mt-1">最新フォーム | <?= htmlspecialchars($active['className'] ?? '—', ENT_QUOTES) ?></p>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <div class="text-2xl font-bold text-gray-800"><?= count($active['attendanceList'] ?? []) ?>名</div>
                <div class="text-xs text-gray-500">出席済み</div>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-center w-16">No</th>
                    <th class="p-3">学籍番号</th>
                    <th class="p-3">氏名</th>
                    <th class="p-3">時刻</th>
                    <th class="p-3">状況</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($active['attendanceList'])): ?>
                    <tr><td colspan="5" class="py-12 text-center text-gray-400">待機中...</td></tr>
                <?php else: foreach ($active['attendanceList'] as $i => $s): ?>
                    <tr class="border-b">
                        <td class="p-3 text-center"><?= $i+1 ?></td>
                        <td class="p-3 font-mono"><?= htmlspecialchars($s['studentId'] ?? '', ENT_QUOTES) ?></td>
                        <td class="p-3"><?= htmlspecialchars($s['studentName'] ?? '', ENT_QUOTES) ?></td>
                        <td class="p-3 text-sm text-gray-500"><?= isset($s['timestamp']) ? date('H:i:s', $s['timestamp']) : '' ?></td>
                        <td class="p-3"><span class="px-2 py-1 rounded text-xs <?= ($s['status'] ?? '') === '出席' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>"><?= htmlspecialchars($s['status'] ?? '', ENT_QUOTES) ?></span></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <div class="p-4 bg-gray-50 border-t flex justify-between">
        <a href="html.php?page=teacher/dashboard" class="px-4 py-2 border bg-white rounded hover:bg-gray-50">戻る</a>
    </div>
</div>