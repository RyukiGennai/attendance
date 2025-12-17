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
}

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