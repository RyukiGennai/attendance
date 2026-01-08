<?php
// 1. 【準備】データベースに接続するための設定を読み込みます。
require_once 'db_connect.php';

// getDB()を呼び出して、データベースを操作するための「リモコン（$pdo）」を受け取ります。
$pdo = getDB();

// 画面に表示するメッセージ（エラーなど）を入れておくための箱です。
$msg = '';

// 2. 【送信後の処理】「作成」ボタンが押された（POSTされた）ときだけ動くブロックです。
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 入力された「出席コード」を $code という変数に保存します。
    $code = $_POST['attendance_code'];

    // 3. 【入力チェック】コードが「英数字6文字」のルールに合っているか調べます。
    // preg_match は、文字の種類や長さをチェックする「型抜き」のようなものです。
    if (preg_match('/^[A-Z0-9]{6}$/', $code)) {
        
        // 4. 【URLの組み立て】学生がアクセスするための専用URLを作ります。
        // サーバーの名前や、このファイルの場所を自動で調べて、
        // 「005_student_dashboard.php?code=XXXXXX」という住所を作ります。
        $url = "http://". $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . "/005_student_dashboard.php?code=" . $code;

        // 5. 【データベースへ保存】「INSERT INTO」を使って、新しい授業の情報を登録します。
        // CLASS_NAME（授業名）、DATE（日付）、NOW()（今この瞬間）、コード、URL、誰が作ったか（先生ID）を保存します。
        $stmt = $pdo->prepare("INSERT INTO tbl_class (CLASS_NAME, DATE, TIME, ATTENDANCE_CODE, URL, USER_ID) VALUES (?, ?, NOW(), ?, ?, ?)");
        
        // 「?」の部分に、画面から送られてきた実際のデータを流し込みます。
        $stmt->execute([$_POST['class_name'], $_POST['date'], $code, $url, $_SESSION['user_id']]);

        // 6. 【次のページへ】保存が終わったら、自動的に「作成完了画面（004）」へ移動します。
        // その際、「何番目に保存されたデータか（ID）」を一緒に伝えます。
        header("Location: 004_class_created.php?id=" . $pdo->lastInsertId());
        exit; // 移動したので、ここでこのページの処理を終わらせます。

    } else {
        // もしコードが6文字じゃなかったり、変な記号が入っていたりしたら、注意書きを出します。
        $msg = "コードは英数字6桁にしてください";
    }
}

// 7. 【見た目】HTMLの頭の部分を読み込みます。
require_once 'header.php';
?>

<div class="max-w-lg w-full bg-white p-8 rounded shadow-lg mx-auto">
    <h2 class="text-2xl font-bold mb-6">出席フォーム作成</h2>
    
    <?php if ($msg): ?><p class="text-red-500 mb-4"><?= $msg ?></p><?php endif; ?>

    <form method="post" class="space-y-4">
        <input type="text" name="class_name" placeholder="授業名" class="w-full border p-3 rounded" required>
        
        <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="w-full border p-3 rounded" required>
        
        <div class="flex gap-2">
            <input type="text" id="code" name="attendance_code" placeholder="出席コード" maxlength="6" class="w-full border p-3 rounded" required>
            
            <button type="button" onclick="document.getElementById('code').value = Math.random().toString(36).substring(2,8).toUpperCase()" class="bg-gray-200 px-4 rounded">
                生成
            </button>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white p-4 rounded font-bold">作成</button>
    </form>

    <a href="002_teacher_dashboard.php" class="block text-center mt-6 text-gray-500">戻る</a>
</div>