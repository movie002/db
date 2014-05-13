<?php
header('Content-Type:text/html;charset= UTF-8'); 
//require_once('360safe/360webscan.php');
require_once("../../php/common/common_gen.php");
require_once("../../php/common/base.php");
require_once("../../php/config.php");
require_once("../../php/common/dbaction.php");
require_once("../../php/common/curl.php");

$conn=mysql_connect ($dbip, $dbuser, $dbpasswd) or die('数据库服务器连接失败：'.mysql_error());
mysql_select_db($dbname, $conn) or die('选择数据库失败');
mysql_query("set names utf8;");
	
srcsub();	
	
function srcsub()
{	
	//print_r($_POST);
	
	if (empty($_POST['title'])||empty($_POST['link'])||empty($_POST['author'])||empty($_POST['passwd']))
	{
		echo '参数未设置!';
		return;
	}

	if(checkpasswd($_POST['passwd'])===-1)
	{
		echo "授权码不对!";
		return;
	}
	
	$updatetime = $_POST['updatetime'];
	if(empty($updatetime))
		$updatetime = date("Y-m-d H:i:s");
		
	addorupdatelink($_POST['id'],$_POST['author'],$_POST['title'],$_POST['link'],'',$_POST['linkquality'],$_POST['linkway'],$_POST['linktype'],$_POST['linkdownway'],$updatetime,$_POST['passwd']);
		
	//调用genpage重新生成页面
	get_file_curl("http://127.0.0.1/php/genv/gen_page.php?id=".$_POST['id']);
	echo '增加成功!刷新查看';	
}
function checkpasswd($passwd)
{
	//定义能删除的权限列表
	$auth = array('111111','dhblog','123456');
	//print_r($auth);
	
	$res1=array_search($passwd,$auth);
	//echo 'res1: '.$res1."\n";
	if ($res1===false)
	{
		//echo "bad passwd \n";
		return -1;
	}
	else
	{
		//echo "good passwd \n";
		return 1;
	}	
	return -1;
}
?>