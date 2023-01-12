<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>仮題</title>
<link href="myImage.css" rel="stylesheet">
<script>
function confirm_test() {
    var conf = confirm("更新します。よろしいですか？");
    return conf;
}
function imgPreView(event) {
	  var file = event.target.files[0];
	  var reader = new FileReader();
	  var preview = document.getElementById("preview");
	  var previewImage = document.getElementById("previewImage");

	  if(previewImage != null) {
	    preview.removeChild(previewImage);
	  }
	  reader.onload = function(event) {
	    var img = document.createElement("img");
	    img.setAttribute("src", reader.result);
	    img.setAttribute("id", "previewImage");
	    img.setAttribute("width", "200px");
	    img.setAttribute("height", "200px");
	    preview.appendChild(img);
	  };

	  reader.readAsDataURL(file);
	}

</script>
</head>
<body>
	<?php
	session_start();
	//test用
	$_SESSION['userID'] = 1;

	function getUserList($id){
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
	    $sql = "SELECT * FROM users where id = '".$id."' ORDER BY id ASC";
	    $prepare = $dbh->prepare($sql);
	    $prepare->execute();
	    $result = $prepare->fetchall(PDO::FETCH_ASSOC);
	    $_SESSION['userlist'] = $result;
	    $dbh = NULL;
	}
	getUserList($_SESSION['userID']);
	?>
	<form action='myImageUpdate.php' method='POST' enctype="multipart/form-data">
	<h1>編集画面</h1>
	<?php
	printf("<input type='hidden' value=%d name='id'>",$_SESSION['userlist'][0]['id']);
	printf("<p>アイコン画像(200×200pxに変更されます)</p>");
	print("<img src=\"importmedia.php\" width=200 height=200 alt='icon'><br>");
	?>
	<input type='file' onChange="imgPreView(event)" name='image' accept='.jpg,.jpeg,.png,.gif'>
	<div id="preview"></div>
	<?php
	printf("<p><span>ユーザーネーム</span></p><input type='text' name='username' value=%s required>",$_SESSION['userlist'][0]['username']);
	?>
	<br>
	<input class="decoBtnUpd click-down" type='submit' name='update' value='更新実行' onClick="return confirm_test()">
	</form>
</body>
</html>