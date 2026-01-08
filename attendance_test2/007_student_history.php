<?php
// 1. 【準備】データベースに接続するための設定を読み込みます。
require_once 'db_connect.php';

// データベースを操作するための「リモコン（$pdo）」を使えるようにします。
$pdo = getDB();

// 2. 【命令書の作成】データベースから「自分の出席データ」を取り出すための指示書（SQL）を作ります。
// ここで「JOIN（結合）」という技を使っています。
// 「出席記録（tbl_attendance_status）」には授業名が書いていないので、
// 「授業リスト（tbl_class）」と合体させて、授業名も一緒に連れてくるようにしています。
$stmt = $pdo->prepare(
    "SELECT a.*, c.CLASS_NAME, c.DATE,
    FROM tbl_attendance_status a ,
    JOIN tbl_class c ON a.CLASS_ID = c.CLASS_ID ,
    WHERE a.USER_ID = ? ,
    ORDER BY c.DATE DESC"
    );

// 3. 【命令の実行】「?」の部分に、ログインしている自分のID（セッションに保存されているもの）を当てはめます。
// 「ORDER BY c.DATE DESC」と書くことで、新しい日付順に並べ替えています。
$stmt->execute([$_SESSION['user_id']]);

// 4. 【データの受け取り】見つかった全てのデータを $history という名前の箱（配列）に一気に詰め込みます。
$history = $stmt->fetchAll();

// 5. 【見た目の準備】共通のヘッダー（HTMLの頭の部分）を読み込みます。
require_once 'header.php';
?>

<div class="max-w-2xl w-full mx-auto p-4">
    <h2 class="text-2xl font-bold mb-6">自分の出席履歴</h2>
    
    <table class="w-full bg-white rounded shadow">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-3">日付</th>
                <th class="p-3">授業</th>
                <th class="p-3">状況</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $row): ?>
            <tr class="border-b text-center">
                <td class="p-3"><?= $row['DATE'] ?></td>
                
                <td class="p-3"><?= $row['CLASS_NAME'] ?></td>
                
                <td class="p-3 text-green-600 font-bold"><?= $row['ATTENDANCE_STATUS'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="005_student_dashboard.php" class="block text-center mt-6">戻る</a>
</div>