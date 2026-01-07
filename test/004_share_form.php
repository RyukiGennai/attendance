<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>コード共有</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="./common.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-xl shadow-xl p-8 text-center">
        <div class="inline-block p-3 bg-green-100 rounded-full mb-4">
            <i data-lucide="check-circle" class="h-8 w-8 text-green-600"></i>
        </div>
        <h2 class="text-xl font-bold text-green-700 mb-2">作成完了</h2>
        <p class="text-gray-500 mb-6">生徒に以下のコードを共有してください</p>
        
        <div class="bg-indigo-50 p-6 rounded-xl border-2 border-indigo-200 mb-6">
            <span id="displayCode" class="text-4xl font-mono font-bold text-indigo-700 tracking-widest">...</span>
        </div>
        
        <div class="flex justify-center mb-6">
            <div id="qrPlaceholder" class="p-4 bg-white rounded-xl shadow border border-gray-100">
                <!-- QRコード画像はAPIで生成 -->
                <img id="qrImage" src="" alt="QR Code" class="rounded-lg">
            </div>
        </div>

        <div class="space-y-3">
            <button onclick="goToLive()" class="w-full py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 flex items-center justify-center gap-2">
                <i data-lucide="list-check"></i> リアルタイム状況を見る
            </button>
            <a href="teacher_dashboard.html" class="block w-full py-3 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50">
                ダッシュボードへ戻る
            </a>
        </div>
    </div>

    <script>
        checkAuth();
        const urlParams = new URLSearchParams(window.location.search);
        const formId = urlParams.get('id');
        const forms = DB.get('attendanceForms');
        const form = forms.find(f => f.id === formId);

        if (form) {
            document.getElementById('displayCode').textContent = form.code;
            
            // 学生用URL (本来はデプロイ先のURL)
            const studentUrl = `${window.location.origin}/student_dashboard.html?code=${form.code}`;
            const qrApi = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(studentUrl)}`;
            document.getElementById('qrImage').src = qrApi;
        } else {
            alert('フォームが見つかりません');
            window.location.href = 'teacher_dashboard.html';
        }

        function goToLive() {
            window.location.href = `live_status.html?id=${formId}`;
        }
    </script>
</body>
</html>