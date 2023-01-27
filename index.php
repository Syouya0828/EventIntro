<?php 
session_start();

require_once("headerLogin.php");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
  <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="main_btn.css">
    <link rel="stylesheet" href="main_table.css">
    <link rel="stylesheet" href="css/header.css">
  <title>タイトル</title>
<?php
?>
</head>
<body>

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
<div id="contents">
  <h2>omrnはイベントの<span>告知</span>を行うサイトです！</h2>
  <h2 class="second">今すぐ始めよう！！</h2>
  <a class="signup"href="SignUp.php">新規登録</a>
</div>
<h2 class="recently">最近の投稿</h2>
<table>
  <tr>
    <th class='title'>イベント</th>
    <th class='detail'>内容</th>
    <th class='place'>開催地</th>
    <th class='date'>日にち</th>
    <th class='cap'>募集人数</th>

  </tr>
  
<?php
$dsn ='mysql:host=localhost;dbname=event_intro';
$username ='root';
try{
  //期限切れは表示させない
  $dbh= new PDO($dsn,$username);
  $sql='select * from events order by postdate DESC LIMIT 10';
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
<footer>
        <p id="copy">
            &copy;omrn
        </p>
    </footer>
</body>
</html>