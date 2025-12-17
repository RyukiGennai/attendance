<?php
require 'db_connect.php';
header('Content-Type: application/json');

if (!isset($_GET['class_id'])) {
    echo json_encode([]);
    exit;
}

$classId = $_GET['class_id'];

// 出席者一覧取得
$sql = "
    SELECT 
        s.ATTENDANCE_ID, 
        s.ATTENDANCE_STATUS, 
        s.TIMESTAMP, 
        u.NAME as studentName, 
        u.STUDENT_NUMBER as studentId
    FROM tbl_attendance_status s
    JOIN mst_user u ON s.USER_ID = u.USER_ID
    WHERE s.CLASS_ID = ?
    ORDER BY s.TIMESTAMP DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$classId]);
$rows = $stmt->fetchAll();

echo json_encode($rows);
?>