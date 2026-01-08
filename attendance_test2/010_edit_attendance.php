<?php
// 1. 【準備】データベースに接続する設定を読み込みます。
require_once 'db_connect.php';

// URLの「?id=10」などの部分から、編集したいデータの番号を拾います。
// もし番号がなければ「null（空っぽ）」にします。
$id = $_GET['id'] ?? null;

// URLに「?action=new」と書いてあれば「新規作成モード」だと判断します。
$is_new = (isset($_GET['action']) && $_GET['action'] === 'new');

// 画面に出すエラーメッセージと、入力欄に表示する初期データを用意しておきます。
$msg = ''; 
$data = ['STUDENT_NUMBER' => '', 'DATE' => date('Y-m-d'), 'CLASS_NAME' => '', 'ATTENDANCE_STATUS' => '出席'];

// 2. 【保存ボタンが押された時の処理】
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 画面の入力欄から送られてきたデータを変数にまとめます。
    $s_num = $_POST['student_number'];
    $date = $_POST['date'];
    $c_name = $_POST['class_name'];
    $status = $_POST['status'];

    // 3. 【名前の逆引き】学籍番号から「ユーザーID（通し番号）」を調べます。
    // 出席テーブルには学籍番号ではなく、ユーザーIDで保存する決まりだからです。
    $u_stmt = $pdo->prepare("SELECT USER_ID FROM mst_user WHERE STUDENT_NUMBER = ?");
    $u_stmt->execute([$s_num]);
    $user = $u_stmt->fetch();

    // 4. 【授業の逆引き】日付と授業名から「授業ID」を調べます。
    // 「2023-10-25のプログラミング」が、何番の授業なのかを特定します。
    $c_stmt = $pdo->prepare("SELECT CLASS_ID FROM tbl_class WHERE DATE = ? AND CLASS_NAME = ?");
    $c_stmt->execute([$date, $c_name]);
    $class = $c_stmt->fetch();

    // 5. 【エラーチェック】入力された学籍番号や授業が実在するか確認します。
    if (!$user) {
        $msg = "エラー：学籍番号「{$s_num}」が見つかりません。";
    } elseif (!$class) {
        $msg = "エラー：{$date} の「{$c_name}」という授業が見つかりません。";
    } else {
        // 6. 【データの保存】
        if ($is_new) {
            // 新規作成モードなら、新しい行を「追加（INSERT）」します。
            $sql = "INSERT INTO tbl_attendance_status (USER_ID, CLASS_ID, ATTENDANCE_STATUS, TIMESTAMP) VALUES (?, ?, ?, NOW())";
            $pdo->prepare($sql)->execute([$user['USER_ID'], $class['CLASS_ID'], $status]);
        } else {
            // 編集モードなら、すでにある行を「上書き（UPDATE）」します。
            $sql = "UPDATE tbl_attendance_status SET USER_ID = ?, CLASS_ID = ?, ATTENDANCE_STATUS = ? WHERE ATTENDANCE_ID = ?";
            $pdo->prepare($sql)->execute([$user['USER_ID'], $class['CLASS_ID'], $status, $id]);
        }
        // 保存が終わったら、一覧画面（009）に戻ります。
        header('Location: 009_attendance_list.php');
        exit;
    }
}

// 7. 【初期データの読み込み】編集モードの時だけ、今現在の登録内容をデータベースから取ってきます。
if ($id && !$is_new) {
    $stmt = $pdo->prepare(
        "SELECT a.*, u.STUDENT_NUMBER, c.CLASS_NAME, c.DATE 
    FROM tbl_attendance_status a 
    JOIN mst_user u ON a.USER_ID = u.USER_ID 
    JOIN tbl_class c ON a.CLASS_ID = c.CLASS_ID 
    WHERE a.ATTENDANCE_ID = ?"
    );
    $stmt->execute([$id]);
    $data = $stmt->fetch();
}
require_once 'header.php';
?>

<div class="max-w-lg mx-auto bg-white p-8 rounded shadow-lg mt-10">
    <h2 class="text-xl font-bold mb-6"><?= $is_new ? '新規記録の追加' : '出席記録の編集' ?></h2>
    
    <?php if($msg): ?><p class="text-red-500 mb-4 font-bold"><?= $msg ?></p><?php endif; ?>

    <form method="post" class="space-y-4">
        <div>
            <label class="block font-bold mb-1">学籍番号</label>
            <input type="text" name="student_number" value="<?= htmlspecialchars($data['STUDENT_NUMBER']) ?>" class="w-full border p-2 rounded" placeholder="例: K000C0000" required>
        </div>

        <div>
            <label class="block font-bold mb-1">日付</label>
            <input type="date" name="date" value="<?= htmlspecialchars($data['DATE']) ?>" class="w-full border p-2 rounded" required>
        </div>

        <div>
            <label class="block font-bold mb-1">授業名</label>
            <input type="text" name="class_name" value="<?= htmlspecialchars($data['CLASS_NAME']) ?>" class="w-full border p-2 rounded" placeholder="例: プログラミング演習" required>
        </div>

        <div>
            <label class="block font-bold mb-1">状況</label>
            <select name="status" class="w-full border p-2 rounded">
                <option value="出席" <?= $data['ATTENDANCE_STATUS'] == '出席' ? 'selected' : '' ?>>出席</option>
                <option value="遅刻" <?= $data['ATTENDANCE_STATUS'] == '遅刻' ? 'selected' : '' ?>>遅刻</option>
                <option value="欠席" <?= $data['ATTENDANCE_STATUS'] == '欠席' ? 'selected' : '' ?>>欠席</option>
            </select>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded font-bold hover:bg-blue-700">保存する</button>
            <a href="009_attendance_list.php" class="block text-center text-gray-400 mt-4 hover:underline">キャンセル</a>
        </div>
    </form>
</div>