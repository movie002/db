<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN" xml:lang="zh-CN">
<head>
	<script type="text/javascript">
	function cnzz()
	{
		(function() {
			var cnzz = document.createElement('script');
			cnzz.type = 'text/javascript';
			cnzz.src = 'http://s22.cnzz.com/z_stat.php?id=1000362328&web_id=1000362328';
			(document.getElementsByTagName('body')[0]
			||document.getElementsByTagName('head')[0]).appendChild(cnzz);
		})();
	}
	window.onload = function ()
	{
		cnzz();	
	}
	</script>
</head>
<body>
<?php
header('Content-Type:text/html;charset= UTF-8'); 
require_once('../360safe/360webscan.php');
require_once("../../php/common/base.php");
require_once("../../php/config.php");
require_once("../../php/genv/config.php");
require_once("../../php/common/dbaction.php");
require_once("../../php/common/curl.php");

$conn=mysql_connect ($dbip, $dbuser, $dbpasswd) or die('数据库服务器连接失败：'.mysql_error());
mysql_select_db($dbname, $conn) or die('选择数据库失败');
mysql_query("set names utf8;");

//定义能删除的权限列表
$auth = array('bigboss'=>array('bigboss'),'10282'=>array('myipr'));

srcdel();

function srcdel()
{
	//print_r($_POST);
	$delurl=$_POST['delurl'];
	$pageid = $_POST['pageid'];
	$passwd = $_POST['passwd'];
	
	if(checkpasswd($passwd,$pageid,$delurl)===-1)
	{
		echo "删除码不对!";
		return;
	}
	$sql="update link set remove = '$passwd' where link='$delurl' and pageid=$pageid;";
	//echo $sql;
	if(dh_mysql_query($sql)==null)
	{
		echo '删除失败！';
		return;
	}
	echo '删除成功!刷新查看';
	//调用genpage重新生成页面
	get_file_curl("http://127.0.0.1/php/genv/gen_page.php?id=$pageid");
	//记录有删除行为发生
	
}

function checkpasswd($passwd,$pageid,$delurl)
{
	global $auth;
	//print_r($auth);
	
	if($passwd=='' || $passwd=='0')
		return -1;
	
	$res=array_search($passwd,$auth['bigboss']);
	if (!($res===false))
		return 1;
	
	if(array_key_exists($pageid,$auth))
	{
		$res=array_search($passwd,$auth[$pageid]);	
		if (!($res===false))
			return 1;
	}

	$sql="select count(*) from link where input = '$passwd' and link='$delurl' and pageid=$pageid;";
	//echo $sql;
	$row = dh_mysql_query($sql);
	$count = mysql_fetch_array($row);
	if($count[0]>=1)
		return 1;
	return -1;
}
?>
</body>
