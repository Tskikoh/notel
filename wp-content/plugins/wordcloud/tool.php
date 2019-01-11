<!doctype html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>ワードクラウドを作成</title>
<?php
$token = "";
$st_token = "";
$key = '';

$user = openssl_decrypt(urldecode($_GET["user"]), 'AES-128-ECB', $key);

$https = $_SERVER['HTTPS'];
if( isset($https) && $https != "off"){
	$ssl = true;
	$protocol = "https://";
} else {
	$ssl = false;
	$protocol = "http://";
}
$server_name = $_SERVER['SERVER_NAME'];
$request_uri = $_SERVER['REQUEST_URI'];
$file_url = $protocol.$server_name.$request_uri;

$url = "https://101023.xyz/sql-test4.php?user_name=";
$url .= $user;

require_once("phpQuery-onefile.php");
$link_url .= "https://101023.xyz/short.php?url=";
$link_url .= urlencode($url);
$link_url .= "&token=";
$link_url .= $st_token;

$shorten = file_get_contents($link_url);
$shorten_url = phpQuery::newDocument($shorten)->text();

if( $user == "asd1458masa" ){
echo "デバッグモード";
echo "<table border=\"1\"><tr><td>";
echo "user</td><td>$user";
echo "</td></tr><tr><td>";
echo "token</td><td>$token";
echo "</td></tr><tr><td>";
echo "st_token</td><td>$st_token";
echo "</td></tr><tr><td>";
echo "key</td><td>$key";
echo "</td></tr><tr><td>";
echo "url</td><td><a href=\"$url\" target=\"_blank\">$url</a>";
echo "</td></tr><tr><td>";
echo "url+token</td><td><a href=\"$url&token=$token\" target=\"_blank\">$url&token=$token</a>";
echo "</td></tr><tr><td>";
echo "url+token=334</td><td><a href=\"$url&token=334\" target=\"_blank\">$url&token=334</a>";
echo "</td></tr><tr><td>";
echo "link_url</td><td><a href=\"$link_url\" target=\"_blank\">$link_url</a>";
echo "</td></tr><tr><td>";
echo "shorten_url</td><td><a href=\"$shorten_url\" target=\"_blank\">$shorten_url</a>";
echo "</td></tr><tr><td>";
echo "file_url</td><td><a href=\"$file_url\" target=\"_blank\">$file_url</a>";
echo "</td></tr><tr><td>";
echo "https / ssl</td><td>$https / $ssl";
echo "</td></tr></table>";
}

?>
	</head>
	<body>
		<!--<h1 style="color: red;">
			ただいまプラグインの調整中のため、つかえましぇーん
		</h1>-->
