<?php
session_start();
require_once("headerLogin.php");
require_once "vendor/autoload.php";
Dotenv\Dotenv::createImmutable(__DIR__)->load();

$key = $_ENV['key'];


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
        $sql = 'SELECT id,username,image_type FROM users WHERE users.id=:id';
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
$userData = getUserData($result['userid']);
//var_dump($result);
$keywords = explode(',', $result["keywords"]);
//var_dump($keyword);
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
    <script src="http://maps.google.com/maps/api/js?key=<?=$key?>&language=ja"></script>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
        <title>イベント情報</title>
        <link rel="stylesheet" href="css/header.css">
        <link rel="stylesheet" href="css/view.css">
        <link rel="stylesheet" href="css/GoogleMap.css">
    </head>
    <body>
        <!-- イベントの詳細 -->
        <header>
        <div class="logo">
            <a href="index.php"><img src="logo/logo.png"></a>
        </div>
        <nav>
            <ul class="nav_header">
                <li>
                    <a href="search.php">検索</a>
                </li>
                <?=$log?>
            </ul>
        </nav>
    </header>
        
        <div id="event">
            <p>イベント日時:<?=substr(h($result["eventdate"]), 0, -3)?></p>
            <p id="title"><?=h($result["eventname"])?></p>
            <div id="user">
            
                <?php
                if($userData['image_type'] == NULL){
                    echo('<img src="logo/user.png" width=35 height=35 alt="icon">');
                }else{
                    echo('<img id="icon"src="images.php?userID='.$result['userid'].'" width=35 height=35 alt="icon">');
                }
                ?>

                <a id="username" href="user.php?pageID=<?=$userData['id']?>"><?=$userData['username']?></a>
            </div>

            <p class="holder">詳細</p>
            <p><?=$result["eventdetail"]?></p>
            <p class="holder">場所</p>
            <p><?=h($result["place"])?></p>
            <div class="mapContent"style="text-align:center;">
                <div id="map"></div>
            </div>
            
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
            <p>イベントに参加しますか</p>
            <div id="btn">
                <?php
                if(isset($_SESSION['userID'])){
                    echo('                <form action="participate.php" method="post">
                    <input type="hidden" name="eventid" value="'.$id.'">
                    <input type="hidden" name="csrf" value="'.$token.'">
                    <input class="pt_btn" type="submit" value="参加する">
                </form>');
                }
                ?>

                <!-- 不参加 -->
                <?php
                if(isset($_SESSION['userID'])){
                    echo('                 <form action="nonParticipate.php" method="post">
                    <input type="hidden" name="eventid" value="'.$id.'">
                    <input type="hidden" name="csrf" value="'.$token.'">
                    <input class="nopt_btn" type="submit" value="参加しない">
                </form>');
                }
                if(!isset($_SESSION['userID'])){
                    echo('<p><span><a href="login.php">ログイン</a></span></p>');
                }
                ?>
                
            </div>

            

            <!-- <form action="" method="post">
                <input type="hidden" name="eventid" value="<?=$id?>">
                <input type="hidden" name="csrf" value="<?=$token?>">
            </form> -->
        </div>

        
        <div id="lists">
            <div class="pt">
                <p>参加者</p>
                <!-- foreachで出力 -->
                <ul>
                    <?php
                    foreach ($ptUsers as $ptUser) {
                        $UserData = getUserData($ptUser['userid']);?>
                        <li><a href="user.php?pageID=<?=$UserData['id']?>"><?=$UserData['username']?></a></li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
            <div class="nopt">
                <p>不参加</p>
                <ul>
                    <?php
                    foreach ($nonPtUsers as $nonPtUser) {
                        $UserData = getUserData($nonPtUser['userid']);?>

                        <li><a href="user.php?pageID=<?=$UserData['id']?>"><?=$UserData['username']?></a></li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
        </div>

        <!-- <div class=""></div> -->
        <!-- コメント投稿 -->
        <!-- 
            TODO    
            ログインしていないと投稿できない
        -->
        <div class="postComment">
        <?php
                if(isset($_SESSION['userID'])){
                    echo('            <form id="comment-form"action="postComment.php" method="POST">
                    <p class="holder">コメント</p>
                    <input id="postComment" type="text" name="comment"><br>
                    <input type="hidden" name="eventid" value="'.$id.'">
                    <input type="hidden" name="csrf" value="'.$token.'">
                </form>
                <input type="submit" id="commentBtn"value="投稿">');
                }else{
                    echo'<p>コメントするには<span><a href="login.php">ログイン</a></span>が必要です</p>';
                }
            ?>

        </div>
        
        <?php
            $comments = getComment($id, $commentPage);
            //var_dump($comments);
        ?>

        <!-- コメント表示 -->
        <div class="comments">
            <!-- 表示させるのは id コメント内容 時間 -->
            <?php foreach($comments as $comment):
                $UserData = getUserData($comment['userid']);
                
            ?>
                <a href="user.php?pageID=<?=$UserData['id']?>"><?=$UserData['username']?></a>
                <div class="date"><?=h($comment['postdate'])?></div>
                <div class="comment"><?=h($comment['comment'])?></div>
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
        <footer>
        <p id="copy">
            &copy;omrn
        </p>
    </footer>
    <?php
                if(isset($_SESSION['userID']) == NULL){
                    echo('<script src="js/checkComment.js"></script>');
                }
            ?>
        <script>

        function generateMap(){
            //最初のMap生生成
            let firstLng = <?=$result["lng"]?>;
			let firstLat = <?=$result["lat"]?>;
            let FirstLatLng = new google.maps.LatLng(firstLat, firstLng);
            let Options = {
                zoom: 15,
                center: FirstLatLng,
                mapTypeId: 'roadmap'
            };
            map = new google.maps.Map(mapElement, Options);
            marker = new google.maps.Marker({
                position: FirstLatLng,
                map: map,
                draggable:false
            })
            geneMarker.setMap(geneMap);
        }
        // let inputAddress,
        //     marker,
        //     map,
        //     latlng;
        // let geocoder = new google.maps.Geocoder();
        const mapElement = document.getElementById('map');

        generateMap();

        
        </script>
    </body>
</html>