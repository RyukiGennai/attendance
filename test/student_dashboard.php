<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>学生ダッシュボード</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="./common.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    <!-- ヘッダー -->
    <header class="bg-white shadow-sm border-b sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2 font-bold text-green-600">
                <i data-lucide="graduation-cap"></i> 学生ポータル
            </div>
            <div class="flex items-center gap-4">
                <button onclick="DB.logout()" class="text-gray-500 hover:text-gray-900 text-sm flex items-center gap-1">
                    <i data-lucide="log-out" class="h-4 w-4"></i> ログアウト
                </button>
            </div>
        </div>
    </header>

    <div class="max-w-md mx-auto mt-10 bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-emerald-50 to-teal-50 border-b">
            <h2 class="text-xl font-bold text-emerald-800 flex items-center gap-2">
                <i data-lucide="user"></i> ようこそ
            </h2>
            <div class="mt-4">
                <div id="studentName" class="text-lg font-bold">...</div>
                <div id="studentId" class="text-sm text-gray-500">...</div>
            </div>
            <div class="mt-2 inline-block px-2 py-1 bg-amber-50 text-amber-700 text-xs rounded border border-amber-200">
                <i data-lucide="alert-circle" class="inline w-3 h-3"></i> 提出期限: 作成から15分以内
            </div>
        </div>
        
        <div class="p-6">
            <form id="attendForm" class="space-y-6">
                <div>
                    <label class="block font-bold mb-2">出席コード</label>
                    <input type="text" id="inputCode" class="w-full p-4 text-center text-2xl font-mono border-2 border-gray-200 rounded-lg tracking-widest uppercase focus:border-emerald-500 outline-none" placeholder="ABC123" maxlength="6" required>
                </div>
                <button type="submit" class="w-full py-4 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 shadow-lg flex items-center justify-center gap-2 transition">
                    <i data-lucide="check-circle"></i> 出席を送信
                </button>
            </form>
        </div>
    </div>

    <!-- 送信完了モーダル（簡易実装） -->
    <div id="successModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded-xl shadow-2xl text-center max-w-sm mx-4">
            <div class="inline-block p-4 bg-green-100 rounded-full mb-4">
                <i data-lucide="check-circle" class="h-12 w-12 text-green-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">送信完了</h2>
            <p id="resultStatus" class="text-lg font-bold text-emerald-600 mb-4"></p>
            <p class="text-gray-500 mb-6">出席データが記録されました</p>
            <button onclick="location.reload()" class="px-6 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">閉じる</button>
        </div>
    </div>

    <script>
        checkAuth();
        const user = DB.getCurrentUser();
        if (user) {
            document.getElementById('studentName').textContent = `${user.name} さん`;
            document.getElementById('studentId').textContent = `学籍番号: ${user.studentId}`;
        }

        document.getElementById('attendForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const code = document.getElementById('inputCode').value.toUpperCase();
            
            // フォーム検索
            const forms = DB.get('attendanceForms');
            const targetFormIndex = forms.findIndex(f => f.code === code);
            
            if (targetFormIndex === -1) {
                alert('出席コードが正しくありません');
                return;
            }
            const form = forms[targetFormIndex];

            // 既存チェック
            if (form.attendanceList.some(s => s.studentId === user.studentId)) {
                alert('この授業にはすでに出席済みです');
                return;
            }

            // 締め切り判定
            const now = Date.now();
            const diff = now - form.createdAt;
            const LIMIT_15 = 15 * 60 * 1000;
            const LIMIT_10 = 10 * 60 * 1000;

            if (diff > LIMIT_15) {
                alert('提出期限（15分）を過ぎているため送信できません');
                return;
            }

            const status = diff <= LIMIT_10 ? '出席' : '遅刻';

            // データ保存
            // 1. 授業データ内に追加
            form.attendanceList.push({
                studentId: user.studentId,
                studentName: user.name,
                timestamp: now,
                status: status,
                comment: ''
            });
            forms[targetFormIndex] = form;
            DB.save('attendanceForms', forms);

            // 2. 全記録に追加
            const records = DB.get('attendanceRecords');
            records.push({
                id: `rec-${now}`,
                studentId: user.studentId,
                studentName: user.name,
                className: form.className,
                date: form.date,
                status: status,
                comment: ''
            });
            DB.save('attendanceRecords', records);

            // 完了表示
            document.getElementById('resultStatus').textContent = `ステータス: ${status}`;
            document.getElementById('successModal').classList.remove('hidden');
        });
    </script>
</body>
</html>