<form action="http://lab.fanbright.jp/lod/web" id="webForm" method="post" accept-charset="utf-8"><div style="display:none;"><input type="hidden" name="_method" value="POST" kl_vkbd_parsed="true"></div><table class="table table-striped table-responsive">
<thead><tr><th width="150">項目</th><th>内容</th><th>備考</th></tr></thead><tbody>
<!--<tr><td>チェックURL</td><td>
<label for="KeywordCheckUrl"></label>--><input name="data[Keyword][check_url]" style="width:300px" maxlength="50" type="hidden" value="<?= $shorten_url; ?>" id="KeywordCheckUrl" kl_vkbd_parsed="true"><!--</td><td>http/httpsで始まるURLを指定してください。50Bytesまで指定可能です。</td></tr>-->
<tr><td>UserAgent</td><td>
<select name="data[Keyword][check_useragent]" id="KeywordCheckUseragent">
<option value="">項目を選択してください</option>
<option value="iPhone_iOS6">iPhone iOS 6</option>
<option value="iPad_iOS6">iPad iOS 6</option>
<option value="Google_Chrome_37" selected="selected">Google Chrome 37</option>
<option value="Android_OS_4x">Android OS 4.x</option>
<option value="IE_11">Internet Explorer 11</option>
</select></td><td>ユーザーエージェントを指定してください。</td></tr>
<tr><td>キーワード出現頻度</td><td>
<select name="data[Keyword][check_appearances]" id="KeywordCheckAppearances">
<option value="">項目を選択</option>
<option value="1">1回以上</option>
<option value="2" selected="selected">2回以上</option>
<option value="3">3回以上</option>
<option value="4">4回以上</option>
<option value="5">5回以上</option>
<option value="6">6回以上</option>
<option value="7">7回以上</option>
<option value="8">8回以上</option>
<option value="9">9回以上</option>
<option value="10">10回以上</option>
</select></td><td>キーワード出現頻度を指定してください。</td></tr>
<tr><td>キーワード出現順位</td><td>
<select name="data[Keyword][check_displayno]" id="KeywordCheckDisplayno">
<option value="">項目を選択</option>
<option value="10">上位 No.10まで</option>
<option value="20">上位 No.20まで</option>
<option value="30">上位 No.30まで</option>
<option value="40">上位 No.40まで</option>
<option value="50">上位 No.50まで</option>
<option value="60">上位 No.60まで</option>
<option value="70">上位 No.70まで</option>
<option value="80">上位 No.80まで</option>
<option value="90">上位 No.90まで</option>
<option value="100" selected="selected">上位 No.100まで</option>
<option value="150">上位 No.150まで</option>
<option value="200">上位 No.200まで</option>
</select></td><td>キーワード出現順位を指定してください。</td></tr>
<tr><td>ワードクラウド種別</td><td>
<input type="radio" name="data[Keyword][check_pattern]" id="KeywordCheckPattern1" value="1" checked="checked" kl_vkbd_parsed="true"><label for="KeywordCheckPattern1">フラット</label><input type="radio" name="data[Keyword][check_pattern]" id="KeywordCheckPattern2" value="2" kl_vkbd_parsed="true"><label for="KeywordCheckPattern2">フラット＋直角</label><input type="radio" name="data[Keyword][check_pattern]" id="KeywordCheckPattern3" value="3" kl_vkbd_parsed="true"><label for="KeywordCheckPattern3">傾き</label></td><td>ワードクラウドの表示種別を指定してください。</td></tr>
<tr><td>ワードクラウドサイズ</td><td>
横幅：<select name="data[Keyword][check_imgw]" id="KeywordCheckImgw">
<option value="">項目を選択</option>
<option value="200">200</option>
<option value="300">300</option>
<option value="400">400</option>
<option value="500">500</option>
<option value="600">600</option>
<option value="700">700</option>
<option value="800">800</option>
<option value="900">900</option>
<option value="999" selected="selected">画面幅</option>
</select>　縦幅：<select name="data[Keyword][check_imgh]" id="KeywordCheckImgh">
<option value="">項目を選択</option>
<option value="200">200</option>
<option value="300">300</option>
<option value="400" selected="selected">400</option>
<option value="500">500</option>
<option value="600">600</option>
<option value="700">700</option>
<option value="800">800</option>
<option value="900">900</option>
</select></td><td>ワードクラウドのSVGサイズ（横幅・縦幅）を指定してください。</td></tr>
<tr><td>文字サイズ</td><td>
最少：<select name="data[Keyword][check_minrange]" id="KeywordCheckMinrange">
<option value="">項目を選択</option>
<option value="10">10</option>
<option value="12">12</option>
<option value="14" selected="selected">14</option>
<option value="16">16</option>
<option value="18">18</option>
<option value="20">20</option>
</select>最大：<select name="data[Keyword][check_maxrange]" id="KeywordCheckMaxrange">
<option value="">項目を選択</option>
<option value="50">50</option>
<option value="60">60</option>
<option value="70">70</option>
<option value="80" selected="selected">80</option>
<option value="90">90</option>
<option value="100">100</option>
</select></td><td>ワードクラウド内の文字サイズ（最少・最大）を指定できます。</td></tr>
</tbody></table>

