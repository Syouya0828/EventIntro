<?php
function connectDB() {
    $param = 'mysql:dbname=event_intro;host=localhost';
    try {
        $pdo = new PDO($param, 'eventuser', 'omrn2022');
        return $pdo;

    } catch (PDOException $e) {
        exit($e->getMessage());
    }
}
$search =$_POST["search"];
$searchDate = $_POST["searchDate"];
$word =  htmlspecialchars($_POST["word"]);
$dbh = connectDB();
$dbh->exec("USE event_intro");
$sql = "";
session_start();
if($search == "date"){ //日付検索
    $sql = ("SELECT * FROM `events` WHERE eventdate BETWEEN '".$searchDate."' AND DATE_ADD('".$searchDate."', INTERVAL 60 DAY)");
    $prepare = $dbh->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['searchResult'] = $result;
    $dbh = NULL;
    header('Location: search.php?sHistory='.$search);
    exit();
}if(!empty($_POST["word"])){ //空の場合は検索しない
    if($search == "title"){ //タイトル検索
        $sql = ("SELECT * FROM events WHERE eventname LIKE '%".$word."%'");
        $prepare = $dbh->prepare($sql);
        $prepare->execute();
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        $_SESSION['searchResult'] = $result;
        $dbh = NULL;
        header('Location: search.php?sHistory='.$search.'&sWord='.$word);
        exit();
    }elseif($search == "titleDate"){ //タイトル日付検索
        $sql = ("SELECT * FROM events WHERE eventname LIKE '%".$word."%' AND eventdate BETWEEN '".$searchDate."' AND DATE_ADD('".$searchDate."', INTERVAL 60 DAY)");
        $prepare = $dbh->prepare($sql);
        $prepare->execute();
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
        $_SESSION['searchResult'] = $result;
        $dbh = NULL;
        header('Location: search.php?sHistory='.$search.'&sWord='.$word);
        exit();
    }
}else{
    $_SESSION['searchResult'] = "";
    $dbh = NULL;
    header('Location: search.php?sHistory='.$search);
}