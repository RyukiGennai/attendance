<?php
// 1. 【セッション（記憶）の開始】
// サーバーが「今操作しているのが誰か」を忘れないようにするための仕組みです。
// すでに「記憶モード（セッション）」が始まっていなければ、新しく開始します。
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // 「出席管理システムのノート」を開いて、続きを書けるようにするイメージです。
}

// 2. 【データベースへの接続情報】
// 情報を保存している「倉庫（データベース）」の住所や、入るための鍵（パスワード）を設定します。
$host = 'localhost';      // 倉庫がある場所（自分のコンピュータ内）
$dbname = 'attendance_db'; // 使う倉庫の名前
$username = 'root';       // 管理者の名前
$password = 'root';       // 管理者のパスワード

// 3. 【データベースへの接続実行】
// 実際に倉庫にアクセスして、PHPから操作できるように「リモコン（$pdo）」を作ります。
try {
    // 倉庫への接続を試みます（住所、ポート番号3307、倉庫名、文字の種類などを指定）。
    $pdo = new PDO("mysql:host=$host;port=3307;dbname=$dbname;charset=utf8", $username, $password);
    
    // データベースからデータをもらう時、使いやすい「配列（箱）」の形で受け取るように設定します。
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // 4. 【エラーの時の安全対策】
    // もしパスワードが違ったり、倉庫が見つからなかったりした場合は、
    // 画面を止めて「エラーが起きたよ！」と理由（$e->getMessage()）を表示します。
    exit('DB Connection Error:' . $e->getMessage());
}

// 5. 【リモコンを貸し出す関数】
// 他のファイルから「データベースを使いたい！」と言われた時に、
// 作成したリモコン（$pdo）を渡してあげるための便利な関数です。
function getDB() {
    global $pdo; // 外側で作った $pdo を関数の内側でも使えるようにします。
    return $pdo; // リモコンを返してあげます。
}
?>