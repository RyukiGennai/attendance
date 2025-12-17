<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ここから既存のコードを続けてください
// Teacher dashboard
?>
<div class="fade-in">
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 mb-6">
        <h2 class="text-xl font-bold flex items-center gap-2 mb-4 text-indigo-700">
            <i data-lucide="sparkles"></i> 教員ダッシュボード
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="/test/html.php?page=teacher/create_form" class="p-6 bg-blue-500 text-white rounded-xl shadow hover:bg-blue-600 transition flex flex-col items-center gap-2">
                <i data-lucide="plus-circle" class="h-8 w-8"></i>
                <span class="font-bold text-lg">出席フォーム作成</span>
                <span class="text-sm opacity-90">新しい授業を開始</span>
            </a>
            <a href="/test/html.php?page=teacher/live_status" class="p-6 bg-emerald-500 text-white rounded-xl shadow hover:bg-emerald-600 transition flex flex-col items-center gap-2">
                <i data-lucide="list-check" class="h-8 w-8"></i>
                <span class="font-bold text-lg">リアルタイム状況</span>
                <span class="text-sm opacity-90">現在の出席を確認</span>
            </a>
            <a href="/test/html.php?page=teacher/attendance_list" class="md:col-span-2 p-6 bg-purple-500 text-white rounded-xl shadow hover:bg-purple-600 transition flex flex-col items-center gap-2">
                <i data-lucide="file-text" class="h-8 w-8"></i>
                <span class="font-bold text-lg">出席リスト管理</span>
                <span class="text-sm opacity-90">過去の記録を編集・検索</span>
            </a>
        </div>
    </div>
</div>