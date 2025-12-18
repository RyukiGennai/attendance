<?php
// functions.php - セッションバックデータとヘルパー（デモ用）
if (session_status() === PHP_SESSION_NONE) session_start();

function init_data(): void {
    if (!isset($_SESSION['initialized'])) {
        $_SESSION['users'] = [
            ['id' => 'teacher', 'password' => 'password', 'role' => 'teacher', 'name' => '教員 太郎'],
            ['id' => 'K023C0063', 'password' => 'password', 'role' => 'student', 'name' => '源内 琉月', 'studentId' => 'K023C0063'],
            ['id' => 'K023C0082', 'password' => 'password', 'role' => 'student', 'name' => '清水屋 雄紀', 'studentId' => 'K023C0082'],
            ['id' => 'K023C0020', 'password' => 'password', 'role' => 'student', 'name' => '筌野 来海', 'studentId' => 'K023C0020'],
            ['id' => 'K023C0022', 'password' => 'password', 'role' => 'student', 'name' => '呉 荻', 'studentId' => 'K023C0022'],
            ['id' => 'K023C0048', 'password' => 'password', 'role' => 'student', 'name' => '岡田 空', 'studentId' => 'K023C0048'],
        ];
        $_SESSION['attendanceForms'] = [];   // ['id','className','date','code','attendanceList'=>[], 'createdAt' => time()]
        $_SESSION['attendanceRecords'] = []; // ['id','studentId','studentName','className','date','status','comment']
        $_SESSION['initialized'] = true;
    }
}

function get_users(): array {
    return $_SESSION['users'] ?? [];
}

function find_user_by_credentials(string $id, string $password): ?array {
    foreach (get_users() as $u) {
        if ($u['id'] === $id && $u['password'] === $password) return $u;
    }
    return null;
}

function login_user(array $user): void {
    $_SESSION['user'] = $user;
}

function logout_user(): void {
    unset($_SESSION['user']);
}

function generate_code(int $length = 6): string {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for ($i = 0; $i < $length; $i++) $str .= $chars[random_int(0, strlen($chars)-1)];
    return $str;
}

function create_attendance_form(string $className, string $date, ?string $code = null): array {
    $code = $code ?: generate_code();
    $form = [
        'id' => 'form-' . time() . '-' . bin2hex(random_bytes(3)),
        'className' => $className,
        'date' => $date,
        'code' => $code,
        'attendanceList' => [],
        'createdAt' => time()
    ];
    $_SESSION['attendanceForms'][] = $form;
    return $form;
}

function get_attendance_forms(): array {
    return $_SESSION['attendanceForms'] ?? [];
}

function find_form_by_code(string $code): ?array {
    foreach (get_attendance_forms() as $f) {
        if ($f['code'] === $code) return $f;
    }
    return null;
}

function update_form(array $form): void {
    foreach ($_SESSION['attendanceForms'] as $i => $f) {
        if ($f['id'] === $form['id']) {
            $_SESSION['attendanceForms'][$i] = $form;
            return;
        }
    }
}

function add_record(array $record): void {
    $_SESSION['attendanceRecords'][] = $record;
}

function get_records(): array {
    return $_SESSION['attendanceRecords'] ?? [];
}

function find_record_index_by_id(string $id): ?int {
    foreach (get_records() as $i => $r) {
        if ($r['id'] === $id) return $i;
    }
    return null;
}

function update_record(array $record): void {
    $idx = find_record_index_by_id($record['id']);
    if ($idx !== null) $_SESSION['attendanceRecords'][$idx] = $record;
}<?php
require_once __DIR__ . '/db_config.php';

// セッション初期化（データ作成は不要になるため削除し、単なる開始のみ）
function init_data() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// ユーザー認証
function find_user_by_credentials($id, $password) {
    $pdo = get_db_connection();
    // SQL: ユーザーIDとパスワードで検索
    $stmt = $pdo->prepare("SELECT * FROM MST_USER WHERE USER_ID = ? AND PASSWORD = ?");
    $stmt->execute([$id, $password]);
    $user = $stmt->fetch();

    if ($user) {
        // PHP側で扱いやすい配列形式に変換
        return [
            'id' => $user['USER_ID'],
            'name' => $user['NAME'],
            'role' => ((int)$user['ROLE'] === 1) ? 'teacher' : 'student',
            'studentId' => $user['STUDENT_NUMBER'] // 学生の場合
        ];
    }
    return null;
}

// 授業（出席フォーム）作成
function create_attendance_form($className, $date, $code = null) {
    $pdo = get_db_connection();
    
    // ID生成 (CL+5桁)
    $stmt = $pdo->query("SELECT COUNT(*) FROM TBL_CLASS");
    $count = $stmt->fetchColumn();
    $classId = 'CL' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);

    // コード生成
    if (!$code) $code = generate_code();

    // 日付と時間の分離 (YYYY-MM-DD HH:MM:SS を想定)
    // フォームからは YYYY-MM-DD が来るため、時間は現在時刻とする
    $time = date('H:i:s');
    
    // 教員ID取得 (セッションから)
    $teacherId = $_SESSION['user']['id'];

    $sql = "INSERT INTO TBL_CLASS (CLASS_ID, CLASS_NAME, `DATE`, `TIME`, ATTENDANCE_CODE, URL, USER_ID) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$classId, $className, $date, $time, $code, 'http://example.com', $teacherId]);

    return ['id' => $classId, 'code' => $code];
}

