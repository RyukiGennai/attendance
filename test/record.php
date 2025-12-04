<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編集・新規追加</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="./common.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    <div class="max-w-2xl mx-auto mt-10 bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold mb-6 text-blue-600" id="pageTitle">編集 / 新規登録</h2>
        <form id="editForm" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1">学籍番号</label>
                    <input type="text" id="sid" class="w-full p-2 border rounded" required oninput="autoFillName(this.value)">
                    <p class="text-xs text-gray-400 mt-1">※番号入力で名前自動反映</p>
                </div>
                <div>
                    <label class="block text-sm mb-1">名前</label>
                    <input type="text" id="sname" class="w-full p-2 border rounded" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1">授業名</label>
                    <input type="text" id="cname" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm mb-1">日付</label>
                    <input type="date" id="cdate" class="w-full p-2 border rounded" required>
                </div>
            </div>
            <div>
                <label class="block text-sm mb-1">状況</label>
                <select id="status" class="w-full p-2 border rounded">
                    <option value="出席">出席</option><option value="遅刻">遅刻</option><option value="欠席">欠席</option>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">コメント</label>
                <textarea id="comment" class="w-full p-2 border rounded"></textarea>
            </div>
            
            <div class="flex justify-between pt-4 border-t">
                <!-- 削除ボタン（編集時のみ表示） -->
                <button type="button" id="deleteBtn" onclick="deleteCurrent()" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 hidden">
                    <i data-lucide="trash-2" class="h-4 w-4 inline mr-1"></i> 削除
                </button>
                <div class="flex gap-2 ml-auto">
                    <a href="attendance_list.html" class="px-4 py-2 border rounded hover:bg-gray-50">キャンセル</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">保存</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        checkAuth();
        const urlParams = new URLSearchParams(window.location.search);
        const recordId = urlParams.get('id');
        let records = DB.get('attendanceRecords');
        const users = DB.get('users');

        // 名前自動反映
        function autoFillName(val) {
            const u = users.find(u => u.studentId === val);
            if(u) document.getElementById('sname').value = u.name;
        }

        // 初期表示（編集 or 新規）
        if (recordId) {
            const rec = records.find(r => r.id === recordId);
            if (rec) {
                document.getElementById('pageTitle').textContent = '出席記録の編集';
                document.getElementById('sid').value = rec.studentId;
                document.getElementById('sname').value = rec.studentName;
                document.getElementById('cname').value = rec.className;
                document.getElementById('cdate').value = rec.date;
                document.getElementById('status').value = rec.status;
                document.getElementById('comment').value = rec.comment || '';
                document.getElementById('deleteBtn').classList.remove('hidden'); // 削除ボタン表示
            }
        } else {
            document.getElementById('pageTitle').textContent = '新規出席登録';
            document.getElementById('cdate').value = new Date().toISOString().split('T')[0];
        }

        // 削除処理
        function deleteCurrent() {
            if(!confirm('この記録を削除しますか？')) return;
            records = records.filter(r => r.id !== recordId);
            DB.save('attendanceRecords', records);
            
            // フォーム側の整合性
            const rec = records.find(r => r.id === recordId); // filter前のが必要だが簡易実装のため省略または再取得が必要
            // 厳密には削除対象の情報を保持しておく必要がありますが、ここではリスト画面へ戻るだけにします
            window.location.href = 'attendance_list.html';
        }

        // 保存処理
        document.getElementById('editForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const newData = {
                id: recordId || `manual-${Date.now()}`,
                studentId: document.getElementById('sid').value,
                studentName: document.getElementById('sname').value,
                className: document.getElementById('cname').value,
                date: document.getElementById('cdate').value,
                status: document.getElementById('status').value,
                comment: document.getElementById('comment').value
            };

            if (recordId) {
                const idx = records.findIndex(r => r.id === recordId);
                if (idx !== -1) records[idx] = newData;
            } else {
                records.push(newData);
            }
            DB.save('attendanceRecords', records);
            window.location.href = 'attendance_list.html';
        });
    </script>
</body>
</html>