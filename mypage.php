<?php
    session_start();
?>
<link rel="stylesheet" href="css/mypage.css">
<link rel="stylesheet" href="css/header.css">
<?php

    if(isset($_SESSION["userID"]) ){
        $userName = $_SESSION["userName"];
        $userID = $_SESSION["userID"];
    }else{
        header("Location:Error.php");
    }
    // if(isset($_GET['userID'])){
    //     $pageID = $_GET['userID'];
    // }else{
    //     header("Location:Error.php");
    // }
    
    require_once("headerLogin.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="displayAjax.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script> 
    
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
    <div class="contents">
        <div class="profile">
            <h1 class="name"><?=$userName?>さんのページ</h1>
            
        </div>
        <a href="myimage.php" class="edit-profile">プロフィール編集</a><br>
        <form action="" id="select-form">
            <select name="displaySetting" id="displaySetting" onchange="getData()">
                <option value="future">今後の予定</option>
                <option value="past">過去の予定</option>
            </select>
            <input id="userID"type="hidden" name="" value="<?=$userID?>">
            
        </form>
        
        <div id="events"></div>
    </div>
    <footer>
        <p id="copy">
            &copy;omrn
        </p>
    </footer>
    <script>
        getData();
    </script>
</body>
</html>