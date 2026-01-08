<?php
// 1. 【準備】データベースに接続するための設定ファイルを読み込みます。
// これで、情報を引き出すための「$pdo（データベース操作用のリモコン）」が使えるようになります。
require_once 'db_connect.php';

// 2. 【情報の受け取り】前のページ（003）から送られてきた「授業ID」をキャッチします。
// $_GET['id'] は、URLの「?id=123」という部分から「123」という数字を読み取ります。
// これが「どの授業を表示するか」を決めるための「荷札（にふだ）」になります。
$stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE CLASS_ID = ?");

// 3. 【検索の実行】荷札の番号を使って、データベースからその授業の情報を全部持ってきます。
$stmt->execute([$_GET['id']]);

// 4. 【変数へ保存】見つかった情報を $class という名前の箱（配列）に保存します。
// これで、$class['ATTENDANCE_CODE'] のように書けば、好きな情報を画面に出せるようになります。
$class = $stmt->fetch();

// 5. 【見た目の準備】HTMLの頭の部分（デザインの設定など）を読み込みます。
require_once 'header.php';
?>

<div class="max-w-lg bg-white p-8 rounded shadow mx-auto text-center mt-10">
    
    <h2 class="text-2xl font-bold mb-6 text-green-600">授業を作成しました</h2>
    
    <p class="text-gray-500 mb-4">学生に以下のコードを共有してください</p>

    <div class="mb-8 p-6 bg-blue-50 rounded border border-blue-200">
        <p class="text-sm text-blue-500 font-bold mb-2">出席コード</p>
        
        <p class="text-6xl font-mono font-bold text-blue-700"><?= htmlspecialchars($class['ATTENDANCE_CODE']) ?></p>
    </div>

    <a href="002_teacher_dashboard.php" class="block w-full bg-gray-800 text-white p-4 rounded font-bold">
        ダッシュボードへ戻る
    </a>
</div>