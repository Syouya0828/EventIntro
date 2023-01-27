<?php
session_start();
require_once('dbc.php');


$event = $_POST;
var_dump($event);
// $id = $_POST['id'];
// $eventname = htmlspecialchars($_POST['eventname']);
// $eventdetail = htmlspecialchars($_POST['eventdetail']);
// $place = htmlspecialchars($_POST['place']);
// $eventdate = htmlspecialchars($_POST['eventdate']);
// $capacity = htmlspecialchars($_POST['capacity']);
// $keywords = htmlspecialchars($_POST['keywords']);
// $update = $_POST['update'];
// $delete = $_POST['delete'];
// $dbh = connectDB();
// $dbh->exec("USE event_intro");
// //変更機能
// if($update != NULL){
//     $sql = "UPDATE events SET eventname=:eventname, eventdetail=:eventdetail, place=:place, eventdate=:eventdate, capacity=:capacity, keywords=:keywords WHERE id = '" .$id."'";
//     $prepare = $dbh->prepare($sql);
//     $prepare -> bindValue(':eventname', $eventname, PDO::PARAM_STR);
//     $prepare -> bindValue(':eventdetail', $eventdetail, PDO::PARAM_STR);
//     $prepare -> bindValue(':place', $place, PDO::PARAM_STR);
//     $prepare-> bindValue(':eventdate', $eventdate, PDO::PARAM_STR);
//     $prepare-> bindValue(':capacity', $capacity, PDO::PARAM_INT);
//     $prepare -> bindValue(':keywords', $keywords, PDO::PARAM_STR);
//     $prepare->execute();
// }
// //削除機能
// if($delete != NULL){
//     $sql = ("DELETE FROM events WHERE id = '" .$id."'");
//     $prepare = $dbh->prepare($sql);
//     $prepare->execute();
// }
// $dbh = NULL;
// //遷移先要変更
// header('Location: karimypage.php');
// exit();
$date = $event['date']." ".$event['time'];

try {
    $dbh = dbConnect();
    $dbh->beginTransaction();
    $sql = "UPDATE events SET eventname=:eventname, eventdetail=:eventdetail, place=:place, lat=:lat, lng=:lng, eventdate=:eventdate, capacity=:capacity WHERE id=:id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':eventname',$event['title'],PDO::PARAM_STR);
    $stmt->bindValue(':eventdetail',$event['content'],PDO::PARAM_STR);
    $stmt->bindValue(':place',$event['place'],PDO::PARAM_STR);
    $stmt->bindValue(':lat',$event['lat'],PDO::PARAM_STR);
    $stmt->bindValue(':lng',$event['lng'],PDO::PARAM_STR);
    $stmt->bindValue(':eventdate',$date,PDO::PARAM_STR);
    $stmt->bindValue(':capacity',$event['capacity'],PDO::PARAM_STR);
    $stmt->bindValue(':id',$event['id'],PDO::PARAM_INT);
    $stmt->execute();
    $dbh->commit();
    header("Location: mypage.php");
} catch(PDOException $e){
    $dbh->rollBack();
    echo '接続失敗'.$e -> getMessage();
    //exit($e);
}


//var_dump($_POST);

?>
