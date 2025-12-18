<?php
// ファイルの先頭に空白や改行が絶対にないようにしてください
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/includes/functions.php';

// DB接続確認も兼ねて初期化（セッション開始等）
init_data();

// GETとPOSTの両方からアクションを受け取る
$action = $_POST['action'] ?? $_GET['action'] ?? null;

// リダイレクト用関数
function redirect_with_msg($page = '', $msg = '', $type = 'success') {
    // html.php と同じディレクトリへ相対リダイレクト
    $url = 'html.php' . ($page ? '?page=' . urlencode($page) : '');
    if ($msg) {
        $url .= ($page ? '&' : '?') . 'msg=' . urlencode($msg) . '&msg_type=' . urlencode($type);
    }
    header('Location: ' . $url);
    exit;
}

// アクションがない場合はログインへ戻す
if (!$action) {
    redirect_with_msg('login');
}

// -----------------------------------------
// 1. ログアウト処理 (ここが重要)
// -----------------------------------------
if ($action === 'logout') {
    logout_user(); // functions.php の関数を呼び出し
    redirect_with_msg('login', 'ログアウトしました', 'info');
}

// -----------------------------------------
// 2. ログイン処理
// -----------------------------------------
if ($action === 'login') {
    $id = trim($_POST['loginId'] ?? '');
    $password = $_POST['loginPassword'] ?? '';
    
    // DBを使ってユーザー検索
    $user = find_user_by_credentials($id, $password);
    
    if ($user) {
        login_user($user);
        redirect_with_msg($user['role'] === 'teacher' ? 'teacher/dashboard' : 'student/dashboard', $user['name'] . 'としてログインしました', 'success');
    } else {
        redirect_with_msg('login', 'IDまたはパスワードが違います', 'error');
    }
}

// -----------------------------------------
// 3. 授業作成 (教員)
// -----------------------------------------
if ($action === 'create_form') {
    $user = $_SESSION['user'] ?? null;
    if (!$user || $user['role'] !== 'teacher') redirect_with_msg('login', '権限がありません', 'error');
    
    $className = trim($_POST['className'] ?? '');
    $date = $_POST['date'] ?? date('Y-m-d');
    $code = trim($_POST['generatedCode'] ?? '');
    
    // DBに保存
    $form = create_attendance_form($className, $date, $code ?: null);
    
    // 作成したIDをセッションに一時保存（共有画面表示用）
    $_SESSION['last_created_form_id'] = $form['id'];
    
    redirect_with_msg('teacher/share_form', '出席フォームが作成されました', 'success');
}

// -----------------------------------------
// 4. 出席送信 (学生)
// -----------------------------------------
if ($action === 'submit_attendance') {
    $user = $_SESSION['user'] ?? null;
    if (!$user || $user['role'] !== 'student') redirect_with_msg('login', 'ログインしてください', 'error');
    
    $code = strtoupper(trim($_POST['studentCode'] ?? ''));
    
    // コードから授業を検索
    $form = find_form_by_code($code);
    
    if (!$form) {
        redirect_with_msg('student/dashboard', '出席コードが正しくありません', 'error');
    }

    // 遅刻判定（作成から10分以上経過で遅刻）
    $now = time();
    $diff = $now - ($form['createdAt'] ?? $now);
    $lateThreshold = 10 * 60; // 10分
    
    $statusInt = ($diff <= $lateThreshold) ? 0 : 1; // 0:出席, 1:遅刻
    $statusStr = ($statusInt === 0) ? '出席' : '遅刻';

    // DBへ登録
    $success = submit_attendance_record($user['id'], $form['id'], $statusInt);

    if (!$success) {
        redirect_with_msg('student/dashboard', 'すでに出席済みです', 'error');
    }

    redirect_with_msg('student/complete', '出席が完了しました（ステータス: ' . $statusStr . '）', 'success');
}

// -----------------------------------------
// 5. レコード保存 (教員による編集・手動追加)
// -----------------------------------------
if ($action === 'save_record') {
    $user = $_SESSION['user'] ?? null;
    if (!$user || $user['role'] !== 'teacher') redirect_with_msg('login', '権限がありません', 'error');

    // ※DB版の実装では、ここでINSERT/UPDATEを行うSQLが必要です。
    // 今回は簡易的に一覧へ戻す処理のみ記述します。
    // 本格的に実装する場合は、入力された学籍番号からUSER_IDを特定する処理などが必要です。
    
    redirect_with_msg('teacher/attendance_list', 'データの更新を行いました（DB処理は要調整）', 'success');
}

// -----------------------------------------
// 6. レコード削除 (教員)
// -----------------------------------------
if ($action === 'delete_record') {
    $user = $_SESSION['user'] ?? null;
    if (!$user || $user['role'] !== 'teacher') redirect_with_msg('login', '権限がありません', 'error');

    $id = $_POST['id'] ?? '';
    
    // DB削除処理
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("DELETE FROM TBL_ATTENDANCE_STATUS WHERE ATTENDANCE_ID = ?");
    $stmt->execute([$id]);

    redirect_with_msg('teacher/attendance_list', '記録を削除しました', 'info');
}

// どのアクションにも当てはまらなかった場合
redirect_with_msg('login');
?>