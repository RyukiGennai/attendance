<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>フォーム作成</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="./common.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    <div class="max-w-2xl mx-auto mt-10 bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-blue-600">
            <i data-lucide="plus-circle"></i> 出席フォーム作成
        </h2>
        <form id="createForm" class="space-y-6">
            <div>
                <label class="block text-sm font-medium mb-1">授業名</label>
                <input type="text" id="className" value="情報システム基礎" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">日付</label>
                <input type="date" id="classDate" class="w-full p-2 border rounded" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">出席コード</label>
                <div class="flex gap-2">
                    <input type="text" id="code" readonly class="w-full p-2 border rounded bg-gray-50 font-mono tracking-widest text-center text-lg">
                    <button type="button" onclick="generate()" class="px-4 py-2 border rounded hover:bg-gray-50">
                        <i data-lucide="sparkles" class="h-4 w-4"></i>
                    </button>
                </div>
            </div>
            <div class="flex justify-between pt-4 border-t">
                <a href="teacher_dashboard.html" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">戻る</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">作成する</button>
            </div>
        </form>
    </div>

    <script>
        checkAuth();
        document.getElementById('classDate').value = new Date().toISOString().split('T')[0];
        
        function generate() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let c = '';
            for (let i = 0; i < 6; i++) c += chars.charAt(Math.floor(Math.random() * chars.length));
            document.getElementById('code').value = c;
        }
        generate(); // 初期生成

        document.getElementById('createForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const forms = DB.get('attendanceForms');
            const newForm = {
                id: `form-${Date.now()}`,
                className: document.getElementById('className').value,
                date: document.getElementById('classDate').value,
                code: document.getElementById('code').value,
                attendanceList: [],
                createdAt: Date.now()
            };
            forms.push(newForm);
            DB.save('attendanceForms', forms);
            
            // 共有画面へ遷移
            window.location.href = `share_form.html?id=${newForm.id}`;
        });
    </script>
</body>
</html>