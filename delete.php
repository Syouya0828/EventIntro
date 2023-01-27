<?php
var_dump($_POST);
require_once('dbc.php');

try {
    $dbh = dbConnect();
    $sql = 'DELETE FROM events WHERE id=:id';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue('id', $_POST['eventID'],PDO::PARAM_INT);
    $stmt->execute();
    $dbh=NULL;
} catch (PDOException $e) {
    print "エラー発生：".$e->getMessage()."</br>";
    die();
}
header("Location: mypage.php");