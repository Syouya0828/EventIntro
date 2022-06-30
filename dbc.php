<?php



function dbConnect(){
$dsn = 'mysql:host=localhost;dbname=event_intro;charset=utf8';
$user = 'eventuser';
$pass = 'omrn2022';
    try {
        $dbh = new PDO($dsn,$user,$pass,[
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        
    } catch(PDOException $e) {
        echo '接続失敗'. $e->getMessage();
        exit();
    };
    return $dbh;
}
$dbh = dbConnect();

