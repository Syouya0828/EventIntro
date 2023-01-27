<?php

session_start();
require_once("headerLogin.php");

if (isset($_SESSION["NAME"])) {
    $errorMessage = "ログアウトしました。";
} else {
    $errorMessage = "セッションがタイムアウトしました。";
}

// セッションの変数のクリア
$_SESSION = array();

// セッションクリア
@session_destroy();
?>

<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>ログアウト</title>
        <link rel="stylesheet" href="css/header.css">
        <link rel="stylesheet" href="css/result.css">
    </head>
    <body>
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
    <div id="contents">
        <h1>ログアウト画面</h1>
        <div><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></div>
        <ul>
            <li><a href="Login.php">ログイン画面に戻る</a></li>
        </ul>
    </div>

        <footer>
        <p id="copy">
            &copy;omrn
        </p>
    </footer>
    </body>
</html>