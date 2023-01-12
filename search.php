<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>仮題</title>
<link href="search.css" rel="stylesheet">
</head>
<body>
	<script type="text/javascript">//今日の日付を予めセット
  	window.onload = function () {
    	var today = new Date();
    	today.setDate(today.getDate());
    	var yyyy = today.getFullYear();
    	var mm = ("0" + (today.getMonth() + 1)).slice(-2);
    	var dd = ("0" + today.getDate()).slice(-2);
    	document.getElementById("today").value = yyyy + '-' + mm + '-' + dd;
  	}
	</script>
	<h2>検索画面</h2>
	<div id="main">
	<form class="search" method='POST' action='searchEvent.php'>
	<p>選択:<select name="search">
	<?php
	if(!empty($_GET['sHistory'])){//検索履歴の表示
	    if($_GET['sHistory'] == "title"){
	        print("<option value='title' selected>タイトル検索</option>");
	    }else{
	        print("<option value='title'>タイトル検索</option>");
	    }
	    if($_GET['sHistory'] == "date"){
	        print("<option value='date' selected>日付検索</option>");
	    }else{
	        print("<option value='date'>日付検索</option>");
	    }
	    if($_GET['sHistory'] == "titleDate"){
	        print("<option value='titleDate' selected>タイトル・日付検索</option>");
	    }else{
	        print("<option value='titleDate'>タイトル・日付検索</option>");
	    }
	}else{
		print("<option value='title'>タイトル検索</option>");
		print("<option value='date'>日付検索</option>");
		print("<option value='titleDate'>タイトル・日付検索</option>");
	}
	?>
	</select></p>
	<p>日付:<input type="date" value="" name="searchDate" id="today"/>(日付から60日以内を検索します。)</p>
	<?php
	if(!empty($_GET['sWord'])){//ワードの表示
	   printf("<p>タイトル:<input type='text' name='word' value=%s>",htmlspecialchars($_GET['sWord']));
	}else{
	   print("<p>タイトル:<input type='text' name='word'/>");
	}
	?>
	<input type='submit' value='検索'/></p>
	</form>
	<?php
	if(!empty($_GET["err"])){
	    print("<p class='err'>該当するイベントは見つかりませんでした。</p>");
	};
	session_start();
	function getUserList(){
	    function connectDB() {
	        $param = 'mysql:dbname=event_intro;host=localhost';
	        try {
	            $pdo = new PDO($param, 'eventuser', 'omrn2022');
	            return $pdo;

	        } catch (PDOException $e) {
	            exit($e->getMessage());
	        }
	    }
	    $dbh = connectDB();
	    $dbh->exec("USE event_intro");
	    $sql = 'SELECT * FROM users ORDER BY id ASC';
	    $prepare = $dbh->prepare($sql);
	    $prepare->execute();
	    $result = $prepare->fetchall(PDO::FETCH_ASSOC);
	    $_SESSION['userlist'] = $result;
	    $dbh = NULL;
	}
	getUserList();
	if(!empty($_SESSION['searchResult'])){
	   print('<table>');
	   $nrows = count($_SESSION['searchResult']);
	   $mrows = count($_SESSION['userlist']);
	   for($i = 0; $i < $nrows; $i++){
	       $username = "";
	       for($j = 0; $j < $mrows; $j++){
	           if($_SESSION['searchResult'][$i]['userid'] === $_SESSION["userlist"][$j]["id"]){
	               $username = $_SESSION["userlist"][$j]["username"];
	           }
	       }
	       print('<tr><td>');
	       printf("<p>%s</p>",$_SESSION['searchResult'][$i]['eventname']);
	       printf("<p>%s</p>",$_SESSION['searchResult'][$i]['eventdate']);
	       printf("<p>%s</p>",$username);
	       print('</td></tr>');
	   }
	   print('</table>');
	}
	?>
	</div>
</body>
</html>