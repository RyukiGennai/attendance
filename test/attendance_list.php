<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">   
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>出席リスト管理</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="./common.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    <div class="max-w-5xl mx-auto p-6">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6 border-b flex justify-between items-center bg-purple-50">
                <h2 class="text-xl font-bold text-purple-700 flex items-center gap-2">
                    <i data-lucide="file-text"></i> 出席リスト管理
                </h2>
                <a href="teacher_dashboard.html" class="text-sm text-purple-600 hover:underline">ダッシュボードへ戻る</a>
            </div>
            
            <!-- 検索フィルター -->
            <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 border-b">
                <input type="date" id="searchDate" class="p-2 border rounded" onchange="renderList()">
                <input type="text" id="searchClass" placeholder="授業名" class="p-2 border rounded" oninput="renderList()">
                <input type="text" id="searchName" placeholder="名前" class="p-2 border rounded" oninput="renderList()">
            </div>

            <!-- リスト -->
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-3">日付</th>
                            <th class="p-3">授業名</th>
                            <th class="p-3">ID</th>
                            <th class="p-3">名前</th>
                            <th class="p-3">状況</th>
                            <th class="p-3">操作</th>
                        </tr>
                    </thead>
                    <tbody id="listBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        checkAuth();

        function renderList() {
            const records = DB.get('attendanceRecords');
            const sDate = document.getElementById('searchDate').value;
            const sClass = document.getElementById('searchClass').value.toLowerCase();
            const sName = document.getElementById('searchName').value.toLowerCase();

            // フィルタリング
            const filtered = records.filter(r => {
                const matchDate = !sDate || r.date.includes(sDate);
                const matchClass = !sClass || r.className.toLowerCase().includes(sClass);
                const matchName = !sName || r.studentName.toLowerCase().includes(sName);
                return matchDate && matchClass && matchName;
            }).sort((a, b) => new Date(b.date) - new Date(a.date));

            const tbody = document.getElementById('listBody');
            if (filtered.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="py-12 text-center text-gray-400">データがありません</td></tr>';
            } else {
                tbody.innerHTML = filtered.map(rec => `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 text-sm">${rec.date}</td>
                        <td class="p-3">${rec.className}</td>
                        <td class="p-3 font-mono text-sm">${rec.studentId}</td>
                        <td class="p-3">${rec.studentName}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-xs ${rec.status === '出席' ? 'bg-gray-100' : 'bg-red-100'}">${rec.status}</span>
                        </td>
                        <td class="p-3">
                            <button onclick="deleteRecord('${rec.id}')" class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">削除</button>
                        </td>
                    </tr>
                `).join('');
            }
        }

        function deleteRecord(id) {
            if (confirm('この記録を削除しますか？')) {
                // 全記録から削除
                let records = DB.get('attendanceRecords');
                const target = records.find(r => r.id === id);
                records = records.filter(r => r.id !== id);
                DB.save('attendanceRecords', records);

                // 授業データ(forms)内のリストからも削除して整合性を保つ
                if (target) {
                    let forms = DB.get('attendanceForms');
                    forms = forms.map(f => {
                        if (f.className === target.className && f.date === target.date) {
                            f.attendanceList = f.attendanceList.filter(s => s.studentId !== target.studentId);
                        }
                        return f;
                    });
                    DB.save('attendanceForms', forms);
                }

                alert('削除しました');
                renderList();
            }
        }

        // 初期表示
        renderList();
    </script>
</body>
</html>