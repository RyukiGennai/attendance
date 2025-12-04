<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>教員ダッシュボード</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="./common.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    <!-- ヘッダー -->
    <header class="bg-white shadow-sm border-b sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2 font-bold text-indigo-600">
                <i data-lucide="graduation-cap"></i> 出席管理システム
            </div>
            <div class="flex items-center gap-4">
                <span id="header-username" class="text-sm font-medium"></span>
                <button onclick="DB.logout()" class="text-gray-500 hover:text-gray-900 text-sm flex items-center gap-1">
                    <i data-lucide="log-out" class="h-4 w-4"></i> ログアウト
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto p-6">
        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h2 class="text-xl font-bold flex items-center gap-2 mb-4 text-indigo-700">
                <i data-lucide="sparkles"></i> 教員ダッシュボード
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="create_form.html" class="p-6 bg-blue-500 text-white rounded-xl shadow hover:bg-blue-600 transition flex flex-col items-center gap-2">
                    <i data-lucide="plus-circle" class="h-8 w-8"></i>
                    <span class="font-bold text-lg">出席フォーム作成</span>
                    <span class="text-sm opacity-90">新しい授業を開始</span>
                </a>
                <button onclick="goToLiveStatus()" class="p-6 bg-emerald-500 text-white rounded-xl shadow hover:bg-emerald-600 transition flex flex-col items-center gap-2">
                    <i data-lucide="list-check" class="h-8 w-8"></i>
                    <span class="font-bold text-lg">リアルタイム状況</span>
                    <span class="text-sm opacity-90">現在の出席を確認</span>
                </button>
                <a href="attendance_list.html" class="md:col-span-2 p-6 bg-purple-500 text-white rounded-xl shadow hover:bg-purple-600 transition flex flex-col items-center gap-2">
                    <i data-lucide="file-text" class="h-8 w-8"></i>
                    <span class="font-bold text-lg">出席リスト管理</span>
                    <span class="text-sm opacity-90">過去の記録を編集・検索</span>
                </a>
            </div>
        </div>
    </main>

    <script>
        checkAuth(); // ログインチェック

        function goToLiveStatus() {
            const forms = DB.get('attendanceForms');
            if (forms.length === 0) {
                alert('出席フォームがまだ作成されていません。先に作成してください。');
                return;
            }
            // 最新のフォームIDを取得して遷移
            const latestForm = forms[forms.length - 1];
            window.location.href = `live_status.html?id=${latestForm.id}`;
        }
    </script>
</body>
</html>