// 出席コード生成
function generate_code() {
    return strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}

// コードから授業を検索
function find_form_by_code($code) {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT * FROM TBL_CLASS WHERE ATTENDANCE_CODE = ?");
    $stmt->execute([$code]);
    $row = $stmt->fetch();

    if ($row) {
        // 既存PHPコードとの互換性のためキー名を調整
        return [
            'id' => $row['CLASS_ID'],
            'className' => $row['CLASS_NAME'],
            'date' => $row['DATE'],
            'createdAt' => strtotime($row['DATE'] . ' ' . $row['TIME']), // タイムスタンプ計算
            'attendanceList' => get_attendance_list($row['CLASS_ID']) // 出席者リスト取得
        ];
    }
    return null;
}

// 特定の授業の出席リストを取得
function get_attendance_list($classId) {
    $pdo = get_db_connection();
    // 結合して名前を取得
    $sql = "SELECT t.*, u.NAME, u.STUDENT_NUMBER 
            FROM TBL_ATTENDANCE_STATUS t
            JOIN MST_USER u ON t.USER_ID = u.USER_ID
            WHERE t.CLASS_ID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$classId]);
    
    $list = [];
    $statusMap = [0 => '出席', 1 => '遅刻', 2 => '早退', 3 => '欠席'];

    while ($row = $stmt->fetch()) {
        $list[] = [
            'studentId' => $row['STUDENT_NUMBER'],
            'studentName' => $row['NAME'],
            'status' => $statusMap[$row['ATTENDANCE_STATUS']] ?? '不明',
            'timestamp' => 0 // DBに登録日時がないため、必要ならカラム追加を推奨（今回は省略）
        ];
    }
    return $list;
}

// 出席登録
function submit_attendance_record($studentUserId, $classId, $statusInt) {
    $pdo = get_db_connection();
    
    // 重複チェック
    $stmt = $pdo->prepare("SELECT * FROM TBL_ATTENDANCE_STATUS WHERE USER_ID = ? AND CLASS_ID = ?");
    $stmt->execute([$studentUserId, $classId]);
    if ($stmt->fetch()) {
        return false; // 既に出席済み
    }

    // ID生成 (AT+5桁) ※排他制御考慮なしの簡易版
    $stmtCount = $pdo->query("SELECT COUNT(*) FROM TBL_ATTENDANCE_STATUS");
    $cnt = $stmtCount->fetchColumn();
    $atId = 'AT' . str_pad($cnt + 1, 5, '0', STR_PAD_LEFT);

    $sql = "INSERT INTO TBL_ATTENDANCE_STATUS (ATTENDANCE_ID, ATTENDANCE_STATUS, USER_ID, CLASS_ID) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$atId, $statusInt, $studentUserId, $classId]);
}

// 全履歴取得（教員用リスト）
function get_records() {
    $pdo = get_db_connection();
    $sql = "SELECT 
                t.ATTENDANCE_ID as id,
                c.CLASS_NAME as className,
                c.DATE as date,
                u.STUDENT_NUMBER as studentId,
                u.NAME as studentName,
                t.ATTENDANCE_STATUS as statusNum
            FROM TBL_ATTENDANCE_STATUS t
            JOIN TBL_CLASS c ON t.CLASS_ID = c.CLASS_ID
            JOIN MST_USER u ON t.USER_ID = u.USER_ID
            ORDER BY c.DATE DESC";
            
    $stmt = $pdo->query($sql);
    $records = [];
    $statusMap = [0 => '出席', 1 => '遅刻', 2 => '早退', 3 => '欠席'];

    while ($row = $stmt->fetch()) {
        $records[] = [
            'id' => $row['id'],
            'className' => $row['className'],
            'date' => $row['date'],
            'studentId' => $row['studentId'],
            'studentName' => $row['studentName'],
            'status' => $statusMap[$row['statusNum']] ?? '-',
            'comment' => '' // DB定義にコメントがないため空
        ];
    }
    return $records;
}

// 授業一覧取得（ダッシュボード用）
function get_attendance_forms() {
    $pdo = get_db_connection();
    $sql = "SELECT * FROM TBL_CLASS ORDER BY `DATE` DESC, `TIME` DESC";
    $stmt = $pdo->query($sql);
    
    $forms = [];
    while ($row = $stmt->fetch()) {
        $forms[] = [
            'id' => $row['CLASS_ID'],
            'className' => $row['CLASS_NAME'],
            'code' => $row['ATTENDANCE_CODE'],
            'attendanceList' => get_attendance_list($row['CLASS_ID'])
        ];
    }
    return $forms;
}

// ログイン処理ヘルパー
function login_user($user) {
    $_SESSION['user'] = $user;
}
function logout_user() {
    unset($_SESSION['user']);
    session_destroy();
}
?>

function delete_record(string $id): void {
    $idx = find_record_index_by_id($id);
    if ($idx !== null) {
        $rec = $_SESSION['attendanceRecords'][$idx];
        array_splice($_SESSION['attendanceRecords'], $idx, 1);
        // フォーム側との整合性
        foreach ($_SESSION['attendanceForms'] as &$form) {
            if ($form['className'] === $rec['className'] && $form['date'] === $rec['date']) {
                $form['attendanceList'] = array_filter($form['attendanceList'], function($s) use ($rec) {
                    return ($s['studentId'] ?? '') !== $rec['studentId'];
                });
            }
        }
    }
}