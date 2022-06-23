<?php
// データベースに接続
function connectDB() {
    $param = 'mysql:dbname=event_intro;host=localhost';
    try {
        $pdo = new PDO($param, 'eventuser', 'omrn2022');
        return $pdo;

    } catch (PDOException $e) {
        exit($e->getMessage());
    }
}
?>