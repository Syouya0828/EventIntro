<?php
session_start();

require_once("headerLogin.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/result.css">
    <title>Document</title>
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
        <p>ご指定のページは削除、または、非公開の可能性がございます。もしくは、ご指定のURLが違う可能性がございます。URLを再度お確かめのうえアクセスをお願いいたします。</p>
        <a href="index.php">メイン画面に戻る</a>
    </div>

    <footer>
        <p id="copy">
            &copy;omrn
        </p>
    </footer>
</body>

</html>