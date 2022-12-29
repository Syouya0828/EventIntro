<?php
    session_start();
    if(isset($_SESSION["userID"]) ){
        $userName = $_SESSION["userName"];
        $userID = $_SESSION["userID"];
    }else{
        header("Location:Error.php");
    }
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
    <h2><?=$userName?>さんのページ</h2>
    <form action="">
        <select name="displaySetting" id="displaySetting" onchange="getData()">
            <option value="all">全ての予定</option>
            <option value="past">過去の予定</option>
            <option value="future">今後の予定</option>
        </select>
    </form>
    <div id="result"></div>
    <script>
        getData("<?=$userName?>");
    </script>

</body>
</html>