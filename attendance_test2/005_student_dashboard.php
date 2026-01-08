<?php
// 1. 【準備】データベースに接続するための設定を読み込みます。
require_once 'db_connect.php';

// データベースを操作するための「リモコン（$pdo）」を使えるようにします。
$pdo = getDB();

// 画面に表示するメッセージ（「コードが違うよ」など）を入れておくための箱です。
$msg = '';

// 2. 【URLからの情報取得】もしURLに「?code=ABCDEF」のようにコードが付いていたら、
// 自動的に入力欄にその文字が入るように準備しておきます。
$code = $_GET['code'] ?? '';

// 3. 【ボタンが押された時の処理】「出席を送信する」ボタンが押された（POSTされた）ら動きます。
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 入力欄に書かれたコードを $input_code という変数に入れます。
    $input_code = $_POST['code'];

    // 4. 【本物かチェック】入力されたコードが「授業テーブル（tbl_class）」にあるか探します。
    $stmt = $pdo->prepare("SELECT * FROM tbl_class WHERE ATTENDANCE_CODE = ?");
    $stmt->execute([$input_code]);
    
    // 見つかった授業の情報を $class に取り出します。
    $class = $stmt->fetch();

    // 5. 【もし授業が見つかったら】
    if ($class) {
        // 6. 【出席を記録】「出席状況テーブル（tbl_attendance_status）」にデータを新しく作ります。
        // 誰が（USER_ID）、どの授業に（CLASS_ID）、「出席」したかを記録します。
        // NOW() は「今この瞬間」の時間を自動で入れてくれる魔法の言葉です。
        $stmt = $pdo->prepare("INSERT INTO tbl_attendance_status (USER_ID, CLASS_ID, ATTENDANCE_STATUS, TIMESTAMP) VALUES (?, ?, '出席', NOW())");
        
        // ログイン中の自分のIDと、見つかった授業のIDをセットして実行します。
        $stmt->execute([$_SESSION['user_id'], $class['CLASS_ID']]);

        // 7. 【完了画面へ】記録が終わったら、「お疲れ様でした！」のページ（006）へ自動で移動します。
        header("Location: 006_attendance_complete.php");
        exit; // 移動したので、このページの処理はここで終わりにします。

    } else {
        // もしデータベースにそのコードがなければ、エラーメッセージを表示します。
        $msg = "無効なコードです";
    }
}

// 8. 【見た目の準備】共通のヘッダー（HTMLの頭の部分）を読み込みます。
require_once 'header.php';
?>

<div class="max-w-md w-full bg-white p-8 rounded shadow mx-auto mt-10">
    
    <h2 class="text-xl font-bold mb-4 text-center">出席送信</h2>

    <a href="logout.php" class="text-red-500 hover:underline">ログアウト</a>
    
    <p class="text-center mb-6"><?= htmlspecialchars($_SESSION['name'] ?? '学生') ?></p>

    <?php if($msg): ?><p class="text-red-500 text-center mb-4"><?= $msg ?></p><?php endif; ?>

    <form method="post" class="space-y-4">
        <input type="text" name="code" value="<?= htmlspecialchars($code) ?>" placeholder="出席コード入力" class="w-full border p-4 text-center text-2xl font-bold" required>
        
        <button type="submit" class="w-full bg-blue-600 text-white p-4 rounded font-bold text-lg">
            出席を送信する
        </button>
    </form>

    <a href="007_student_history.php" class="block text-center mt-6 text-blue-500">
        自分の出席履歴を見る
    </a>
</div>