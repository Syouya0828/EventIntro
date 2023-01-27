<?php

// セッション開始
session_start();
require_once("headerLogin.php");

if(isset($_SESSION['userID'])){
    header("Location: index.php");
}

$db['host'] = "localhost";  // DBサーバのURL
$db['user'] = "eventuser";  // ユーザー名
$db['pass'] = "omrn2022";  // ユーザー名のパスワード
$db['dbname'] = "event_intro";  // データベース名

// エラーメッセージの初期化
$errorMessage = "";

// ログインボタンが押された場合
if (isset($_POST["login"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["userid"])) {  // emptyは値が空のとき
        $errorMessage = 'ユーザーIDが未入力です。';
    } else if (empty($_POST["password"])) {
        $errorMessage = 'パスワードが未入力です。';
    }

    if (!empty($_POST["userid"]) && !empty($_POST["password"])) {
        // 入力したユーザIDを格納
        $userid = $_POST["userid"];

        // 2. ユーザIDとパスワードが入力されていたら認証する
        $dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

        // 3. エラー処理
        try {
            $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

            $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
            $stmt->execute(array($userid));

            $password = $_POST["password"];

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($password, $row['password'])) {
                    session_regenerate_id(true);

                    // 入力したIDのユーザー名を取得
                    $id = $row['id'];
                    $sql = "SELECT * FROM users WHERE id = $id";  //入力したIDからユーザー名を取得
                    $stmt = $pdo->query($sql);
                    foreach ($stmt as $row) {
                        $row['name'];  // ユーザー名
                    }
                    $_SESSION["userName"] = $row['username'];
                    $_SESSION["userID"] = $row['id'];
                    header("Location: index.php");  // メイン画面へ遷移
                    exit();  // 処理終了
                } else {
                    // 認証失敗
                    $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
                }
            } else {
                // 4. 認証成功なら、セッションIDを新規に発行する
                // 該当データなし
                $errorMessage = 'ユーザーIDあるいはパスワードに誤りがあります。';
            }
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
            //$errorMessage = $sql;
            // $e->getMessage() でエラー内容を参照可能（デバッグ時のみ表示）
            // echo $e->getMessage();
        }
    }
}
?>

<html>
    <head>
            <meta charset="UTF-8">
            <title>ログイン</title>
    </head>
    <body>
    <link rel="stylesheet" href="css/header.css">
        <link rel="stylesheet" href="Login.css">
        <header>
        <div class="logo">
            <a href="index.php"><img src="logo/logo.png"></a>
        </div>
        <nav>
            <ul class="nav_header">
                <li>
                    <a href="search.php">検索</a>
                </li>
                <?=$log?>
            </ul>
        </nav>
    </header>
        <form id="loginForm" name="loginForm" action="" method="POST">
            
                <h4>omrnログインフォーム</h4>
               <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
              <label for="userid"></label><input type="text" style="width: 700px; height: 50px; border: 2px solid #93e6B4; border-radius: 0.67em; padding: 0.5em; " id="userid" name="userid" placeholder="ユーザー名を入力してください" value="<?php if (!empty($_POST["userid"])) {echo htmlspecialchars($_POST["userid"], ENT_QUOTES);} ?>">
                <br>
                <h6><label for="password"></label><input type="password" style="width: 700px; height: 50px; border: 2px solid #93e6B4; border-radius: 0.67em; padding: 0.5em; " id="password" name="password" value="" placeholder="パスワードを入力"></h6>
                <br>
                <h2><input type="submit" style="width: 200px; height: 50px;"id="login" name="login" value="ログイン"></h2>
        </form>
        <br>
        <form action="SignUp.php">
                <h4>新規登録はこちら</h4>
                <input type="submit" value="新規登録">
            
        </form>
        <footer>
        <p id="copy">
            &copy;omrn
        </p>
    </footer>
    </body>
</html>