<script type="text/javascript">
$(function(){
  $("#exslideBox").hide();
  $(".exopentable").click(function(){
    $("#exslideBox").slideToggle("slow");
  });
});
</script>
<p class="exopentable"><i class="fa-fw fa fa-wrench" style="color:black;"></i>除外ワードを指定したい場合、本テキスト部をクリックしてください。</p>
<div id="exslideBox" style="display: none;">
<table class="table table-striped table-responsive">
<thead><tr><th width="150">項目</th><th>除外ワード</th><th>備考</th></tr></thead><tbody>
<tr><td>除外ワード1</td><td>
<label for="KeywordExclusion1"></label><input name="data[Keyword][exclusion1]" style="width:200px" maxlength="120" type="text" value="" id="KeywordExclusion1" kl_vkbd_parsed="true"></td><td>除外ワードがあれば指定してください。</td></tr>
<tr><td>除外ワード2</td><td>
<label for="KeywordExclusion2"></label><input name="data[Keyword][exclusion2]" style="width:200px" maxlength="120" type="text" value="" id="KeywordExclusion2" kl_vkbd_parsed="true"></td><td>除外ワードがあれば指定してください。</td></tr>
<tr><td>除外ワード3</td><td>
<label for="KeywordExclusion3"></label><input name="data[Keyword][exclusion3]" style="width:200px" maxlength="120" type="text" value="" id="KeywordExclusion3" kl_vkbd_parsed="true"></td><td>除外ワードがあれば指定してください。</td></tr>
</tbody></table>
</div>

<div class="submit"><input type="submit" value="Check" kl_vkbd_parsed="true"></div></form>
		
