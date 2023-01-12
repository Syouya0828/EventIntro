<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>仮題</title>
<link href="update.css" rel="stylesheet">
<script>
function confirm_test(val) {
	if(val == "update"){
    	var conf = confirm("更新します。よろしいですか？");
    	return conf;
	}else if(val == "delete"){
		var conf = confirm("本当に削除しますか？この操作はやり直すことができません。");
		return conf;
	}
}
</script>
</head>
<body>
	<?php session_start(); ?>
	<form action='updateEvent.php' method='POST'>
	<h1>編集画面</h1>
	<?php
	printf("<input type='hidden' value=%d name='id'>",$_SESSION['list'][$_POST["i"]]['id']);
	printf("<p><span>タイトル</span></p><input type='text' value=%s name='eventname' required>",$_SESSION['list'][$_POST['i']]['eventname']);
	printf("<p><span>イベント内容</span></p><textarea name='eventdetail' cols='50' rows='4' required>%s</textarea>",$_SESSION['list'][$_POST['i']]['eventdetail']);
	printf("<p><span>場所</span></p><input type='text' value=%s name='place' required>",$_SESSION['list'][$_POST['i']]['place']);
	printf("<p><span>日時</span></p><input type='date' value=%s name='eventdate' required>",$_SESSION['list'][$_POST['i']]['eventdate']);
	printf("<p><span>参加人数</span></p><input type='text' value=%s name='capacity' required>",$_SESSION['list'][$_POST['i']]['capacity']);
	printf("<p for='keyword'><span>キーワード</span></p><input type='text' value=%s name='keywords' required><br/>",$_SESSION['list'][$_POST['i']]['keywords']);
	?>
	<input class="decoBtnUpd click-down" type='submit' name='update' value='更新実行' onClick="return confirm_test('update')">
	<input class="decoBtnDel click-down" type='submit' name='delete' value='削除する' onClick="return confirm_test('delete')">
	</form>
</body>
</html>