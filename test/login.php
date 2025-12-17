<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ここから既存のコードを続けてください

?>
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="w-full max-w-md bg-white rounded-xl shadow-xl p-8 fade-in">
        <div class="text-center mb-8">
            <div class="inline-block p-4 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl mb-4">
                <i data-lucide="graduation-cap" class="h-12 w-12 text-white"></i>
            </div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">出席管理システム</h1>
            <p class="text-gray-500 mt-2">ログインして始めましょう</p>
        </div>
        <form action="actions.php" method="post" class="space-y-4">
            <input type="hidden" name="action" value="login">
            <div>
                <label class="block text-sm font-medium mb-1">ユーザーID</label>
                <input type="text" name="loginId" class="w-full p-2 border rounded-md" placeholder="ユーザーIDを入力" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">パスワード</label>
                <input type="password" name="loginPassword" class="w-full p-2 border rounded-md" placeholder="パスワードを入力" required>
            </div>
            <button type="submit" class="w-full py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-md hover:opacity-90">ログイン</button>
        </form>
        <div class="mt-4 text-center text-xs text-gray-400">
            デモ用: teacher / password または K023C0063 / password
        </div>
    </div>
</div>