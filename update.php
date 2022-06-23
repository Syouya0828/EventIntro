<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>仮題</title>
<script>
//check
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
	<h2>編集画面</h2>
	<?php session_start(); ?>
	<form action='updateEvent.php' method='POST'>
	<?php
    //html部分は要変更
	printf("<input type='hidden' value=%d name='id'>",$_SESSION['list'][$_POST["i"]]['id']);
	print("<table border='1' style='border-collapse: collapse'>");
	printf("<tr><th>eventname</th><td><input type='text' value=%s name='eventname' required></td></tr>",$_SESSION['list'][$_POST['i']]['eventname']);
	printf("<tr><th>eventdetail</th><td><input type='text' value=%s name='eventdetail' required></td></tr>",$_SESSION['list'][$_POST['i']]['eventdetail']);
	printf("<tr><th>place</th><td><input type='text' value=%s name='place' required></td></tr>",$_SESSION['list'][$_POST['i']]['place']);
	printf("<tr><th>eventdate</th><td><input type='date' value=%s name='eventdate' required></td></tr>",$_SESSION['list'][$_POST['i']]['eventdate']);
	printf("<tr><th>capacity</th><td><input type='text' value=%s name='capacity' required></td></tr>",$_SESSION['list'][$_POST['i']]['capacity']);
	printf("<tr><th>keywords</th><td><input type='text' value=%s name='keywords' required></td></tr>",$_SESSION['list'][$_POST['i']]['keywords']);
	print("</table>");
	?>
	<input type='submit' name='update' value='更新実行' onClick="return confirm_test('update')">
	<input type='submit' name='delete' value='削除する' onClick="return confirm_test('delete')">
	</form>
</body>
</html>