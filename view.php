<?php
session_start();

/*
    TODO
    ・コメントIDの上限が超えた場合のエラーページ遷移
    ・コメントをログインしていないとできないようにする
    ・
*/

///SESSIONにデータがある場合
if(isset($_SESSION["userID"])){
    $userName = $_SESSION["userName"];
    $userID = $_SESSION["userID"];
}
//仮にgetからページのidを取ってくる場合
if(!isset($_GET['id']) || !isset($_GET['commentPage'])){
    exit("http://localhost/eventintro/view.php?commentPage=1&id=1");//エラーメッセージ
}else{
    $id = $_GET['id'];
    $commentPage = $_GET['commentPage'];

}


if(!isset($_SESSION['csrfToken'])){
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['csrfToken'] = $csrfToken;
}
$token = $_SESSION['csrfToken'];


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
/**
* ユーザー情報を取ってくる
*
* @param int $id ユーザーID 
* @return array ユーザー詳細
*/
function getUserData($id){//ユーザーのデータを取ってくる
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
/**
* クロスサイトスクリプティング（XSS）の対策
*
* @param string $str イベントの文字列
* @return string イベントの文字列
*/
function h($str){//XSS対策
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
* コメント内容を取ってくる
*
* @param int $id イベントID $commentPage コメントページの数字
* @return array コメント内容
*/
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
/**
* コメント数をカウントする
*
* @param int $id イベントID 
* @return array コメント数
*/
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

//ユーザーIDを取ってくる
function getPtStateData($id, $ptState){
    try {
        //idからPtStateを取ってくる
        $dbh = dbConnect();
        $sql = 'SELECT userid FROM ptstate WHERE ptstate.eventid=:eventid and ptstate.state=:ptState';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':eventid', $id, PDO::PARAM_INT);
        $stmt->bindValue(':ptState', $ptState, PDO::PARAM_INT);
        $stmt->execute();
        $State = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $dbh=NULL;
        return $State;
    } catch (PDOException $e) {
        echo '接続失敗'.$e -> getMessage();
        die();
    }



}

$result = getEventData($id);
//var_dump($result);
$keywords = explode(',', $result["keywords"]);
//var_dump($keyword);
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>イベント情報</title>
    </head>
    <body>
        <!-- イベントの詳細 -->
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
        
        <!-- 
            参加確認 
            SESSIONにログイン可否
        -->
        <?php
            $ptUsers = getPtStateData($id, 0);
            $nonPtUsers = getPtStateData($id, 1);

            //var_dump($ptUsers);
            
        ?>

        <div class="ptState">
            <!-- 参加 -->
            <form action="participate.php" method="post">
                <input type="hidden" name="eventid" value="<?=$id?>">
                <input type="hidden" name="csrf" value="<?=$token?>">
                <input class="state_btn" type="submit" value="参加する">
            </form>


            <ul>
                
            </ul>
            <!-- 不参加 -->
            <form action="nonParticipate.php" method="post">
                <input type="hidden" name="eventid" value="<?=$id?>">
                <input type="hidden" name="csrf" value="<?=$token?>">
                <input class="state_btn" type="submit" value="参加しない">
            </form>
            

            <!-- <form action="" method="post">
                <input type="hidden" name="eventid" value="<?=$id?>">
                <input type="hidden" name="csrf" value="<?=$token?>">
            </form> -->
        </div>

        
        
        <div class="pt">
            <p>参加する</p>
            <!-- foreachで出力 -->
            <ul>
                <?php
                foreach ($ptUsers as $ptUser) {
                    $userData = getUserData($ptUser['userid']);
                    //var_dump($userData);
                    echo('<li>'.$userData['username'].'</li>');
                }
                ?>
            </ul>
        </div>
        <div class="nopt">
            <p>参加しない</p>
            <ul>
                <?php
                foreach ($nonPtUsers as $nonPtUser) {
                    $userData = getUserData($nonPtUser['userid']);
                    //var_dump($userData);
                    echo('<li>'.$userData['username'].'</li>');
                }
                ?>
            </ul>
        </div>
        <!-- <div class=""></div> -->
        <!-- コメント投稿 -->
        <!-- 
            TODO    
            ログインしていないと投稿できない
        -->
        <div class="postComment">
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

        <!-- コメント表示 -->
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
                $displayNum = 15;
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