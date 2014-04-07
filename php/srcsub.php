<?php
	header('Content-Type:text/html;charset= UTF-8'); 
	if(is_file('360safe/360webscan.php'))
	{
		require_once('360safe/360webscan.php');
	}
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
		$sql='insert into emailsub(id,email,updatetime);'
		dh_query($sql);
		$sql='insert into pagetmp(id,email,updatetime);'
		dh_query($sql);
		echo '订阅成功!';
	}
	else
	{
		echo '参数未设置!';
	}
?>