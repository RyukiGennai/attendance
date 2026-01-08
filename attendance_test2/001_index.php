<?php
// 1. 他のファイルから「データベース（情報の保管箱）への接続設定」を読み込みます。
// これで、$pdoという変数を使ってデータベースが操作できるようになります。
require_once 'db_connect.php';

// エラーメッセージを貯めておくための箱（変数）です。最初は空っぽにしておきます。
$error = '';

// 2. 「ログインボタン」が押されたかどうかを判定します。
// 画面の入力欄からデータが送られてきたとき（POSTされたとき）だけ実行されます。
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 3. 画面の入力欄（user_id と password）に書かれた文字を受け取って、変数に保存します。
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    // db_connect.phpで定義されている「データベースを使うための準備」を呼び出します。
    $pdo = getDB();

    // 4. データベースの中から、入力された「ユーザーID」と同じ人がいるか探します。
    // 「?」を使っているのは、悪いプログラム（SQL注射）を流し込まれないための安全対策です。
    $stmt = $pdo->prepare("SELECT * FROM mst_user WHERE USER_ID = ?");
    $stmt->execute([$user_id]);
    
    // 見つかった人の情報を $user という変数に取り出します。
    $user = $stmt->fetch();

    // 5. 「ユーザーが見つかったか」かつ「パスワードが合っているか」を確認します。
    if ($user && $user['PASSWORD'] === $password) {
        
        // 6. 【ログイン成功！】
        // セッションという「サーバー側の記憶スペース」に、ログインした人の情報を保存します。
        // これで、他のページに移動しても「誰がログイン中か」を忘れません。
        $_SESSION['user_id'] = $user['USER_ID']; // IDを記録
        $_SESSION['name'] = $user['NAME'];       // 名前を記録
        $_SESSION['role'] = $user['ROLE'];       // 先生か生徒かの役割を記録

        // 7. 「役割（ROLE）」を見て、移動先のページを振り分けます。
        if ($user['ROLE'] == 1) {
            // ROLEが1（先生）なら、先生用のメニューページへ飛ばします。
            header('Location: 002_teacher_dashboard.php');
        } else {
            // それ以外（生徒）なら、生徒用の出席入力ページへ飛ばします。
            header('Location: 005_student_dashboard.php');
        }
        // 移動したので、このページの処理はここで終了します。
        exit;
        
    } else {
        // 8. 【ログイン失敗…】
        // ユーザーが見つからないか、パスワードが違った場合にメッセージを表示します。
        $error = 'ユーザーIDまたはパスワードが違います';
    }
}

// 9. 画面の見た目を作るための部品（HTMLの頭の部分）を読み込みます。
require_once 'header.php';
?>

<div class="bg-white p-8 rounded shadow-md w-96 mx-auto mt-20">
    <h2 class="text-2xl font-bold mb-6 text-center">出席管理システム</h2>
    
    <?php if ($error): ?><p class="text-red-500 mb-4"><?= $error ?></p><?php endif; ?>
    
    <form method="post" class="space-y-4">
        <div>
            <label class="block mb-1 font-bold">ユーザーID</label>
            <input type="text" name="user_id" class="w-full border p-2 rounded" required>
        </div>
        <div>
            <label class="block mb-1 font-bold">パスワード</label>
            <input type="password" name="password" class="w-full border p-2 rounded" required>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded font-bold hover:bg-blue-700">ログイン</button>
    </form>
</div>