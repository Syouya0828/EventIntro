<?php
    session_start();


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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>コメント</title>
</head>
<body>
    <?php 
        if($_POST['csrf'] === $_SESSION['csrfToken']){
            $comment = $_POST['comment'];
            $id = $_POST['eventid'];
    
            //テスト
            $userid = 1;
    
            // jsエラーメッセージをviewに表示させる
            // コメントを登録する
            // セキュリティーを設定
            $dbh = dbConnect();
            $dbh->beginTransaction();
            try {
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $sql = 'INSERT INTO comments(comment, eventid, userid) VALUES (:comment, :eventid, :userid)';
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(':comment',$comment, PDO::PARAM_STR);
                $stmt->bindValue(':eventid',$id, PDO::PARAM_INT);
                $stmt->bindValue(':userid',$userid, PDO::PARAM_INT);
                $stmt->execute();
                $dbh->commit();
                $dbh = null;
                unset($_SESSION['csrfToken']);
                header( "Location: view.php?commentPage=1&id=".$id );
            } catch (PDOException $e) {
                echo '接続失敗'.$e -> getMessage();
                die();
            }
            
        }

    ?>
</body>
</html>