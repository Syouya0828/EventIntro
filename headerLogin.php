<?php
if(isset($_SESSION["userID"])){
    $log = '<li><a href="event_createForm.php">募集</a></li><li><a href="mypage.php">マイページ</a></li><li><a href="logout.php">ログアウト</a></li>';
}else{
    $log = '<li><a href="login.php">ログイン</a></li>';
}
?>