<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ここから既存のコードを続けてください

// header.php - 共通ヘッダ
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>出席管理システム</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans JP', sans-serif; }
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>

    <header class="sticky top-0 z-10 bg-white/80 backdrop-blur-md shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="graduation-cap" class="h-6 w-6 text-indigo-600"></i>
                <span class="font-bold text-lg bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    出席管理システム
                </span>
            </div>
            <div class="flex items-center gap-4">
                <?php if ($user): ?>
                    <span class="text-sm font-medium"><?= htmlspecialchars($user['name'], ENT_QUOTES) ?></span>
                    <form action="actions.php" method="post" class="inline">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="text-muted-foreground flex items-center hover:text-gray-900">
                            <i data-lucide="log-out" class="h-4 w-4 mr-2"></i> ログアウト
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </header>