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
	
//定义能删除的权限列表
$auth = array('','','');
srcsub();	
	
function srcsub()
{	
	if (!isset($_POST['email']))
	{
		echo '参数未设置!';
		return;
	}

	print_r($_POST); 
	if(checkpasswd($passwd,$pageid)===-1)
	{
		echo "删除码不对!";
		return;
	}

	if(empty($_POST['email']))
	{
		echo '未设置邮件';
		return;
	}

	$email=$_POST['email'];
	$id = $_POST['id'];
	
	
	$updatetime = date("Y-m-d H:i:s");
	$sql="insert into link(pageid,email,updatetime,type) values ($id,'$email','$updatetime',1) ON DUPLICATE KEY UPDATE updatetime='$updatetime';";
	if(dh_mysql_query($sql)==null)
	{
		echo '订阅失败！';
		return;
	}
	//$sql='insert into pagetmp(id,email,updatetime);'
	//dh_query($sql);
		
		
	echo '增加成功!刷新查看';
	//调用genpage重新生成页面
	get_file_curl("http://127.0.0.1/php/genv/gen_page.php?id=$pageid");
}
function checkpasswd($passwd)
{
	global $auth;
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