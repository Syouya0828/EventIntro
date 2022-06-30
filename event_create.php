<?php
require_once('dbc.php');
$event = $_POST;
$link_a = "./event_create.html";
$link_a_text = "戻る";



echo ($event['date']);
if (empty($event['title'])) {
    echo "タイトルを入力してください<br/>";
    exit ("<a href=". $link_a .">". $link_a_text ."</a>");
}

if (empty($event['content'])) {
    echo "内容を入力してください<br/>";
    exit ("<a href=". $link_a .">". $link_a_text ."</a>");
}

if (empty($event['place'])) {
    echo "場所を入力してください<br/>";
    exit ("<a href=". $link_a .">". $link_a_text ."</a>");
}
if (empty($event['date'])) {
    echo "日付を入力してください<br/>";
    exit ("<a href=". $link_a .">". $link_a_text ."</a>");
}

if (empty($event['capacity'])) {
    echo "参加人数を入力してください<br/>";
    exit ("<a href=". $link_a .">". $link_a_text ."</a>");
}

//SQLにデータを入れる
$sql = 'INSERT INTO
            events(eventname, eventdetail, place, eventdate, capacity, keywords)
        VALUES
            (:eventname, :eventdetail, :place, :eventdate, :capacity, :keywords)';
$dbh = dbConnect();
$dbh->beginTransaction();
try {
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':eventname',$event['title'],PDO::PARAM_STR);
    $stmt->bindValue(':eventdetail',$event['content'],PDO::PARAM_STR);
    $stmt->bindValue(':place',$event['place'],PDO::PARAM_STR);
    $stmt->bindValue(':eventdate',$event['date'],PDO::PARAM_STR);
    $stmt->bindValue(':capacity',$event['capacity'],PDO::PARAM_STR);
    $stmt->bindValue(':keywords',$event['title'],PDO::PARAM_STR);
    $stmt->execute();
    $dbh->commit();
    echo 'イベントを作成しました';
} catch(PDOException $e){
    $dbh->rollBack();
    exit($e);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <a href="./event_create.html">戻る</a>
</body>
</html>