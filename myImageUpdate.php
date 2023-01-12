<?php
function connectDB() {
    $param = 'mysql:dbname=event_intro;host=localhost';
    try {
        $pdo = new PDO($param, 'eventuser', 'omrn2022');
        return $pdo;

    } catch (PDOException $e) {
        exit($e->getMessage());
    }
}
$id = $_POST['id'];
$username = htmlspecialchars($_POST['username']);
$image = file_get_contents($_FILES['image']['tmp_name']);
$image_type = $_FILES['image']['type'];
$dbh = connectDB();
if(!empty($_FILES['image']['name'])){
    $dbh->exec("USE event_intro");
    $sql = "UPDATE users SET username=:username, image=:image, image_type=:image_type WHERE id = '" .$id."'";
    $prepare = $dbh->prepare($sql);
    $prepare -> bindValue(':username', $username, PDO::PARAM_STR);
    $prepare -> bindValue(':image', $image, PDO::PARAM_STR);
    $prepare -> bindValue(':image_type', $image_type, PDO::PARAM_STR);
    $prepare->execute();
    $dbh = NULL;
}else{
    $dbh->exec("USE event_intro");
    $sql = "UPDATE users SET username=:username WHERE id = '" .$id."'";
    $prepare = $dbh->prepare($sql);
    $prepare -> bindValue(':username', $username, PDO::PARAM_STR);
    $prepare->execute();
    $dbh = NULL;
}
header('Location: myImage.php');
exit();

?>