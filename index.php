<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
  <link rel="stylesheet" href="mian.css">
    <link rel="stylesheet" href="main_btn.css">
    <link rel="stylesheet" href="main_table.css">
  <title>タイトル</title>
<?php
?>
</head>
<body>

<header>
    <h1>
        <a href="/">タイトル</a>
    </h1>
    <nav class="pc-nav">
        <ul>
            <li><a href="index.php">ホーム</a></li>
            <li><a href="searchEvent.php">検索</a></li>
            <li><a href="event_createForm.html">募集</a></li>
            <li><a href="mypage.php">マイページ</a></li>
        </ul>
    </nav>
</header>
<div class="pad"></div>
<h2>サイトの紹介</h2>
<p>ああああああああああ</p>    
<div class="pad2"></div>
<a href="Login.php" class="btn btn--orange btn-c">
  <i class="fa far fa-envelope"></i>ログイン
</a>
<div class="pad3"></div>
<a href="SignUp.php" class="btn btn--orange btn-c">
  <i class="fa far fa-envelope"></i>会員登録
</a>
<br><br><br><br>
<table>
  <tr>
    <th class='title'>イベント</th>
    <th class='detail'>内容</th>
    <th class='place'>開催地</th>
    <th class='date'>日にち</th>
    <th class='cap'>募集人数</th>
    <th class='key'>タグ</th>
  </tr>
  
<?php
$dsn ='mysql:host=localhost;dbname=event_intro';
$username ='root';
try{
  //期限切れは表示させない
  $dbh= new PDO($dsn,$username);
  $sql='select * from events';
  $sth =$dbh->prepare($sql);
  $sth->execute();
 $result = $sth->fetchAll();
 
 
foreach ($result as $row) {
        $detail =$row['eventdetail'];
        $detail=insertStr($detail,"<br>",30);
        echo "<tr><td class='title'><a href='view.php?commentPage=1&id=".$row['id']."'>".$row['eventname']."</a></td>";
        echo "<td class='detail'>";
        echo $detail;
        echo "</td>";
        echo "<td class='place'>".$row['place']."</td>";
        echo "<td class='date'>".$row['eventdate']."</td>";
        echo "<td class='cap'>".$row['capacity']."</td>";
        echo "<td class='key'>".$row['keywords']."</td>";
        echo "</tr>";
}
}
catch(PDOException $e){
      echo "<p>Failed : " . $e->getMessage()."</p>";
    exit();
}

function insertStr($text, $insert, $num){
    $returnText = $text;
    $text_len = mb_strlen($text, "utf-8");
    $insert_len = mb_strlen($insert, "utf-8");
    for($i=0; ($i+1)*$num<$text_len; $i++) {
        $current_num = $num+$i*($insert_len+$num);
        $returnText = preg_replace("/^.{0,$current_num}+\K/us", $insert, $returnText);
    }
    return $returnText;
}
?>
</table>
</body>
</html>