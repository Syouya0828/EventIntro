<?php
    session_start();
    sleep(1);

    // if(isset($_SESSION["userID"]) ){
    //     $userID = $_SESSION["userID"];
    // }else{
    //     header("Location:Error.php");
    // }

    function dbConnect(){//DB接続
        $user = "eventuser";
        $pass = "omrn2022";
    
        try {
            $dbh = new PDO('mysql:host=localhost;dbname=event_intro', $user, $pass);
        } catch (PDOException $e) {
            echo '接続失敗'.$e -> getMessage();
            die();
        }
        return $dbh;
    }

    function getData($sql, $id){
        try {
            $dbh = dbConnect();
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':id',$id, PDO::PARAM_STR);
            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $dbh = NULL;
            return($events);
        } catch (PDOException $e) {
            echo '接続失敗'.$e -> getMessage();
            die();
        }

    }


    $request = json_decode(file_get_contents("php://input"), true);
    //echo($request["select"]);
    
    //var_dump($userData);
    $today = date("Y-m-d");
    switch($request["select"]){
        case 'all':
            $sql = 'SELECT id,eventname,eventdate FROM events WHERE userid=:id';
            break;
        case 'past':
            $sql = 'SELECT id,eventname,eventdate FROM events WHERE userid=:id and eventdate<now() ORDER BY eventdate ASC';
            break;
        case 'future':
            $sql = 'SELECT id,eventname,eventdate FROM events WHERE userid=:id and eventdate>=now() ORDER BY eventdate ASC';
            break;
    }
    //echo($sql);
    //echo($request["userID"]);
    $events = getData($sql, $request["userID"]);


    $json = json_encode($events, JSON_UNESCAPED_UNICODE);
    header("Content-Type: application/json; charset=UTF-8");
    echo $json;
    