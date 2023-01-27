<?php

// セッション開始
session_start();
require_once('dbc.php');
if(isset($_SESSION["userID"]) ){
    header("Location:index.php");
}
require_once("headerLogin.php");
$db['host'] = "localhost";  // DBサーバのURL
$db['user'] = "eventuser";  // ユーザー名
$db['pass'] = "omrn2022";  // ユーザー名のパスワード
$db['dbname'] = "event_intro";  // データベース名

// エラーメッセージ、登録完了メッセージの初期化
$errorMessage = "";
$signUpMessage = "";

// ログインボタンが押された場合
if (isset($_POST["signUp"])) {
    // 1. ユーザIDの入力チェック
    if (empty($_POST["username"])) {  // 値が空のとき
        $errorMessage = 'ユーザーIDが未入力です。';
    } else if (empty($_POST["mailaddress"])) {
        $errorMessage = 'メールアドレスが未入力です。';
    } else if (empty($_POST["password"])) {
        $errorMessage = 'パスワードが未入力です。';
    } else if (empty($_POST["password2"])) {
        $errorMessage = 'パスワードが未入力です。';
    }

    if (!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["password2"]) && $_POST["password"] === $_POST["password2"]) {
        // 入力したユーザIDとパスワードを格納
        $username = $_POST["username"];
        $mailaddress = $_POST["mailaddress"];
        $password = $_POST["password"];

        // 2. ユーザIDとパスワードが入力されていたら認証する
        $dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

        // 3. エラー処理
        try {
            $pdo = dbConnect();

            $stmt = $pdo->prepare("INSERT INTO users(username, mailaddress, password) VALUES (?, ?, ?)");

            $stmt->execute(array($username, $mailaddress, password_hash($password, PASSWORD_DEFAULT)));  // パスワードのハッシュ化
            $userid = $pdo->lastinsertid();
            $signUpMessage = '登録が完了しました。あなたは '. $userid. ' 番目に登録しました(^^♪パスワードは '. $password. ' です。ユーザー名とパスワードを忘れないようにしてください';  // ログイン時に使用するIDとパスワード
            header("Location: Login.php");
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
          
        }
    } else if($_POST["password"] != $_POST["password2"]) {
        $errorMessage = 'パスワードに誤りがあります。';
    }
}
?>

<html>
    <head>
            <meta charset="UTF-8">
            <title>新規登録</title>
    </head>
    <body>
        <link rel="stylesheet" href="SignUp.css">
        <link rel="stylesheet" href="css/header.css">
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

                <h4>新規登録フォーム</h4>
                <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
                <div><font color="#0000ff"><?php echo htmlspecialchars($signUpMessage, ENT_QUOTES); ?></font></div>
                <label for="username"></label><input type="text" style="width: 700px; height: 50px; border: 2px solid #93e6B4; border-radius: 0.67em; padding: 0.5em; " id="username" name="username" placeholder="ユーザー名を入力" value="<?php if (!empty($_POST["username"])) {echo htmlspecialchars($_POST["username"], ENT_QUOTES);} ?>">
                <br>
                <br><label for="mailaddress"></label><input type="text" style="width: 700px; height: 50px; border: 2px solid #93e6B4; border-radius: 0.67em; padding: 0.5em; " id="mailaddress" name="mailaddress" value="" placeholder="メールアドレスを入力してください">
                <br>
                <br><label for="password"></label><input type="text" style="width: 700px; height: 50px; border: 2px solid #93e6B4; border-radius: 0.67em; padding: 0.5em; " id="password" name="password" value="" placeholder="パスワードを入力してください">
                <br>
                <br><label for="password2"></label><input type="password" style="width: 700px; height: 50px; border: 2px solid #93e6B4; border-radius: 0.67em; padding: 0.5em; " id="password2" name="password2" value="" placeholder="再度パスワードを入力してください">
                <br>
                <h2><input type="submit" style="width: 200px; height: 50px;" id="signUp" name="signUp" value="新規登録"></h2>
        </form>
        <br>
        <form action="Login.php">
            <input type="submit" value="戻る">
        </form>
        <footer>
        <p id="copy">
            &copy;omrn
        </p>
    </footer>
    </body>
</html>