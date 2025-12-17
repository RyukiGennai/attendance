<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ここから既存のコードを続けてください
$records = get_records();
$searchDate = $_GET['searchDate'] ?? '';
$searchClass = $_GET['searchClassName'] ?? '';
$searchName = $_GET['searchName'] ?? '';

$filtered = array_filter($records, function($r) use($searchDate,$searchClass,$searchName){
    $matchDate = !$searchDate || strpos($r['date'],$searchDate) !== false;
    $matchClass = !$searchClass || mb_strpos($r['className'],$searchClass) !== false;
    $matchName = !$searchName || mb_strpos($r['studentName'],$searchName) !== false;
    return $matchDate && $matchClass && $matchName;
});
?>
<div class="bg-white rounded-xl shadow-lg border border-gray-100 fade-in">
    <div class="p-6 border-b flex justify-between items-center bg-purple-50">
        <h2 class="text-xl font-bold text-purple-700 flex items-center gap-2"><i data-lucide="file-text"></i> 出席リスト管理</h2>
        <a href="html.php?page=teacher/edit_record" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm flex items-center gap-2"><i data-lucide="plus-circle" class="h-4 w-4"></i> 新規追加</a>
    </div>
    <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 border-b">
        <form method="get" action="html_Version4.php" class="col-span-3 md:col-auto md:flex md:gap-2">
            <input type="hidden" name="page" value="teacher/attendance_list">
            <input type="date" name="searchDate" class="p-2 border rounded" value="<?= htmlspecialchars($searchDate, ENT_QUOTES) ?>">
            <input type="text" name="searchClassName" placeholder="授業名" class="p-2 border rounded" value="<?= htmlspecialchars($searchClass, ENT_QUOTES) ?>">
            <input type="text" name="searchName" placeholder="名前" class="p-2 border rounded" value="<?= htmlspecialchars($searchName, ENT_QUOTES) ?>">
            <button class="px-3 py-2 bg-gray-200 rounded ml-2">検索</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3">日付</th><th class="p-3">授業名</th><th class="p-3">ID</th><th class="p-3">名前</th><th class="p-3">状況</th><th class="p-3">コメント</th><th class="p-3">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($filtered)): ?>
                    <tr><td colspan="7" class="py-12 text-center text-gray-400">データがありません</td></tr>
                <?php else: foreach ($filtered as $rec): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 text-sm"><?= htmlspecialchars($rec['date'], ENT_QUOTES) ?></td>
                        <td class="p-3"><?= htmlspecialchars($rec['className'], ENT_QUOTES) ?></td>
                        <td class="p-3 font-mono text-sm"><?= htmlspecialchars($rec['studentId'], ENT_QUOTES) ?></td>
                        <td class="p-3"><?= htmlspecialchars($rec['studentName'], ENT_QUOTES) ?></td>
                        <td class="p-3"><span class="px-2 py-1 rounded text-xs <?= $rec['status'] === '出席' ? 'bg-gray-100' : 'bg-red-100' ?>"><?= htmlspecialchars($rec['status'], ENT_QUOTES) ?></span></td>
                        <td class="p-3 text-sm text-gray-500"><?= htmlspecialchars($rec['comment'], ENT_QUOTES) ?></td>
                        <td class="p-3 flex gap-2">
                            <a href="html.php?page=teacher/edit_record&id=<?= urlencode($rec['id']) ?>" class="px-2 py-1 bg-blue-500 text-white text-xs rounded">編集</a>
                            <form action="actions.php" method="post" onsubmit="return confirm('この記録を削除しますか？')" class="inline">
                                <input type="hidden" name="action" value="delete_record">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($rec['id'], ENT_QUOTES) ?>">
                                <button type="submit" class="px-2 py-1 bg-red-500 text-white text-xs rounded">削除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">
        <a href="html.php?page=teacher/dashboard" class="px-4 py-2 border rounded hover:bg-gray-50">戻る</a>
    </div>
</div>