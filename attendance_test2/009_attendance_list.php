<?php
// 1. 【準備】データベースに接続する設定を読み込みます。
require_once 'db_connect.php';

// データベース操作用のリモコン（$pdo）を使えるようにします。
$pdo = getDB();

// 2. 【削除処理】もし「削除ボタン」が押されてデータが送られてきたら動きます。
if (isset($_POST['delete'])) {
    // 指定されたIDの記録を「出席状況テーブル（tbl_attendance_status）」から消去します。
    // DELETE文は、名簿から1行分を消しゴムで消すような命令です。
    $pdo->prepare("DELETE FROM tbl_attendance_status WHERE ATTENDANCE_ID = ?")->execute([$_POST['id']]);
}

// 3. 【全データの取得】表に表示するための情報をデータベースから全部持ってきます。
// ここでも「LEFT JOIN」を使って、3つの名簿を合体させています。
// 出席記録(a)に、ユーザー名簿(u)と授業名簿(c)をくっつけて、「誰がどの授業に出たか」を1行にまとめます。
// ORDER BY で「日付が新しい順」、同じ日なら「時間が新しい順」に並べています。
$list = $pdo->query(
    "SELECT a.*, u.NAME, u.STUDENT_NUMBER, c.CLASS_NAME, c.DATE ,
    FROM tbl_attendance_status a ,
    LEFT JOIN mst_user u ON a.USER_ID = u.USER_ID ,
    LEFT JOIN tbl_class c ON a.CLASS_ID = c.CLASS_ID ,
    ORDER BY c.DATE DESC, a.TIMESTAMP DESC"
    )->fetchAll();

// 4. 【見た目の準備】HTMLの頭の部分を読み込みます。
require_once 'header.php';
?>

<div class="max-w-5xl mx-auto p-6 bg-white shadow mt-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">出席記録一覧</h2>
        
        <div>
            <a href="010_edit_attendance.php?action=new" class="bg-green-600 text-white px-4 py-2 rounded font-bold">
                + 新規記録追加
            </a>
            <a href="002_teacher_dashboard.php" class="ml-4 text-gray-500">戻る</a>
        </div>
    </div>

    <table class="w-full border-collapse">
        <tr class="bg-gray-100 border-b">
            <th class="p-3">日付</th>
            <th class="p-3">授業名</th>
            <th class="p-3">学籍番号</th>
            <th class="p-3">名前</th>
            <th class="p-3">状況</th>
            <th class="p-3">操作</th>
        </tr>

        <?php foreach ($list as $row): ?>
        <tr class="border-b text-center hover:bg-gray-50">
            <td class="p-3"><?= $row['DATE'] ?></td>
            <td class="p-3"><?= $row['CLASS_NAME'] ?></td>
            <td class="p-3"><?= $row['STUDENT_NUMBER'] ?></td>
            <td class="p-3"><?= $row['NAME'] ?></td>
            <td class="p-3"><?= $row['ATTENDANCE_STATUS'] ?></td>
            
            <td class="p-3 flex justify-center gap-2">
                <a href="010_edit_attendance.php?id=<?= $row['ATTENDANCE_ID'] ?>" class="text-blue-600 border px-2 rounded">
                    編集
                </a>
                
                <form method="post">
                    <input type="hidden" name="id" value="<?= $row['ATTENDANCE_ID'] ?>">
                    <button name="delete" class="text-red-600 border px-2 rounded" onclick="return confirm('本当に消しますか？')">
                        削除
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>