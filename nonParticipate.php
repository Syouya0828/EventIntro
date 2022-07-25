<?php
    session_start();

    if($_POST['csrf'] === $_SESSION['csrfToken']){
        //POSTのデータを取ってくる
        $eventid = $_POST["eventid"];
        //$userid = $_SESSION['userid'];
        $userid = 1;
    }else{
        exit("エラー");
    }
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
    function getPtState($eventid, $userid){
        try {
            
            $sql = "SELECT * FROM ptstate WHERE ptstate.eventid=:eventid AND ptstate.userid=:userid";
            $dbh = dbConnect();
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':eventid',$eventid, PDO::PARAM_INT);
            $stmt->bindValue(':userid',$userid, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $dbh = NULL;
            return($result);
        } catch (PDOException $e) {
            echo '接続失敗'.$e -> getMessage();
            die();
        }
    }
    function updatePtState($result, $userid, $eventid){
        $dbh = dbConnect();
        $userid = 1;
        //参加表示しているかの確認
        if($result == NULL){//参加表示していない場合
            //INSERT
            $sql = "INSERT INTO
                ptstate(eventid, userid, state)
            VALUES
                (:eventid, :userid, :state)";
            try {
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(':eventid', $eventid, PDO::PARAM_INT);
                $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
                $stmt->bindValue(':state', '1', PDO::PARAM_INT);
                $stmt->execute();
                $dbh = NULL;
                header( "Location: view.php?commentPage=1&id=".$eventid);
            }catch (PDOException $e){
                print "エラー:".$e->getMessage()."</br>";
                die();
            }   
        }elseif($result["state"] == '1'){
            //元に戻す
            header( "Location: view.php?commentPage=1&id=".$eventid);
        }else{//参加表示している場合
            //UPDATE
            $sql = "UPDATE ptstate SET state = :state WHERE userid = :userid AND eventid = :eventid";
            try {
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(':state', '1', PDO::PARAM_INT);
                $stmt->bindValue(':eventid', $eventid, PDO::PARAM_INT);
                $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
                $stmt->execute();
                $dbh = NULL;
                header( "Location: view.php?commentPage=1&id=".$eventid);
            }catch (PDOException $e){
                print "エラー:".$e->getMessage()."</br>";
                die();
            } 
        }

    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
    <?php
    $result = getPtState($eventid, $userid);
    updatePtState($result, $eventid, $userid);
    //var_dump($result);
    ?>
</body>
</html>