<?php
		/*
		 * 
		 * 
		 * 
		 * 
		 * 
		?>
		
<form action="https://lab.fanbright.jp/lod/web" id="webForm" method="post" accept-charset="utf-8"><div style="display:none;"><input type="hidden" name="_method" value="POST" kl_vkbd_parsed="true"></div><table class="table table-striped table-responsive">
<thead><tr><th width="150">項目</th><th>内容</th><th>備考</th></tr></thead><tbody>
<!--<tr><td>チェックURL</td><td>
<label for="KeywordCheckUrl"></label>--><input name="check_url" style="width:300px" maxlength="50" type="hidden" value="<?= $shorten_url; ?>" id="KeywordCheckUrl" kl_vkbd_parsed="true"><!--</td><td>http/httpsで始まるURLを指定してください。50Bytesまで指定可能です。</td></tr>-->
<tr><td>UserAgent</td><td>
<select name="check_useragent" id="KeywordCheckUseragent">
<option value="">項目を選択してください</option>
<option value="iPhone_iOS6">iPhone iOS 6</option>
<option value="iPad_iOS6">iPad iOS 6</option>
<option value="Google_Chrome_37" selected="selected">Google Chrome 37</option>
<option value="Android_OS_4x">Android OS 4.x</option>
<option value="IE_11">Internet Explorer 11</option>
</select></td><td>ユーザーエージェントを指定してください。</td></tr>
<tr><td>キーワード出現頻度</td><td>
<select name="check_appearances" id="KeywordCheckAppearances">
<option value="">項目を選択</option>
<option value="1">1回以上</option>
<option value="2" selected="selected">2回以上</option>
<option value="3">3回以上</option>
<option value="4">4回以上</option>
<option value="5">5回以上</option>
<option value="6">6回以上</option>
<option value="7">7回以上</option>
<option value="8">8回以上</option>
<option value="9">9回以上</option>
<option value="10">10回以上</option>
</select></td><td>キーワード出現頻度を指定してください。</td></tr>
<tr><td>キーワード出現順位</td><td>
<select name="check_displayno" id="KeywordCheckDisplayno">
<option value="">項目を選択</option>
<option value="10">上位 No.10まで</option>
<option value="20">上位 No.20まで</option>
<option value="30">上位 No.30まで</option>
<option value="40">上位 No.40まで</option>
<option value="50">上位 No.50まで</option>
<option value="60">上位 No.60まで</option>
<option value="70">上位 No.70まで</option>
<option value="80">上位 No.80まで</option>
<option value="90">上位 No.90まで</option>
<option value="100" selected="selected">上位 No.100まで</option>
<option value="150">上位 No.150まで</option>
<option value="200">上位 No.200まで</option>
</select></td><td>キーワード出現順位を指定してください。</td></tr>
<tr><td>ワードクラウド種別</td><td>
<input type="radio" name="check_pattern" id="KeywordCheckPattern1" value="1" checked="checked" kl_vkbd_parsed="true"><label for="KeywordCheckPattern1">フラット</label><input type="radio" name="check_pattern" id="KeywordCheckPattern2" value="2" kl_vkbd_parsed="true"><label for="KeywordCheckPattern2">フラット＋直角</label><input type="radio" name="check_pattern" id="KeywordCheckPattern3" value="3" kl_vkbd_parsed="true"><label for="KeywordCheckPattern3">傾き</label></td><td>ワードクラウドの表示種別を指定してください。</td></tr>
<tr><td>ワードクラウドサイズ</td><td>
横幅：<select name="check_imgw" id="KeywordCheckImgw">
<option value="">項目を選択</option>
<option value="200">200</option>
<option value="300">300</option>
<option value="400">400</option>
<option value="500">500</option>
<option value="600">600</option>
<option value="700">700</option>
<option value="800">800</option>
<option value="900">900</option>
<option value="999" selected="selected">画面幅</option>
</select>　縦幅：<select name="check_imgh" id="KeywordCheckImgh">
<option value="">項目を選択</option>
<option value="200">200</option>
<option value="300">300</option>
<option value="400" selected="selected">400</option>
<option value="500">500</option>
<option value="600">600</option>
<option value="700">700</option>
<option value="800">800</option>
<option value="900">900</option>
</select></td><td>ワードクラウドのSVGサイズ（横幅・縦幅）を指定してください。</td></tr>
<tr><td>文字サイズ</td><td>
最少：<select name="check_minrange" id="KeywordCheckMinrange">
<option value="">項目を選択</option>
<option value="10">10</option>
<option value="12">12</option>
<option value="14" selected="selected">14</option>
<option value="16">16</option>
<option value="18">18</option>
<option value="20">20</option>
</select>最大：<select name="check_maxrange" id="KeywordCheckMaxrange">
<option value="">項目を選択</option>
<option value="50">50</option>
<option value="60">60</option>
<option value="70">70</option>
<option value="80" selected="selected">80</option>
<option value="90">90</option>
<option value="100">100</option>
</select></td><td>ワードクラウド内の文字サイズ（最少・最大）を指定できます。</td></tr>
</tbody></table>

<script type="text/javascript">
$(function(){
  $("#exslideBox").hide();
  $(".exopentable").click(function(){
    $("#exslideBox").slideToggle("slow");
  });
});
</script>
<p class="exopentable"><i class="fa-fw fa fa-wrench" style="color:black;"></i>除外ワードを指定したい場合、本テキスト部をクリックしてください。</p>
<div id="exslideBox" style="display: none;">
<table class="table table-striped table-responsive">
<thead><tr><th width="150">項目</th><th>除外ワード</th><th>備考</th></tr></thead><tbody>
<tr><td>除外ワード1</td><td>
<label for="KeywordExclusion1"></label><input name="exclusion1" style="width:200px" maxlength="120" type="text" id="KeywordExclusion1" kl_vkbd_parsed="true"></td><td>除外ワードがあれば指定してください。</td></tr>
<tr><td>除外ワード2</td><td>
<label for="KeywordExclusion2"></label><input name="exclusion2" style="width:200px" maxlength="120" type="text" id="KeywordExclusion2" kl_vkbd_parsed="true"></td><td>除外ワードがあれば指定してください。</td></tr>
<tr><td>除外ワード3</td><td>
<label for="KeywordExclusion3"></label><input name="exclusion3" style="width:200px" maxlength="120" type="text" id="KeywordExclusion3" kl_vkbd_parsed="true"></td><td>除外ワードがあれば指定してください。</td></tr>
</tbody></table>
</div>

<div class="submit"><input type="submit" value="Check" kl_vkbd_parsed="true"></div></form>
<?php		*/ ?>
		
	</body>
</html>