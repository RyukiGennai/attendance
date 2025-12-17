<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/includes/functions.php';
init_data();

$action = $_POST['action'] ?? $_GET['action'] ?? null;

function redirect_with_msg($page = '', $msg = '', $type = 'success') {
    // html.php と同じディレクトリへ相対リダイレクト
    $url = 'html.php' . ($page ? '?page=' . urlencode($page) : '');
    if ($msg) {
        $url .= ($page ? '&' : '?') . 'msg=' . urlencode($msg) . '&msg_type=' . urlencode($type);
    }
    header('Location: ' . $url);
    exit;
}

if ($action === 'login') {
    $id = trim($_POST['loginId'] ?? '');
    $password = $_POST['loginPassword'] ?? '';
    $user = find_user_by_credentials($id, $password);
    if ($user) {
        login_user($user);
        redirect_with_msg($user['role'] === 'teacher' ? 'teacher/dashboard' : 'student/dashboard', $user['name'] . 'としてログインしました', 'success');
    } else {
        redirect_with_msg('login', 'IDまたはパスワードが違います', 'error');
    }
}

if ($action === 'logout') {
    logout_user();
    redirect_with_msg('login', 'ログアウトしました', 'info');
}

if ($action === 'create_form') {
    $user = $_SESSION['user'] ?? null;
    if (!$user || $user['role'] !== 'teacher') redirect_with_msg('login', '権限がありません', 'error');
    $className = trim($_POST['className'] ?? '');
    $date = $_POST['date'] ?? date('Y-m-d');
    $code = trim($_POST['generatedCode'] ?? '');
    $form = create_attendance_form($className, $date, $code ?: null);
    $_SESSION['last_created_form_id'] = $form['id'];
    redirect_with_msg('teacher/share_form', '出席フォームが作成されました', 'success');
}

if ($action === 'submit_attendance') {
    $user = $_SESSION['user'] ?? null;
    if (!$user || $user['role'] !== 'student') redirect_with_msg('login', 'ログインしてください', 'error');
    $code = strtoupper(trim($_POST['studentCode'] ?? ''));
    $form = find_form_by_code($code);
    if (!$form) redirect_with_msg('student/dashboard', '出席コードが正しくありません', 'error');

    // duplicate check
    $studentId = $user['studentId'] ?? null;
    foreach ($form['attendanceList'] as $s) {
        if (($s['studentId'] ?? '') === $studentId) {
            redirect_with_msg('student/dashboard', 'すでに出席済みです', 'error');
        }
    }

    $now = time();
    $diff = $now - ($form['createdAt'] ?? $now);
    $lateThreshold = 10 * 60;
    $deadline = 15 * 60;

    if ($diff > $deadline) {
        redirect_with_msg('student/dashboard', '提出期限（15分）を過ぎているため送信できません', 'error');
    }

    $status = $diff <= $lateThreshold ? '出席' : '遅刻';
    $submission = [
        'studentId' => $studentId,
        'studentName' => $user['name'],
        'timestamp' => $now,
        'status' => $status,
        'comment' => ''
    ];
    // push to form (update session)
    foreach ($_SESSION['attendanceForms'] as &$f) {
        if ($f['id'] === $form['id']) {
            $f['attendanceList'][] = $submission;
            $form = $f;
            break;
        }
    }
    $record = [
        'id' => 'rec-' . $now . '-' . bin2hex(random_bytes(2)),
        'studentId' => $studentId,
        'studentName' => $user['name'],
        'className' => $form['className'],
        'date' => $form['date'],
        'status' => $status,
        'comment' => ''
    ];
    add_record($record);

    redirect_with_msg('student/complete', '出席が完了しました（ステータス: ' . $status . '）', 'success');
}

if ($action === 'save_record') {
    $user = $_SESSION['user'] ?? null;
    if (!$user || $user['role'] !== 'teacher') redirect_with_msg('login', '権限がありません', 'error');

    $id = $_POST['id'] ?? '';
    $record = [
        'id' => $id ?: ('manual-' . time() . '-' . bin2hex(random_bytes(2))),
        'studentId' => trim($_POST['studentId'] ?? ''),
        'studentName' => trim($_POST['studentName'] ?? ''),
        'className' => trim($_POST['className'] ?? ''),
        'date' => $_POST['date'] ?? date('Y-m-d'),
        'status' => $_POST['status'] ?? '出席',
        'comment' => trim($_POST['comment'] ?? '')
    ];

    $idx = find_record_index_by_id($record['id']);
    if ($idx !== null) {
        update_record($record);
        redirect_with_msg('teacher/attendance_list', '記録を更新しました', 'success');
    } else {
        add_record($record);
        redirect_with_msg('teacher/attendance_list', '新規記録を追加しました', 'success');
    }
}

if ($action === 'delete_record') {
    $user = $_SESSION['user'] ?? null;
    if (!$user || $user['role'] !== 'teacher') redirect_with_msg('login', '権限がありません', 'error');
    $id = $_POST['id'] ?? '';
    delete_record($id);
    redirect_with_msg('teacher/attendance_list', '削除しました', 'success');
}

// 不明な操作
redirect_with_msg('', '不明な操作', 'error');