<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>リアルタイム状況</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="./common.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    <div class="max-w-5xl mx-auto p-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-b flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h2 class="text-xl font-bold flex items-center gap-2 text-emerald-800">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                        リアルタイム出席状況
                    </h2>
                    <p id="formInfo" class="text-sm text-gray-500 mt-1">5秒ごとに更新中...</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <div id="count" class="text-2xl font-bold text-gray-800">0名</div>
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
                    <tbody id="listBody">
                        <!-- JSで描画 -->
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 bg-gray-50 border-t flex justify-between">
                <a href="teacher_dashboard.html" class="px-4 py-2 border bg-white rounded hover:bg-gray-50">戻る</a>
            </div>
        </div>
    </div>

    <script>
        checkAuth();
        const urlParams = new URLSearchParams(window.location.search);
        const formId = urlParams.get('id');

        function renderList() {
            const forms = DB.get('attendanceForms');
            const form = forms.find(f => f.id === formId);
            
            if (!form) {
                document.getElementById('listBody').innerHTML = '<tr><td colspan="5" class="p-4 text-center">フォームが見つかりません</td></tr>';
                return;
            }

            // 情報更新
            document.getElementById('formInfo').textContent = `5秒ごとに更新中 | ${form.className} (${form.date})`;
            document.getElementById('count').textContent = `${form.attendanceList.length}名`;

            // リスト更新
            const tbody = document.getElementById('listBody');
            if (form.attendanceList.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="py-12 text-center text-gray-400">待機中...</td></tr>';
            } else {
                tbody.innerHTML = form.attendanceList.map((sub, i) => `
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="p-3 text-center">${i + 1}</td>
                        <td class="p-3 font-mono">${sub.studentId}</td>
                        <td class="p-3">${sub.studentName}</td>
                        <td class="p-3 text-sm text-gray-500">${new Date(sub.timestamp).toLocaleTimeString()}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-xs ${
                                sub.status === '出席' ? 'bg-green-100 text-green-800' : 
                                sub.status === '遅刻' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'
                            }">${sub.status}</span>
                        </td>
                    </tr>
                `).join('');
            }
        }

        function finalizeAbsences() {            
            // 対象フォームの再取得
            const formIndex = forms.findIndex(f => f.id === formId);
            if (formIndex === -1) return;
            const form = forms[formIndex];

            // 未提出者特定
            const allStudents = users.filter(u => u.role === 'student');
            const submittedIds = new Set(form.attendanceList.map(s => s.studentId));
            const missing = allStudents.filter(s => s.studentId && !submittedIds.has(s.studentId));

            if (missing.length === 0) {
                alert('未提出の学生はいません');
                return;
            }

            const now = Date.now();
            missing.forEach(student => {
                // フォームに追加
                form.attendanceList.push({
                    studentId: student.studentId,
                    studentName: student.name,
                    timestamp: now,
                    status: '欠席',
                    comment: '自動欠席'
                });
                // 全記録に追加
                records.push({
                    id: `rec-${now}-${student.studentId}`,
                    studentId: student.studentId,
                    studentName: student.name,
                    className: form.className,
                    date: form.date,
                    status: '欠席',
                    comment: '自動欠席'
                });
            });

            // 保存
            forms[formIndex] = form;
            DB.save('attendanceForms', forms);
            DB.save('attendanceRecords', records);
            
            alert(`${missing.length}名を欠席として登録しました`);
            renderList();
        }

        // 初回実行と定期実行
        renderList();
        setInterval(renderList, 5000);
    </script>
</body>
</html>