<?php
$tiger = 1935; // 大阪タイガースの年
$then = 2018; // サイトの公開年
  $now = date('Y');
  if ($then < $now) {
    $year = $then.'–'.$now;
  } else {
    $year = $then;
  }

if($_GET["token"] == "334"){
	header("HTTP/1.1 334 なんでや！阪神関係ないやろ！");
	echo "<!doctype html>";
	echo "<html>";
	echo "	<head>";
	echo "		<title>334 なんでや！阪神関係ないやろ！</title>";
	echo "		<meta charset=\"utf-8\">";
	echo "	</head>";
	echo "	<body>";
	echo "		<h1>334 なんでや！阪神関係ないやろ！</h1>";
	echo "		<hr />";
	echo "		(c)".$tiger." - ".$year." 阪神タイガース. All rights reserved.";
	echo "	</body>";
	echo "</html>";
	exit;
//39.111.56.80
} elseif($_SERVER["REMOTE_ADDR"] != "126.205.2.240" && $_GET["token"] != "73PX8D0CS9FRBEPLQpq1aVWrVJKaB5d9wKpsaU9N"){
	header("HTTP/1.1 403 Forbidden");
	echo "<!doctype html>";
	echo "<html>";
	echo "	<head>";
	echo "		<title>403 Forbidden</title>";
	echo "		<meta charset=\"utf-8\">";
	echo "	</head>";
	echo "	<body>";
	echo "		<h1>403 Forbidden</h1>";
	echo "		<table>";
	echo "		<tr><td>Error</td><td>Direct access to this page is not allowed.</td></tr>";
	echo "		<tr><td>エラー</td><td>このページへの直接アクセスは許可されていません。</td></tr>";
	echo "		<tr><td>错误</td><td>不允许直接访问此页面。</td></tr>";
	echo "		<tr><td>오류</td><td>이 페이지에 직접 액세스는 허용되지 않습니다.</td></tr>";
	echo "		<tr><td>Ошибка</td><td>прямой доступ к этой странице запрещен.</td></tr>";
	echo "		</table>";
	echo "		<hr />";
	echo "		(c)".$year." [".$_SERVER["SERVER_NAME"]."]. All rights reserved.";
	echo "	</body>";
	echo "</html>";
	exit;
}


$user_name = $_GET["user_name"];

echo <<< EOM
<!doctype html>
<html>
<head>
	<title><$user_name></title>
	<meta charset="utf-8">
</head>
<body>
EOM;
$s=new PDO("mysql:host=mysql133.phy.lolipop.lan;dbname=LAA0613647-oasobi2;charset=utf8;","LAA0613647","Qwerty1234");

$re=$s->query("SELECT wp11_posts.ID, post_title, post_content
FROM wp11_posts
INNER JOIN wp11_users ON wp11_posts.post_author = wp11_users.ID
WHERE wp11_posts.post_author
IN (

SELECT wp11_users.ID
FROM wp11_posts
WHERE user_login = '$user_name'
)
AND post_status != 'trash'
AND post_type != 'customize_changeset'
AND post_title != '自動下書き'
AND post_status != 'draft'
AND post_status != 'inherit'
GROUP BY post_title");

echo "<table border=\"1\">";
while ($kekka=$re->fetch()) {
	echo "<tr>";
	for ($i = 1; $i <= 2; $i++) {
		echo "<td>";
		echo strip_tags($kekka[$i]);
		//echo $kekka[$i];
		echo "</td>";
	}
	echo "</tr>";
}
echo "</table>";
echo "</body>";
echo "</html>";