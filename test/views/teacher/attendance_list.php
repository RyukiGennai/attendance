<?php
// ...
$records = get_records(); // DBから取得（JOIN済み）

// 検索ロジック
$searchDate = $_GET['searchDate'] ?? '';
$searchClass = $_GET['searchClassName'] ?? '';
$searchName = $_GET['searchName'] ?? '';

$filtered = array_filter($records, function($r) use($searchDate,$searchClass,$searchName){
    // DBのDATE型と比較
    $matchDate = !$searchDate || $r['date'] === $searchDate;
    $matchClass = !$searchClass || mb_strpos($r['className'],$searchClass) !== false;
    $matchName = !$searchName || mb_strpos($r['studentName'],$searchName) !== false;
    return $matchDate && $matchClass && $matchName;
});
// ... 以下のHTML表示部分は変更なしで動作するように配列キーを合わせています ...
?>