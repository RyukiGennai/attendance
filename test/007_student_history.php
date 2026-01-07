<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>出席履歴</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="./common.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    <div class="max-w-md mx-auto mt-10 bg-white rounded-xl shadow-lg">
        <div class="p-4 border-b bg-gray-50 flex items-center gap-2">
            <i data-lucide="history"></i> <span class="font-bold">出席履歴</span>
        </div>
        <div class="p-4">
            <table class="w-full text-left">
                <thead><tr class="text-sm text-gray-500"><th class="p-2">日付</th><th class="p-2">授業</th><th class="p-2">状況</th></tr></thead>
                <tbody id="historyList"></tbody>
            </table>
            <a href="student_dashboard.html" class="block text-center w-full mt-6 py-2 border rounded hover:bg-gray-50">戻る</a>
        </div>
    </div>
    <script>
        checkAuth();
        const user = DB.getCurrentUser();
        const records = DB.get('attendanceRecords');
        const myRecords = records.filter(r => r.studentId === user.studentId).sort((a,b)=>new Date(b.date)-new Date(a.date));
        
        document.getElementById('historyList').innerHTML = myRecords.length === 0 ? '<tr><td colspan="3" class="py-4 text-center text-gray-400">履歴なし</td></tr>' :
        myRecords.map(r => `
            <tr class="border-b">
                <td class="p-3 text-sm">${r.date}</td>
                <td class="p-3 text-sm">${r.className}</td>
                <td class="p-3"><span class="px-2 py-1 rounded text-xs ${r.status==='出席'?'bg-green-100':'bg-yellow-100'}">${r.status}</span></td>
            </tr>
        `).join('');
    </script>
</body>
</html>