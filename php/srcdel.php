<?php
	header('Content-Type:text/html;charset= UTF-8'); 

	require_once('360safe/360webscan.php');
	require("../../php/common/common_gen.php");
	require("../../php/common/base.php");
	require("../../php/config.php");
	$conn=mysql_connect ($dbip, $dbuser, $dbpasswd) or die('数据库服务器连接失败：'.mysql_error());
	mysql_select_db($dbname, $conn) or die('选择数据库失败');
	mysql_query("set names utf8;");
	
	if (isset($_POST['email']))
	{ 
		//print '<pre>'; 
		//print_r($_POST); 
		//print '</pre>';
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
	else
	{
		echo '参数未设置!';
	}
?>