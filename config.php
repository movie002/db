<?php
$dbip='localhost';
$dbuser='root';
$dbpasswd='root';
$dbname='dhmedia';

//页面存储的深度
$DH_page_store_deep = 3;
//每个深度的页面个数
$DH_page_store_count = 100;
date_default_timezone_set ('Asia/Shanghai');

//每页显示的记录的个数
$pagecount=15;
//预留几页的空间
//$pagebgen=2;

$DH_output_path= $_SERVER['DOCUMENT_ROOT'] . '/';
$DH_input_path= '/srv/movie002/';
$DH_home_url= 'http://movie002.com/';

//$DH_output_path= $_SERVER['DOCUMENT_ROOT'] . '/movie/';
//$DH_input_path= $_SERVER['DOCUMENT_ROOT'] . '/movie002/';
//$DH_home_url= 'http://127.0.0.1/movie/';

$DH_src_path= $DH_input_path. 'gen/';
$DH_html_path= $DH_src_path . 'html/';
$DH_output_html_path = $DH_output_path.'html/';
$DH_output_index_path = $DH_output_path.'index/';
$DH_html_url= $DH_home_url.'html/';
$DH_index_url= $DH_home_url.'index/';

function dh_mysql_query($sql)
{
	$rs = mysql_query($sql);
	$mysql_error = mysql_error();
	if($mysql_error)
	{
		echo 'dh_mysql_query error info:'.$mysql_error.'</br>';
		echo $sql;
		return null;
	}
	return $rs;
}

function output_page_path($basepath,$id) 
{	
	global $DH_page_store_deep,$DH_page_store_count;
	$result=$basepath;
	$useid = $id;
	for($i= $DH_page_store_deep-1;$i>0;$i--)
	{
		$cut = pow($DH_page_store_count,$i);
		$mod = floor($useid/$cut);
		$result .= $mod .'/';
		if (!file_exists($result))  
		{   
			mkdir($result,0777);
		}
		$useid = $useid%$cut;
	}
	$result .=$useid.'.html';
	return $result;	
}

function dh_file_get_contents($filename) 
{
	$fh = fopen($filename, 'rb', false)or die("Can not open file: $filename.\n");
	clearstatcache();
	if ($fsize = @filesize($filename)) {
		$data = fread($fh, $fsize);
	} else {
		$data = '';
		while (!feof($fh)) {
			$data .= fread($fh, 8192);
		}
	}
	fclose($fh);
	return $data;
}

function dh_file_put_contents($filename, $content) {
	
	// Open the file for writing
	$fh = @fopen($filename, 'wb', false)or die("Can not open file: $filename.\n");
	// Write to the file
	$ext=strrchr($filename,'.');
//	if ($ext=='.html')
//		$content = higrid_compress_html($content);
	@fwrite($fh, $content);
	// Close the handle
	@fclose($fh);
}


