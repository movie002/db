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
$auth = array('bigboss'=>array('bigboss'),'10091'=>array('123456','klkl'));

srcdel();

function srcdel()
{
	//print_r($_POST);
	$delurl=$_POST['delurl'];
	$pageid = $_POST['pageid'];
	$passwd = $_POST['passwd'];
	
	if(checkpasswd($passwd,$pageid)===-1)
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
}

function checkpasswd($passwd,$pageid)
{
	global $auth;
	//print_r($auth);
	
	$res1=array_search($passwd,$auth['bigboss']);
	//echo 'res1: '.$res1."\n";
	if ($res1===false)
	{
		//echo "not bigboss \n";	
		$res2=array_search($passwd,$auth[$pageid]);
		//echo 'res2: '.$res2."\n";
		if ($res2===false)
		{
			//echo "not others \n";
		}
		else
		{
			//echo "good passwd \n";
			return 1;
		}	
	}
	return -1;
}
?>