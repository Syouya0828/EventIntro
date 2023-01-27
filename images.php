<?php
require_once('dbc.php');
function getUserData($id){//ユーザーのデータを取ってくる
    try {
        $dbh = dbConnect();
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $sql = 'SELECT id,username,image_type,image FROM users WHERE users.id=:id';
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
$userID = $_GET['userID'];
//$userID = 5;
$userData = getUserData($userID);

header("Content-Type:".$userData['image_type']);
echo($userData['image']);
?>