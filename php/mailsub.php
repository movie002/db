<?php
header('Content-Type:text/html;charset= UTF-8'); 
//require_once('360safe/360webscan.php');
require_once("../../php/common/base.php");
require_once("../../php/config.php");
require_once("../../php/common/dbaction.php");
require_once("../../php/common/curl.php");

$conn=mysql_connect ($dbip, $dbuser, $dbpasswd) or die('数据库服务器连接失败：'.mysql_error());
mysql_select_db($dbname, $conn) or die('选择数据库失败');
mysql_query("set names utf8;");
	
mailsub();	
	
function mailsub()
{
	if (!isset($_POST['email']))
	{
		echo '参数未设置!';
		return;
	}

	print_r($_POST); 

	if(empty($_POST['email']))
	{
		echo '未设置邮件';
		return;
	}
	//print_r($_POST);
	$email=$_POST['email'];
	$id = $_POST['id'];
	$updatetime = date("Y-m-d H:i:s");
	$sql="insert into mailsub(pageid,email,updatetime,type) values ($id,'$email','$updatetime',1) ON DUPLICATE KEY UPDATE updatetime='$updatetime';";
	if(dh_mysql_query($sql)==null)
	{
		echo '订阅失败！';
		return;
	}
	//$sql='insert into pagetmp(id,email,updatetime);'
	//dh_query($sql);
	
	//邮件发送
	
	echo '订阅成功!请查看邮件提示信息';
}
?>