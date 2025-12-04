<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - 出席管理システム</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="./common.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-xl shadow-xl p-8">
        <div class="text-center mb-8">
            <div class="inline-block p-4 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl mb-4">
                <i data-lucide="graduation-cap" class="h-12 w-12 text-white"></i>
            </div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">出席管理システム</h1>
            <p class="text-gray-500 mt-2">ログインして始めましょう</p>
        </div>
        
        <form id="loginForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">ユーザーID</label>
                <input type="text" id="loginId" class="w-full p-2 border rounded-md" placeholder="ユーザーIDを入力" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">パスワード</label>
                <input type="password" id="password" class="w-full p-2 border rounded-md" placeholder="パスワードを入力" required>
            </div>
            <button type="submit" class="w-full py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-md hover:opacity-90">ログイン</button>
        </form>
        
        <div class="mt-4 text-center text-xs text-gray-400">
            デモ用: teacher / password または K023C0063 / password
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const id = document.getElementById('loginId').value;
            const pass = document.getElementById('password').value;
            
            const user = DB.findUser(id, pass);
            if (user) {
                DB.setCurrentUser(user);
                // 権限によって遷移先を変える
                if (user.role === 'teacher') {
                    window.location.href = 'teacher_dashboard.html';
                } else {
                    window.location.href = 'student_dashboard.html';
                }
            } else {
                alert('IDまたはパスワードが違います');
            }
        });
    </script>
</body>
</html>