function dh_replace_snapshot($type='middle',$row,$DH_output_content,$needcountrytype=false)
{	
	global $conn,$linkway,$DH_html_url,$DH_html_path,$linkquality,$moviecountry,$DH_index_url;
	$DH_output_content_page = str_replace("%title%",$row['title'],$DH_output_content);
	$aka='';
	//if($row['aka'])
	//{
	//	$aka='<b>影片别名:</b> '.$row['aka'];
	//}
	//$DH_output_content_page = str_replace("%aka%",$aka,$DH_output_content_page);
	$DH_output_content_page = str_replace("%aka%",$row['aka'],$DH_output_content_page);
	//$imgposter = str_replace('spic','mpic',$row['imgurl']);
	//$imgnum=$count%5+1;
	//$imgposter =$imgurl='http://img'.$imgnum.'.douban.com/mpic/'.$row['imgurl'];
	
	$width='80px';
	$height='120px';
	if($type=='big')
	{
		$width='100px';
		$height='148px';
	}
	if($type=='small')
	{
		$width='40px';
		$height='60px';
	}
	
	$countrymeta='';
	if($needcountrytype===false)
		$countrymeta='';
	else
	{
		//$countrymeta='['.$moviecountry[$row['catcountry']].']';
		$countrymeta=' [<a href="'.$DH_index_url.$row['cattype'].'_'.$row['catcountry'].'_'.$needcountrytype.'/1.html">'.$moviecountry[$row['catcountry']].'</a>] ';
		if($row['quality']>=5)
		{
			$countrymeta.=' [<a href="'.$DH_index_url.$row['cattype'].'_'.$row['catcountry'].'_c/1.html">高清</a>]';
		}
	}
	$DH_output_content_page = str_replace("%moviemeta%",$countrymeta,$DH_output_content_page);
	
	$simgurl=$row['imgurl'];	
	$imgposter='';
	$page_path = output_page_path($DH_html_url,$row['id']);
	if($simgurl!='' && $simgurl[0]=='s')
	{
		$imgposter ='<a href="'.$page_path.'" target="_blank" title="'.$row['title'].'"><img style="width:'.$width.';height:'.$height.'" alt="'.$row['title'].'的海报" data-src="http://img3.douban.com/mpic/'.$simgurl.'" width="'.$width.'" height="'.$height.'"/></a>';
	}
	else
	{
		$imgposter = '<imgdao link_src="'.$page_path.'" img_src="http://img3.douban.com/view/photo/thumb/public/p'.$simgurl.'.jpg" style="witdh:'.$width.';height:'.$height.'" src_width="'.$width.'" src_height="'.$height.'" alt="'.$row['title'].'的海报"><span></span></imgdao>';
	}	
	$DH_output_content_page = str_replace("%imgposter%",$imgposter,$DH_output_content_page);
	
	$DH_output_content_page = str_replace("%title_link%",$page_path,$DH_output_content_page);
	
	//电视剧显示集数
	if($row['cattype']=='3')
	{
		preg_match('/<e>(.*?)<\/e>/',$row['meta'],$match);
		if(!empty($match[1]))
		{	
			$replace .= '&nbsp; <b>集数：</b>'.$match[1];
		}
	}

	$DH_output_content_page = str_replace("%way%",$linkquality[$row['quality']],$DH_output_content_page);
	//数字化的发型日期
	$updatetime=date("m-d",strtotime($row['updatetime']));
	$DH_output_content_page = str_replace("%updatetime%",$updatetime,$DH_output_content_page);	
	
	//发行日期
	preg_match('/<p>(.*?)<\/p>/',$row['meta'],$match);
	$pubdate='';
	if(!empty($match[1]))
	{	
		$pubdate = $match[1];
		$pubdate = str_replace(" ",'',$pubdate);
	}		
	//影片长度
	preg_match('/<i>(.*?)<\/i>/',$row['meta'],$match);
	$time='';
	if(!empty($match[1]))
	{	
		$time = $match[1];
		$time = str_replace(" ",'',$time);
	}
	$lengthall=mb_strlen($time,'UTF-8')+mb_strlen($pubdate,'UTF-8');
	if($lengthall>50)
	{
		$pubdate = '<span style="font-size:12px">'.$pubdate.'</span>';
		$time = '<span style="font-size:12px">'.$time.'</span>';
	}

	$DH_output_content_page = str_replace("%pubdate%",$pubdate,$DH_output_content_page);
	$DH_output_content_page = str_replace("%pubdate2%",$row['pubdate'],$DH_output_content_page);	
	$DH_output_content_page = str_replace("%time%",$time,$DH_output_content_page);	
	
	//国家
	preg_match('/<g>(.*?)<\/g>/',$row['meta'],$match);
	$replace='';
	if(!empty($match[1]))
	{	
		$replace = $match[1];
		$replace = str_replace(" ",'',$replace);
	}
	$DH_output_content_page = str_replace("%country%",$replace,$DH_output_content_page);
	
	//语言
	preg_match('/<l>(.*?)<\/l>/',$row['meta'],$match);
	$replace='';
	if(!empty($match[1]))
	{	
		$replace = $match[1];
		$replace = str_replace(" ",'',$replace);
	}
	$DH_output_content_page = str_replace("%language%",$replace,$DH_output_content_page);	

	//类别
	preg_match('/<t>(.*?)<\/t>/',$row['meta'],$match);
	if(!empty($match[1]))
	{	
		$replace = $match[1];
		$replace = str_replace(" ",'',$replace);
	}
	$DH_output_content_page = str_replace("%tags%",$replace,$DH_output_content_page);	

	$DH_output_content_page = str_replace("%rating%",$row['hot'],$DH_output_content_page);
	global $moviestatus;
	$mstatus='';
	if($row['mstatus']==2)
		$mstatus = '[<a href="'.$DH_index_url.'1_i/1.html">'.$moviestatus[$row['mstatus']].'</a>]';	
	if($row['mstatus']==3)
		$mstatus = '[<a href="'.$DH_index_url.'1_o/1.html">'.$moviestatus[$row['mstatus']].'</a>]';	
	$DH_output_content_page = str_replace("%mstatus%",$mstatus,$DH_output_content_page);
	
	// $sqllinks = "select way from link t where t.mediaid = '".$row['mediaid']."' group by way";
	// $reslinks=mysql_query($sqllinks,$conn);	
	// $way='';
	// if($reslinks)
	// {
		// while($rowlinks = mysql_fetch_array($reslinks))
		// {
			// $waynum = $rowlinks['way'];
			// $way.=$linkway[$waynum].'|';
		// }
		// $way = substr($way,0,strlen($way)-1);
	// }	
	// $DH_output_content_page = str_replace("%way%",$way,$DH_output_content_page);

	$DH_output_content_page = str_replace('%num1%',$row['ziyuan'],$DH_output_content_page);
	$DH_output_content_page = str_replace('%num2%',$row['yugao'],$DH_output_content_page);
	$DH_output_content_page = str_replace('%num3%',$row['yingping'],$DH_output_content_page);	
	$DH_output_content_page = str_replace('%num4%',$row['zixun'],$DH_output_content_page);

	return $DH_output_content_page;
}

?>