<?php
    //sleep(1);]
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
    function getUserID($userName){
        try {
            $dbh = dbConnect();
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $sql = 'SELECT id,username FROM users WHERE users.id=:id';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':id',$id, PDO::PARAM_INT);
            $stmt->execute();
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            $dbh = NULL;
            return($userData);
        } catch (PDOException $e) {
            echo '接続失敗'.$e -> getMessage();
            die();
        }
    }
    function getData($sql){
        try {
            $dbh = dbConnect();
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
        } catch (PDOException $e) {
            echo '接続失敗'.$e -> getMessage();
            die();
        }

    }


    $request = json_decode(file_get_contents("php://input"), true);
    //echo($request["select"]);
    
    $userID = getUserData();

    switch($request["select"]){
        case 'all':
            $sql = 'SELECT * FROM events ';
            break;
        case 'past':
            break;
        case 'future':
            break;
    }



    $result = array(
        'value' => $request["select"],
        'userName' => $request["userName"],
        'test' => "seikou"
    );

    $json = json_encode($result, JSON_UNESCAPED_UNICODE);
    header("Content-Type: application/json; charset=UTF-8");
    echo $json;
    