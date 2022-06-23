<!-- マイページとは名ばかりのただの一覧表示 -->
<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>仮題</title>
</head>
<body>
	<h2>マイページ</h2>
	<?php
	session_start();
	require_once("getList.php");
	getList();
	print('<table border="1" style="border-collapse: collapse">');
	printf("<tr><th>eventname</th><th>機能</th></tr>");
	$nrows = count($_SESSION['list']);
	for($i = 0; $i < $nrows; $i++){
	    print('<tr>');
	    printf("<td><p>%s</p></td>",$_SESSION['list'][$i]['eventname']);
	    print("<td><form method='POST' action='update.php'>");
	    printf("<input type='hidden' name='i' value=%d>",$i);
	    print("<input type='submit' value='変更または削除'/></form></td>");
// 	    print("<td><form method='POST' action='deleteEvent.php' onsubmit='return confirm_test()'>");
// 	    printf("<input type='hidden' name='id' value=%d/>",$_SESSION['list'][$i]['id']);
// 	    print("<input type='submit' value='削除'/></form></td>");
	    print('</tr>');
	}
	print('</table>');
	?>
</body>
</html>