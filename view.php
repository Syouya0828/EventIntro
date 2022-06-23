<?php
session_start();

//仮にgetからページのidを取ってくる場合
if(!isset($_GET['id']) || !isset($_GET['commentPage'])){
    exit("うー");//エラーメッセージ
}
$id = $_GET['id'];
$commentPage = $_GET['commentPage'];

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
/**
* イベント情報を取ってくる
*
* @param int $id イベントID
* @return array イベント情報
*/
function getEventData($id){
    try {
        $dbh = dbConnect();
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $stmt = $dbh->prepare('select events.* from events where events.id=?');
        $stmt->bindValue(1, $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $dbh = NULL;
        if(!$result){
            // die("a");
            //ErrorPageへ
        }
        return $result;
    } catch (PDOException $e) {
        echo '接続失敗'.$e -> getMessage();
        die();
    }
}
function getUserData($id){//ユーザーのデータを取ってくる
    try {
        $dbh = dbConnect();
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        
    } catch (PDOException $e) {
        echo '接続失敗'.$e -> getMessage();
        die();
    }
}
function h($str){//XSS対策
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
function getComment($id, $commentPage){
    try {
        $displayNum = 10;
        $first = ($commentPage - 1) * $displayNum;
        $dbh = dbConnect();
        $sql = 'SELECT * FROM comments WHERE comments.eventid=:id order by comments.postdate desc LIMIT :first, :second';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':id',$id, PDO::PARAM_INT);
        $stmt->bindValue(':first',$first, PDO::PARAM_INT);
        $stmt->bindValue(':second',$displayNum, PDO::PARAM_INT);
        $stmt->execute();
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $dbh = NULL;
        return ($comments);
    } catch (PDOException $e) {
        echo '接続失敗'.$e -> getMessage();
        die();
    }

}
function countComments($id){
    
    try {
        $dbh = dbConnect();
        $sql = 'SELECT count(comments.comment) FROM comments WHERE comments.eventid=?';
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($id));
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        $dbh = null;
        //var_dump($count);
        return($count);
    }catch (PDOException $e){
        print "エラー:".$e->getMessage()."</br>";
        die();
    }  
}
$result = getEventData($id);
//var_dump($result);
$keywords = explode(',', $result["keywords"]);
//var_dump($keyword);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>イベント情報</title>
    </head>
    <body>
        <div id="event">
            <h1>イベント名:<?=h($result["eventname"])?></h1>
            <h2>イベント日時:<?=h($result["eventdate"])?></h2>
            <h2>イベント詳細:<?=h($result["eventdetail"])?></h2>
            <h2>イベント場所:<?=h($result["place"])?></h2>
            <h2>定員:<?=h($result["capacity"])?></h2>
            <div class="keywords">
            <?php foreach($keywords as $keyword): ?>
                <div class="keyword"><?=$keyword?></div>
            <?php endforeach; ?>
        </div>
        <div class="postComment">
            <?php
                if(!isset($_SESSION['csrfToken'])){
                    $csrfToken = bin2hex(random_bytes(32));
                    $_SESSION['csrfToken'] = $csrfToken;
                }
                $token = $_SESSION['csrfToken'];
            ?>
            <form action="postComment.php" method="POST">
                <p>コメント:</p>
                <input type="text" name="comment">
                <input type="hidden" name="eventid" value="<?=$id?>">
                <input type="hidden" name="csrf" value="<?=$token?>">
                <input type="submit" value="投稿">
            </form>
        </div>

        <?php
            $comments = getComment($id, $commentPage);
            //var_dump($comments);
        ?>
        <div class="comments">
            <!-- 表示させるのは id コメント内容 時間 -->
            <?php foreach($comments as $comment):?>
                <div id="userID"></div>
                <div id="date"><?=h($comment['postdate'])?></div>
                <div id="comment"><?=h($comment['comment'])?></div>
            <?php endforeach; ?>
            <?php 
                $count = countComments($id);
                $countNum = $count["count(comments.comment)"];
                $displayNum = 10;
                $maxPage = ceil($countNum / $displayNum);

                if($countNum > $displayNum):
                    if($commentPage > 1 && $commentPage!=$maxPage):?>
                        <a href="view.php?commentPage=<?=$commentPage-1?>&id=<?=$id?>">前へ</a>
                        <a href="view.php?commentPage=<?=$commentPage+1?>&id=<?=$id?>">次へ</a>
                    <?php
                    endif;
                    if($commentPage == $maxPage):
                    ?>
                        <a href="view.php?commentPage=<?=$commentPage-1?>&id=<?=$id?>">前へ</a>
                    <?php
                    endif;
                    if($commentPage == 1):
                    ?>
                        <a href="view.php?commentPage=<?=$commentPage+1?>&id=<?=$id?>">次へ</a>
                    <?php
                    endif;
                    if($commentPage > $maxPage):
                        header( "Location: view.php?commentPage=".$maxPage."&id=".$id );
                    endif;
                endif;
                    ?>
            
        </div>
    </body>
</html>