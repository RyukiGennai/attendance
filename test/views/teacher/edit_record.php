<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ここから既存のコードを続けてください
// edit or add record
$id = $_GET['id'] ?? '';
$record = [
    'id' => '',
    'studentId' => '',
    'studentName' => '',
    'className' => '',
    'date' => date('Y-m-d'),
    'status' => '出席',
    'comment' => ''
];
if ($id) {
    foreach (get_records() as $r) {
        if ($r['id'] === $id) { $record = $r; break; }
    }
}
?>
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-6 fade-in">
    <h2 class="text-xl font-bold mb-6 text-blue-600">編集 / 新規登録</h2>
    <form action="actions.php" method="post" class="space-y-4">
        <input type="hidden" name="action" value="save_record">
        <input type="hidden" name="id" value="<?= htmlspecialchars($record['id'], ENT_QUOTES) ?>">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">学籍番号</label>
                <input type="text" name="studentId" value="<?= htmlspecialchars($record['studentId'], ENT_QUOTES) ?>" class="w-full p-2 border rounded" required>
                <p class="text-xs text-gray-400 mt-1">※番号入力で名前自動反映（サンプルは手動）</p>
            </div>
            <div>
                <label class="block text-sm mb-1">名前</label>
                <input type="text" name="studentName" value="<?= htmlspecialchars($record['studentName'], ENT_QUOTES) ?>" class="w-full p-2 border rounded" required>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">授業名</label>
                <input type="text" name="className" value="<?= htmlspecialchars($record['className'], ENT_QUOTES) ?>" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block text-sm mb-1">日付</label>
                <input type="date" name="date" value="<?= htmlspecialchars($record['date'], ENT_QUOTES) ?>" class="w-full p-2 border rounded" required>
            </div>
        </div>
        <div>
            <label class="block text-sm mb-1">状況</label>
            <select name="status" class="w-full p-2 border rounded">
                <option value="出席" <?= $record['status'] === '出席' ? 'selected' : '' ?>>出席</option>
                <option value="遅刻" <?= $record['status'] === '遅刻' ? 'selected' : '' ?>>遅刻</option>
                <option value="欠席" <?= $record['status'] === '欠席' ? 'selected' : '' ?>>欠席</option>
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">コメント</label>
            <textarea name="comment" class="w-full p-2 border rounded"><?= htmlspecialchars($record['comment'], ENT_QUOTES) ?></textarea>
        </div>
        <div class="flex justify-between pt-4 border-t">
            <a href="html.php?page=teacher/attendance_list" class="px-4 py-2 border rounded">キャンセル</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">保存</button>
        </div>
    </form>
</div>