<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>送信完了</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="./common.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4 text-center py-12 bg-white rounded-xl shadow-xl">
        <div class="inline-block p-4 bg-green-100 rounded-full mb-6">
            <i data-lucide="check-circle" class="h-12 w-12 text-green-600"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">送信完了</h2>
        <p class="text-gray-500 mb-6">出席データが記録されました</p>
        
        <div class="bg-gray-50 p-4 rounded-lg mx-8 mb-8 text-left border">
            <div class="mb-2 text-sm text-gray-500">授業名:</div>
            <div id="className" class="font-bold text-lg mb-4">...</div>
            <div class="flex justify-between items-center border-t pt-2">
                <div class="text-sm text-gray-500">ステータス:</div>
                <span id="statusBadge" class="px-3 py-1 rounded-full text-sm font-bold">...</span>
            </div>
        </div>

        <a href="student_dashboard.html" class="px-8 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-700">トップに戻る</a>
    </div>

    <script>
        checkAuth();
        const params = new URLSearchParams(window.location.search);
        const status = params.get('status') || '完了';
        const className = params.get('class') || '';

        document.getElementById('className').textContent = decodeURIComponent(className);
        
        const badge = document.getElementById('statusBadge');
        badge.textContent = decodeURIComponent(status);
        if (status === '出席') {
            badge.className = "px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-700";
        } else {
            badge.className = "px-3 py-1 rounded-full text-sm font-bold bg-yellow-100 text-yellow-700";
        }
    </script>
</body>
</html>