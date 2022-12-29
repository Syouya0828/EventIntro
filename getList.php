<?php
function getList(){
    require_once ('functions.php');
    $dbh = connectDB();
    $dbh->exec("USE event_intro");
    $sql = 'SELECT * FROM events ORDER BY id ASC';
    $prepare = $dbh->prepare($sql);
    $prepare->execute();
    $result = $prepare->fetchall(PDO::FETCH_ASSOC);
    $_SESSION['list'] = $result;
    $dbh = NULL;
}