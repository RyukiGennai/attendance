<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ここから既存のコードを続けてください

session_start();
require_once __DIR__ . '/includes/functions.php';

// 初期データをセッションに用意
init_data();

// ログイン済みユーザー
$user = $_SESSION['user'] ?? null;

// page パラメータで表示切替（例: ?page=teacher/dashboard）
$page = $_GET['page'] ?? null;

// デフォルトページ
if (!$page) {
    $page = $user ? ($user['role'] === 'teacher' ? 'teacher/dashboard' : 'student/dashboard') : 'login';
}

// 許可されたビューだけをロード
$allowedViews = [
    'login','actions',
    'teacher/dashboard', 'teacher/create_form', 'teacher/share_form', 'teacher/live_status', 'teacher/attendance_list', 'teacher/edit_record',
    'student/dashboard', 'student/history', 'student/complete'
];

if (!in_array($page, $allowedViews, true)) {
    http_response_code(404);
    echo "404 Not Found";
    exit;
}

include __DIR__ . '/includes/header.php';
?>
<main class="max-w-5xl mx-auto p-4 sm:p-6 lg:p-8">
    <?php include __DIR__ . "/views/{$page}.php"; ?>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>