<?php
session_start();

require_once('dbc.php');
require_once("headerLogin.php");
$event = $_POST;
//$event['capacity'] = mb_convert_kana($event['capacity'],'n','UTF-8');
$link_a = "./event_createForm.php";
$link_a_text = "戻る";
$userID = $_SESSION["userID"];


$date = $event['date']." ".$event['time'];

$sql = 'INSERT INTO
            events(eventname, eventdetail, place, lat, lng, eventdate, capacity, userid)
        VALUES
            (:eventname, :eventdetail, :place, :lat, :lng, :eventdate, :capacity, :userid)';
$dbh = dbConnect();
$dbh->beginTransaction();
try {
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':eventname',$event['title'],PDO::PARAM_STR);
    $stmt->bindValue(':eventdetail',$event['content'],PDO::PARAM_STR);
    $stmt->bindValue(':place',$event['place'],PDO::PARAM_STR);
    $stmt->bindValue(':lat',$event['lat'],PDO::PARAM_STR);
    $stmt->bindValue(':lng',$event['lng'],PDO::PARAM_STR);
    $stmt->bindValue(':eventdate',$date,PDO::PARAM_STR);
    $stmt->bindValue(':capacity',$event['capacity'],PDO::PARAM_STR);
    $stmt->bindValue(':userid',$userID,PDO::PARAM_INT);
    //$stmt->bindValue(':keywords',$event['keywords'],PDO::PARAM_STR);
    $stmt->execute();
    $dbh->commit();
} catch(PDOException $e){
    $dbh->rollBack();
    header("Location: ErrorPage.php");
}
?>
<!DOCTYPE html>
<html lang="ja">
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
        <p>イベントの作成が完了しました</p>
        <a href="./event_createForm.php">戻る</a>
    </div>
    <footer>
        <p id="copy">
            &copy;omrn
        </p>
    </footer>
    
</body>

</html>