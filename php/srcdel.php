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

srcdel();

function srcdel()
{
	print_r($_POST);
	$delurl=$_POST['delurl'];
	$pageid = $_POST['pageid'];
	$sql="delete from link where link='$delurl' and pageid=$pageid;";
	echo $sql;
	if(dh_mysql_query($sql)==null)
	{
		echo '删除失败！';
		return;
	}
	echo '删除成功!刷新查看';
	//调用genpage重新生成页面
	get_file_curl("http://127.0.0.1/php/genv/gen_page.php?id=$pageid");
}
?>