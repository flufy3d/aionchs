<?php
!function_exists('readover') && exit('Forbidden');
function convert($message,$allow,$type="post") 
{
	global $attachper, $code_num,$code_htm,$phpcode_htm,$foruminfo,$picpath,$imgpath,$stylepath,$attachname,$attachpath,$admincheck,$tpc_author,$tpc_buy,$i_table,$db_cvtimes,$forumset;
	if (file_exists(D_P."data/bbscache/wordsfb.php")){
		include(D_P."data/bbscache/wordsfb.php");
		$replace = $wordsfb + $replace;
		if($replace){
			foreach($replace as $key => $value){
				$message = str_replace($key,$value,$message);
			}
		}
	}
	$code_num=0;
	$code_htm=array();
	if(strpos($message,"[code]") !== false && strpos($message,"[/code]") !== false){
		$message=preg_replace("/\[code\](.+?)\[\/code\]/eis","phpcode('\\1')",$message,$db_cvtimes);
	}
	if(strpos($message,"[emule]") !== false && strpos($message,"[/emule]") !== false){
                $message=preg_replace("/\[emule\](.+?)\[\/emule\]/eis","emule('\\1')",$message);
        }

	if(strpos($message,"[payto]") !== false && strpos($message,"[/payto]") !== false){
		require_once(R_P.'require/paytofunc.php');
		$message=preg_replace("/\[payto\](.+?)\[\/payto\]/eis","payto('\\1')",$message);
	}
	if(strpos($message,"[download]") !== false && strpos($message,"[/download]") !== false){
		require_once(R_P.'require/downloadfunc.php');
		$message=preg_replace("/\[download\](.+?)\[\/download\]/eis","download('\\1')",$message);
	}

	$message = preg_replace('/\[list=([aA1]?)\](.+?)\[\/list\]/is', "<ol type=\\1>\\2</ol>", $message);
	$message = str_replace("[u]","<u>",$message);
	$message = str_replace("[/u]","</u>",$message);
	$message = str_replace("[b]","<b>",$message);
	$message = str_replace("[/b]","</b>",$message);
	$message = str_replace("[i]","<i>",$message);
	$message = str_replace("[/i]","</i>",$message);
	$message = str_replace("[list]","<ul>",$message);
	$message = str_replace('[*]', '<li>', $message);
	$message = str_replace("[/list]","</ul>",$message);
	$message = str_replace("p_w_upload",$attachname,$message);//此处位置不可调换
	$message = str_replace("p_w_picpath",$picpath,$message);//此处位置不可调换

	$searcharray = array(
		"/\[font=([^\[]*)\](.+?)\[\/font\]/is",
		"/\[color=([#0-9a-z]{1,10})\](.+?)\[\/color\]/is",
		"/\[box=([#0-9a-z]{1,10})\](.+?)\[\/box\]/is",
		"/\[typewriter\]([^\[]*)\[\/typewriter\]/is",
		"/\[crfont\]([^\[]*)\[\/crfont\]/is",
		"/\[dao\]([^\[]*)\[\/dao\]/is",
		"/\[strike\]([^\[]*)\[\/strike\]/is",
	        "/\[qq\]([^\[]*)\[\/qq\]/is",
		"/\[email=([^\[]*)\]([^\[]*)\[\/email\]/is",
	    "/\[email\]([^\[]*)\[\/email\]/is",
		"/\[size=(\d+)\](.+?)\[\/size\]/eis",
		"/(\[align=)(left|center|right)(\])(.+?)(\[\/align\])/is",
		"/\[glow=(\d+)\,([0-9a-zA-Z]+?)\,(\d+)\](.+?)\[\/glow\]/is"
	);
	$replacearray = array(
		"<font face='\\1'>\\2</font>",
		"<font color='\\1'>\\2</font>",
                "<blockquote style='margin-left:20px;  margin-right:20px; border:#DDE3EC  dashed 1px; padding:5px; background-color:#FFFFFF;background-color:\\1;'>\\2</blockquote>",
                "<span class='typewriter'>\\1</SPAN>",
                "<span class='rainbow'>\\1</SPAN>",
                "<span style='width:400;  font-family: Arial ;  position: relative; filter: flipv'>\\1</SPAN>",
                "<strike>\\1</strike>",
                "<a href='http://wpa.qq.com/msgrd?V=1&Uin=\\1&Site=十字佣兵&Menu=yes' target='_blank'>QQ：\\1</a>",
		"<a href='mailto:\\1'>E-mail：\\2</a>",
		"<a href='mailto:\\1'>E-mail：\\1</a>",
		"<a href='mailto:\\1'>\\2</a>",
		"<a href='mailto:\\1'>\\1</a>",
		"size('\\1','\\2','$allow[size]')",
		"<DIV Align=\\2>\\4</DIV>",
		"<span style='WIDTH:\\1;filter:glow(color=\\2,strength=\\3)'>\\4</span>"
	);
	$message=preg_replace($searcharray,$replacearray,$message);

	if ($allow['pic']){
		$message = preg_replace("/\[img\](.+?)\[\/img\]/eis","cvpic('\\1','','$allow[picwidth]','$allow[picheight]')",$message,$db_cvtimes);
    } else{
		$message = preg_replace("/\[img\](.+?)\[\/img\]/eis","nopic('\\1')",$message,$db_cvtimes);
	}

	if(strpos($message,'[/URL]')!==false || strpos($message,'[/url]')!==false){
		$searcharray = array(
			"/\[url=(https?|ftp|gopher|news|telnet|mms|rtsp)([^\[]*)\](.+?)\[\/url\]/eis",			
			"/\[url\]www\.([^\[]*)\[\/url\]/eis",
			"/\[url\](https?|ftp|gopher|news|telnet|mms|rtsp)([^\[]*)\[\/url\]/eis"
		);
		$replacearray = array(
			"cvurl('\\1','\\2','\\3')",
			"cvurl('\\1')",
			"cvurl('\\1','\\2')",
		);
		$message=preg_replace($searcharray,$replacearray,$message);
	}

	$searcharray = array(
		"/\[fly\]([^\[]*)\[\/fly\]/is",
		"/\[move\]([^\[]*)\[\/move\]/is",
	);
	$replacearray = array(
		"<marquee width=90% behavior=alternate scrollamount=3>\\1</marquee>",
		"<marquee scrollamount=3>\\1</marquee>",
	);
	$message=preg_replace($searcharray,$replacearray,$message);


	if($type=="post"){
		if(strpos($message,'[p:')!==false || strpos($message,'[s:')!==false){
			global $face,$motion,$act;
			include_once(D_P.'data/bbscache/postcache.php');
			$message=preg_replace("/\[s:(.+?)\]/eis","postcache('\\1','1')",$message,$db_cvtimes);

			$act="<font color=red><b>[$tpc_author]</b></font>";
			$message=preg_replace("/\[p:(.+?)\]/eis","postcache('\\1','2')",$message,$db_cvtimes);
		}

		if($foruminfo['allowhide'] && strpos($message,"[post]") !== false && strpos($message,"[/post]") !== false){
			$message=preg_replace("/\[post\](.+?)\[\/post\]/eis","post('\\1')",$message);
		}
		if($foruminfo['allowencode'] && strpos($message,"[hide") !== false && strpos($message,"[/hide]") !== false){
			$message=preg_replace("/\[hide=(.+?)\](.+?)\[\/hide\]/eis","hiden('\\1','\\2')",$message);
		}
		if($foruminfo['allowsell'] && strpos($message,"[sell") !== false && strpos($message,"[/sell]") !== false){
			$message=preg_replace("/\[sell=(.+?)\](.+?)\[\/sell\]/eis","sell('\\1','\\2')",$message);
		}
	}

	if ($allow['flash']){
        $message = preg_replace("/(\[flash=)(\d+?)(\,)(\d+?)(\])(.+?)(\[\/flash\])/is","<OBJECT CLASSID=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" WIDTH=\\2 HEIGHT=\\4><PARAM NAME=MOVIE VALUE=\\6><PARAM NAME=PLAY VALUE=TRUE><PARAM NAME=LOOP VALUE=TRUE><PARAM NAME=QUALITY VALUE=HIGH><EMBED SRC=\\6 WIDTH=\\2 HEIGHT=\\4 PLAY=TRUE LOOP=TRUE QUALITY=HIGH></EMBED></OBJECT><br />[<a target=_blank href=\\6>Full Screen</a>] ",$message,$db_cvtimes);
	}else{
		$message = preg_replace("/(\[flash=)(\d+?)(\,)(\d+?)(\])(.+?)(\[\/flash\])/is","<img src='$imgpath/$stylepath/file/music.gif' align='absbottom'> <a target=_blank href=\\6>flash: \\6</a>",$message,$db_cvtimes);
	}

	if (strpos($message,"[quote]") !== false && strpos($message,"[/quote]") !== false){
		$message=preg_replace("/\[quote\](.+?)\[\/quote\]/eis","qoute('\\1')",$message);
	}
	if(is_array($code_htm)){
		krsort($code_htm);
		foreach($code_htm as $codehtm){
			foreach($codehtm as $key=>$value){
				$message=str_replace("<\twind_code_$key\t>",$value,$message);
			}
		}
	}
	if($type=="post"){
		if($allow['mpeg']){
			global $lang;
			require_once GetLang('bbscode');
$message = preg_replace("/\[mp\](.+?)\[\/mp\]/eis","mediaplayer('\\1','-01')",$message,$db_cvtimes);
$message = preg_replace("/(\[mp=)([0-1]{1,1})(\])(.+?)(\[\/mp\])/eis","mediaplayer('\\4','\\2','-01')",$message,$db_cvtimes);
$message = preg_replace("/\[wmv\](.+?)\[\/wmv\]/eis","mediaplayer('\\1','-00')",$message,$db_cvtimes);
$message = preg_replace("/(\[wmv=)([0-9]{1,3})(\,)([0-9]{1,3})(\,)([0-1]{1,1})(\])(.+?)(\[\/wmv\])/eis","mediaplayer('\\8','\\6','\\2','\\4')",$message,$db_cvtimes);
$message = preg_replace("/\[rm\](.+?)\[\/rm\]/eis","realplayer('\\1')",$message,$db_cvtimes);
$message = preg_replace("/(\[rm=)([0-9]{1,3})(\,)([0-9]{1,3})(\,)([0-1]{1,1})(\])(.+?)(\[\/rm\])/eis","realplayer('\\8','\\2','\\4','\\6')",$message,$db_cvtimes);
/*--------同步歌词播放器--BY 猎英夜客--HTTP://www.3hz.cn*/
			$message = preg_replace("/\[mp3\](.+?)\[\/mp3\]/is","
<object classid=\"clsid:6bf52a52-394a-11d3-b153-00c04f79faa6\" id=\"aboutplayer\" width=\"480\" height=\"240\">
<param name=\"url\" value=\"\\1\">
<param name=\"volume\" value=\"100\">
<param name=\"enablecontextmenu\" value=\"0\">
<param name=\"enableerrordialogs\" value=\"0\">
</object>
<div id=\"lrcollbox\" style=\"overflow:hidden; height:260; width:480; background-color:#000000;\">
<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" id=\"lrcoll\" style=\"position:relative; top: -20px;\" oncontextmenu=\"return false;\">
<tr><td nowrap height=\"20\" align=\"center\" id=\"lrcwt1\"></td></tr>
<tr><td nowrap height=\"20\" align=\"center\" id=\"lrcwt2\"></td></tr>
<tr><td nowrap height=\"20\" align=\"center\" id=\"lrcwt3\"></td></tr>
<tr><td nowrap height=\"20\" align=\"center\" id=\"lrcwt4\"></td></tr>
<tr><td nowrap height=\"20\" align=\"center\" id=\"lrcwt5\"></td></tr>
<tr><td nowrap height=\"20\" align=\"center\" id=\"lrcwt6\"></td></tr>
<tr><td nowrap height=\"20\" align=\"center\">
<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
<tr><td nowrap height=\"20\"><span id=\"lrcwt7\" style=\"height:20\"></span></td></tr>
<tr style=\"position:relative; top: -20px; z-index:6\"><td nowrap height=\"20\"><div id=\"lrcfilter\" style=\"overflow:hidden; width:100%; color:#FFFF33; height:20\"></div></td></tr>
</table>
</td></tr>
<tr style=\"position:relative; top: -20px\"><td nowrap height=\"20\" align=\"center\">
<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
<tr><td nowrap height=\"20\"><span id=\"lrcbox\" style=\"height:20\">歌词加载中</span></td></tr>
<tr style=\"position:relative; top: -20px; z-index:6;\"><td nowrap height=\"20\"><div id=\"lrcbc\" style=\"overflow:hidden; height:20; width:0;\"></div></td></tr>
</table>
</td></tr>
<tr style=\"position:relative; top: -40px;\"><td nowrap height=\"20\" align=\"center\" id=\"lrcwt8\"></td></tr>
<tr style=\"position:relative; top: -40px;\"><td nowrap height=\"20\" align=\"center\" id=\"lrcwt9\"></td></tr>
<tr style=\"position:relative; top: -40px;\"><td nowrap height=\"20\" align=\"center\" id=\"lrcwt10\"></td></tr>
<tr style=\"position:relative; top: -40px;\"><td nowrap height=\"20\" align=\"center\" id=\"lrcwt11\"></td></tr>
<tr style=\"position:relative; top: -40px;\"><td nowrap height=\"20\" align=\"center\" id=\"lrcwt12\"></td></tr>
<tr style=\"position:relative; top: -40px;\"><td nowrap height=\"20\" align=\"center\" id=\"lrcwt13\"></td></tr>
</table>
</div><script language=\"JavaScript\" src=\"image/rs.js\"></script>
<form name=delatc action=\'masingle.php?action=delatc\' method=post>",$message,$db_cvtimes);
			$message = preg_replace("/\[3hz\](.+?)\[\/3hz\]/is","
</form><span id=\"lrcdata\">
<!--\\1
--></SPAN>",$message,$db_cvtimes);
/*--------同步歌词播放器--BY 猎英夜客--HTTP://www.3hz.cn*/
}else{
$message = preg_replace("/(\[mp=)([0-1]{1,1})(\])(.+?)(\[\/mp\])/eis","<img src='$imgpath/$stylepath/file/music.gif' align='absbottom'> <a target=_blank href='\\4'>\\4</a>",$message);
$message = preg_replace("/\[mp\](.+?)\[\/mp\]/eis","<img src='$imgpath/$stylepath/file/music.gif' align='absbottom'> <a target=_blank href='\\2'>\\2</a>",$message,$db_cvtimes);
$message = preg_replace("/(\[wmv=)([0-9]{1,3})(\,)([0-9]{1,3})(\,)([0-1]{1,1})(\])(.+?)(\[\/wmv\])/eis","<img src='$imgpath/$stylepath/file/music.gif' align='absbottom'> <a target=_blank href='\\8'>\\8</a>",$message,$db_cvtimes);
$message = preg_replace("/\[wmv\](.+?)\[\/wmv\]/eis","<img src='$imgpath/$stylepath/file/music.gif' align='absbottom'> <a target=_blank href='\\2'>\\2</a>",$message,$db_cvtimes);
$message = preg_replace("/(\[rm=)([0-9]{1,3})(\,)([0-9]{1,3})(\,)([0-1]{1,1})(\])(.+?)(\[\/rm\])/eis","<img src='$imgpath/$stylepath/file/music.gif' align='absbottom'> <a target=_blank href='\\8'>\\8</a>",$message,$db_cvtimes);
$message = preg_replace("/\[rm\](.+?)\[\/rm\]/eis","<img src='$imgpath/$stylepath/file/music.gif' align='absbottom'> <a target=_blank href='\\2'>\\2</a>",$message,$db_cvtimes);
/*--------同步歌词播放器--BY 猎英夜客--HTTP://www.3hz.cn*/
			$message = preg_replace("/\[mp3\](.+?)\[\/mp3\]/is","<img src='$imgpath/$stylepath/file/music.gif' align='absbottom'> <a target=_blank href='\\1'>\\1</a>",$message,$db_cvtimes);
			$message = preg_replace("/\[3hz\](.+?)\[\/3hz\]/is","<img src='$imgpath/$stylepath/file/music.gif' align='absbottom'> <a target=_blank href='\\1'>\\1</a>",$message,$db_cvtimes);
/*--------同步歌词播放器--BY 猎英夜客--HTTP://www.3hz.cn*/

		}
	}
	if(is_array($phpcode_htm)){
		foreach($phpcode_htm as $key=>$value){
			$message=str_replace("<\twind_phpcode_$key\t>",$value,$message);
		}
	}
	return $message;
}
function postcache($key,$type){
	if($type==1){
		global $face,$imgpath;
		return "<img src='$imgpath/post/smile/{$face[$key]}'>";
	}elseif($type==2){
		global $act,$motion,$imgpath;
		return "<br>$act {$motion[$key][1]}<br><img src=$imgpath/post/act/{$motion[$key][2]}><br>";
	}
}
function copyctrl($bgcolor='#FFFFFF'){

	$lenth=10;
	mt_srand((double)microtime() * 1000000);
	for($i=0;$i<$lenth;$i++){
		$randval.=chr(mt_rand(0,126));
	}
	$randval=str_replace('<','&lt;',$randval);
	return "<span style=\"font-size: 0pt;color:'$bgcolor'\" > $randval </span>&nbsp;<br>";
}

function attachment($message){
	global $attachper,$db_cvtimes;

	$attachper && $message=preg_replace("/\[attachment=([0-9]+)\]/eis","upload('\\1')",$message,$db_cvtimes);
	return $message;
}

function upload($aid){
	global $attachments,$aids;

	if($attachments[$aid]){
		$aids[]=$aid;
		return $attachments[$aid];
	} else{
		return "[attachment=$aid]";
	}
}

function size($size,$code,$allowsize){
	$allowsize && $size > $allowsize && $size = $allowsize;
	return "<font size='$size'>$code</font>";
}
function cvurl($http,$url='',$name=''){
	global $code_num,$code_htm;
	$code_num++;
	if(!$url){
		$url="<a href='http://www.$http' target=_blank>www.$http</a>";
	} elseif(!$name){
		$url="<a href='$http$url' target=_blank>$http$url</a>";
	} else{
		$url="<a href='$http$url' target=_blank>$name</a>";
	}
	$code_htm[0][$code_num]=$url;
	return "<\twind_code_$code_num\t>";
}

function nopic($url){
	global $code_num,$code_htm,$imgpath,$stylepath;
	$code_num++;
	$code_htm[-1][$code_num]="<img src='$imgpath/$stylepath/file/img.gif' align='absbottom' border=0> <a target=_blank href='$url'>img: $url</a>";
	return "<\twind_code_$code_num\t>";
}

function cvpic($url,$type='',$picwidth='',$picheight=''){
	global $db_bbsurl,$picpath,$attachpath,$code_num,$code_htm;
	$code_num++;
	if(strtolower(substr($url,0,4))!='http')$url="$db_bbsurl/$url";
	if($picwidth || $picheight){
		$onload = "onload=\"";
		$picwidth  && $onload .= "if(this.width>'$picwidth')this.width='$picwidth';";
		$picheight && $onload .= "if(this.height>'$picheight')this.height='$picheight';";
		$onload .= "\"";
		$code = "<img src='$url' border=0 onclick=\"if(this.width>=$picwidth) window.open('$url');\" $onload>";
	} else{
		$code = "<img src='$url' border=0 onclick=\"if(this.width>screen.width-461) window.open('$url');\">";
	}
	$code_htm[-1][$code_num]=$code;
	if($type){
		return $code;
	} else{
		return "<\twind_code_$code_num\t>";
	}
}

function phpcode($code){
	global $phpcode_htm,$codeid;
	$code=str_replace("[attachment=","&#91;attachment=",$code);
	$codeid ++;
	$phpcode_htm[$codeid]="<div style=\"font-size:9px;margin-left:5px\"><b>CODE:</b></div><div class=quote id='code$codeid'>$code</div><div style=\"font-size:11px;margin-left:5px\"><a href=\"javascript:\"  onclick=\"CopyCode(document.getElementById('code$codeid'));\">[Copy to clipboard]</a></div>";

	return "<\twind_phpcode_$codeid\t>";
}

function qoute($code){
	global $code_num,$code_htm,$i_table;
	$code_num++;
	$code_htm[6][$code_num]="<div style=\"font-size:9px;margin-left:5px\"><b>QUOTE:</b></div><div class=quote>$code</div>";
	return "<\twind_code_$code_num\t>";
}

function post($code){
	global $SYSTEM,$postcode1,$postcode2,$attachper,$db,$tid,$fid,$winduid,$windid,$admincheck,$groupid,$tpc_author,$i_table;
	global $code_num,$code_htm,$lang;
	require_once GetLang('bbscode');
	$code_num++;
	$attachper=0;
	$rs = $db->get_one("SELECT count(*) AS count FROM pw_posts WHERE tid='$tid' AND authorid='$winduid'");
	if($rs['count']>0){
		$havereply='yes';
	}
	if($admincheck==1 || $SYSTEM['viewhide'] || $havereply=='yes' || $tpc_author==$windid){
		$attachper=1;
		$code_htm[3][$code_num]="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$lang[bbcode_hide1]<br><div class=quote>{$code}</div>";
	} else{
		$code_htm[3][$code_num]="<br><br><div class=quote>$lang[bbcode_hide2]</div><br><br>";
	}
	return "<\twind_code_$code_num\t>";
}

function hiden($rvrc,$code){
	global $hidecode1,$hidecode2,$hidecode3,$db,$groupid,$attachper,$userrvrc,$i_table;
	global $code_num,$code_htm,$lang;
	require_once GetLang('bbscode');
	$code_num++;
	$attachper=0;
	if($groupid!='guest'){
		global $admincheck,$userrvrc,$userpath,$windid,$tpc_author,$SYSTEM;
		$rvrc=trim(intval(stripslashes($rvrc)));
		if($windid!=$tpc_author && $userrvrc<$rvrc && $admincheck!=1 && !$SYSTEM['viewhide']){
			$code="<div class=quote>{$lang[bbcode_encode1]}{$rvrc}</div>";
		} else {
			$attachper=1;
			$code="&nbsp; &nbsp; &nbsp; {$lang[bbcode_encode2]}<br><div class=quote>{$code}</div>";
		}
	} else{
		$code=$lang['bbcode_encode3'];
	}
	$code_htm[4][$code_num]=$code;
	return "<\twind_code_$code_num\t>";
}

function sell($moneycost,$code){
	global $SYSTEM,$admincheck,$attachper,$windid,$tpc_author,$tpc_buy,$fid,$tid,$pid,$i_table,$manager,$groupid,$code_num,$code_htm,$lang,$db_credits,$db_bbsurl;
	list($db_moneyname,,,,,)=explode("\t",$db_credits);
	require_once GetLang('bbscode');
	$code_num++;
	$sellcheck=$attachper=0;
	$moneycost = (int)$moneycost;
	if($moneycost < 0){
		$moneycost = 0;
	}elseif ($moneycost > 1000){
		$moneycost = 1000;
	}elseif ($moneycost && !ereg("^[0-9]{0,}$",$moneycost)){
		$moneycost = 0;
	}
	$userarray=explode(',',$tpc_buy);
	foreach($userarray as $value){
		if($value){
			$count++;
			$buyers.="<OPTION value=>".$value."</OPTION>";
		}
	}
	!$count && $count=0;
	if ($groupid!='guest'&& ($SYSTEM['viewhide'] || $admincheck || $tpc_author==$windid || ($userarray && @in_array($windid,$userarray)))){
		$sellcheck=1;
	}
	$attachper=$sellcheck;
	$bbcode_sell_info=str_replace(array('$moneycost','$db_moneyname','$count'),array($moneycost,$db_moneyname,$count),$lang['bbcode_sell_info']);
	if($groupid!='guest' && $sellcheck==1){
		$printcode="&nbsp; &nbsp; &nbsp; &nbsp;<span class='bold'><font color='red'>{$bbcode_sell_info}</font></span><select name='buyers'><OPTION value=''>{$lang[bbcode_sell_buy]}</OPTION>$buyers</select><br><div class=quote>$code</div>";
	}else{
		$printcode="<br><span class='bold'><font color='red'>{$bbcode_sell_info}</font></span><select name='buyers'><OPTION value=''>{$lang[bbcode_sell_buy]}</OPTION><OPTION value=>-----------</OPTION>$buyers</select><input type='button' value='{$lang[bbcode_sell_submit]}' style='color: #000000; background-color: #f3f3f3; border-style: solid; border-width: 1' onclick=location.href='$db_bbsurl/job.php?action=buytopic&tid=$tid'><br><br><font color=red>{$lang[bbcode_sell_notice]}</font><br>";
	}
	$code_htm[5][$code_num]=$printcode;
	return "<\twind_code_$code_num\t>";
}

function showfacedesign($usericon){
	$user_a=explode('|',$usericon);
	if (strpos($usericon,'<')!==false || empty($user_a[0]) && empty($user_a[1])){
		return "<img src='image/face/0.gif' border=0>";
	}
	global $imgpath;
	if ($user_a[1]){
		if(!ereg("^http",$user_a[1])){
			$user_a[1]="$imgpath/upload/$user_a[1]";
		}
		if($user_a[2] && $user_a[3]){
			return "<img src='$user_a[1]' width=$user_a[2] height=$user_a[3] border=0>";
		}else{
			return "<img src='$user_a[1]' border=0>";
		}
	} else {
		return "<img src='$imgpath/face/$user_a[0]' border=0>";
	}
}
/*------------------------------------------------PLAYER UBB CODE--BY KINPOO--HTTP://www.CNGUY.com---BEGIN*/
################################################config begin############################
//"true" or "1" is on,"false" or "0" is off
$player_file=true;		//是否显示影片地址
$player_color='#D2D2D2';	//播放器颜色~
$player_ubb_on=true;        //是否使用此UBBs for Discuz!
#################################################config end#############################
$player_num=0;
$player_pre=mt_rand();
function realplayer($url,$width=450,$height=350,$auto=0){
	global $player_pre,$player_num;
	$pid=$player_pre.'_'.$player_num;

	$fullscreen ="function Full_{$pid}(){if(!document.all.I_{$pid}.CanStop()){alert('影片未开始，无法全屏!');}else{alert('将进入全屏模式,按Esc键退出全屏!');document.all.I_{$pid}.SetFullScreen();}}";

	$player ="<OBJECT classid='clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA' id='I_{$pid}' width='$width' height='$height'>";
	$player.="<PARAM NAME='SRC' VALUE='#url#'>";
	$player.="<PARAM NAME='CONSOLE' VALUE='P_{$pid}'>";
	$player.="<PARAM NAME='CONTROLS' VALUE='Imagewindow'>";
	$player.="<PARAM NAME='AUTOSTART' VALUE='1'></OBJECT>";
	$player.="<br><OBJECT classid='CLSID:CFCDAA03-8BE4-11CF-B84B-0020AFBBCCFA' id='C_{$pid}' width='$width' height='32'>";
	$player.="<PARAM NAME='SRC' VALUE='#url#'>";
	$player.="<PARAM NAME='CONSOLE' VALUE='P_{$pid}'>";
	$player.="<PARAM NAME='CONTROLS' VALUE='controlpanel'>";
	$player.="<PARAM NAME='AUTOSTART' VALUE='1'></OBJECT>";

	$htmlcode=get_player_html($player,$pid,$fullscreen,$url,$auto);

	$player_num++;
	return $htmlcode;
}

function mediaplayer($url,$auto,$width=450,$height=350){
	global $player_num,$player_pre;
	$pid=$player_pre.'_'.$player_num;

	$fullscreen="function Full_{$pid}(){alert('将进入全屏模式!双击,退出全屏!\\n若影片未加载成功将无法进入全屏!');document.all.P_{$pid}.DisplaySize=3;}";

	if($auto=="-01" || $width=="-01"){
		$fullscreen="";
		$width=280;
		$height=69;
	}
	($auto==="-00" || $auto=="-01") && $auto=0;

	$player ="<OBJECT align='middle' classid='CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95' id='P_{$pid}' width='$width' height='$height'>";
	$player.="<param name='autostart' value='1'>";
	$player.="<param name='ShowStatusBar' value='-1'>";
	$player.="<param name='EnableContextMenu' value='0'>";
	$player.="<param name='Filename' value='#url#'></OBJECT>";

	$htmlcode=get_player_html($player,$pid,$fullscreen,$url,$auto);
	
	$player_num++;
	return $htmlcode;
}

function get_player_html($player,$pid,$full,$url,$auto){
	global $player_file,$player_color,$discuz_uid,$groupid;
	
	if($player_file){
		if(!defined('IN_DISCUZ') && $groupid=='guest'){
			$show_url="<a name='A_$pid'></a><a href='./login.php'><font color='#FFFFFF'><b>[ 下载-登陆 ]</b></font></a>";
		}elseif (defined('IN_DISCUZ') && !$discuz_uid){
			$show_url="<a name='A_$pid'></a><a href='./logging.php?action=login'><font color='#FFFFFF'><b>[ 下载-登陆 ]</b></font></a>";
		}else{
			$show_url="<a name='A_$pid' href='#url#' style='color=orangered'><font color='#FFFFFF'><b>[ 下载-媒体 ]</b></font></a>";
		}
	}else{
		$show_url="<a name='A_$pid'></a>";
	}
	if(!defined('IN_DISCUZ')) {
		$crlf="<br>";
	}else {
		$crlf="\r\n";
	}
	$url = trim($url);
	$url = explode($crlf,$url);
	$player_select = "<select name='SELECT_{$pid}' onChange=\"HTML_{$pid}(0);\"><option value=''>  === 选择播放 ===  </option>";
	foreach ($url as $key => $value) {
		if(!defined('IN_DISCUZ')) {
			global $wind_version;
			if ($wind_version<'4') {
				$value=preg_replace("/\[\twind_code_([0-9]+)\t\]/eis","PHPWind3_URL_Back('\\1')",$value);
			}
		}
		$value=str_replace('\"','"',$value);
		$value=preg_replace("/<a href=[\"']{1}(.[^\"']+)[\"']{1}(.+)<\/a>/i","\\1",$value);
		$url[$key]=explode('|',$value);
		$url[$key][0] = trim($url[$key][0]);
		if(!$url[$key][0]) continue;
		++$count;
		$url[$key][1] = " $count ".trim($url[$key][1]);
		$player_select.="<option value=\"{$url[$key][0]}\">{$url[$key][1]}</option>";
	}
	$player_select .= '</select>';
	$count = "<font color='$player_color'><b>共有 $count 集</b></font>";
	$show_url = str_replace("#url#",$url[0][0],$show_url);
	
	if ($full!='') $full_btn="\"<input type='button' name='btn_{$pid}_B_1' onclick='javascript:Full_{$pid}()' value='全屏播放'\" + btn_end";
	else $full_btn="\"\"";

	$btn_end =" style='background-color: $player_color;color: #FFFFFF;cursor: hand;filter: Alpha(Opacity=100, FinishOpacity=0, Style=3, StartX=70, StartY=70, FinishX=100, FinishY=100);font-weight: bolder;width: 100px;height:18px;border: 0px;'";
	$btn_end.=" onmouseover=\\\"this.style.filter='';this.style.color='#666666';this.style.background='#EEFFFF';\\\"";
	$btn_end.=" onmouseout=\\\"this.style.filter='Alpha(Opacity=100, FinishOpacity=0, Style=3, StartX=70, StartY=70, FinishX=100, FinishY=100)';this.style.color='#FFFFFF';this.style.background='$player_color';\\\">";

	$code ="<script language='javascript'>var btn_end=\"$btn_end\";";
	$code.="var HTM_{$pid}_A_0=\"<input type='button' name='btn_{$pid}_A_0' onclick='javascript:HTML_{$pid}(0)' value='观看媒体'\" + btn_end;";
	$code.="var HTM_{$pid}_A_1=\"<input type='button' name='btn_{$pid}_A_1' onclick='javascript:HTML_{$pid}(1)' value='关闭媒体'\" + btn_end;";
	$code.="var HTM_{$pid}_B_0=\"$count\";";
	$code.="var HTM_{$pid}_B_1=$full_btn;";
	$code.="var HTM_{$pid}_C_0='';";
	$code.="var HTM_{$pid}_C_1=\"$player\";";
	$code.="function ChangeUrl_{$pid}(url){";
	$code.="A_{$pid}.href=url;";
	$code.="document.all.TD_{$pid}_C.innerHTML=HTM_{$pid}_C_1.replace(/#url#/ig,url);";
	$code.="}";
	$code.="function HTML_{$pid}(close){";
	$code.="if(close){";
	$code.="document.all.TD_{$pid}_A.innerHTML=HTM_{$pid}_A_0;";
	$code.="document.all.TD_{$pid}_B.innerHTML=HTM_{$pid}_B_0;";
	$code.="document.all.TD_{$pid}_C.innerHTML=HTM_{$pid}_C_0;";
	$code.="}else{";
	$code.="document.all.TD_{$pid}_A.innerHTML=HTM_{$pid}_A_1;";
	$code.="document.all.TD_{$pid}_B.innerHTML=HTM_{$pid}_B_1;";
	$code.="var url=document.all.SELECT_{$pid}.options[document.all.SELECT_{$pid}.selectedIndex].value;";
	$code.="if(!url) {url=document.all.SELECT_{$pid}.options[1].value;}";
	$code.="ChangeUrl_{$pid}(url);";
	$code.="}} $full</script>";
	$code.="<table width='50%' style='border:4px double $player_color;background:#FFFFFF;' cellspacing='0' cellpadding='4'>";
	$code.="<tr bgcolor='$player_color'><td><font color='#FFFFFF'><b>[ 播放媒体文件 ]</b></font></td> <td align='right'>$show_url</td></tr>";
	$code.="<tr><td colspan='2'>$player_select</td></tr>";
	$code.="<tr><td id='TD_{$pid}_A'></td> <td align='right' id='TD_{$pid}_B'></td></tr>";
	$code.="<tr><td colspan='2' id='TD_{$pid}_C'></td></tr></table>";
	$code.="<script language='javascript'>HTML_{$pid}(".($auto?0:1).");</script>";

	if (!defined('IN_DISCUZ')) {
		global $code_num,$code_htm,$wind_version;
		if ($wind_version<'4') {
			$code_num++;
			$code_htm[2][$code_num]=$code;
			return "[\twind_code_$code_num\t]";
		}
	}
	return $code;
}
function PHPWind3_URL_Back($key) {
	global $code_htm;
	$temp = $code_htm[0][$key];
	unset($code_htm[0][$key]);
	return $temp;
}
/*------------------------------------------------PLAYER UBB CODE--BY KINPOO--HTTP://www.CNGUY.com-----END*/
function emule($code){
   global $num,$code_num,$code_htm,$tablecolor,$readcolorone,$readcolortwo;
   $code=str_replace("<br>","\n",$code);
   $code=str_replace("<br />","\n",$code);
   $code_array=explode("\n",$code);
   $rain="<br><script language='JavaScript' src='data/emule.js'></script><table width=98% align=center cellspacing=1 cellpadding=5 bgcolor=$tablecolor><tr bgcolor=$readcolorone><td align=center colspan='2'>下面是eMule专用的下载链接，您必须安装eMule才能点击下载    <a href='http://www.emule.org.cn/download' target=_blank><strong><font color=red>eMule官方下载地址</font></strong></a></td></tr>";
   foreach($code_array as $emule)
   {
       if($emule!=''){
       $emule_array=explode("|",$emule);
       $total+=$emule_array[3];
       $totalper=$emule_array[3];
       if($totalper>(1024*1024*1024*1024)){
           $totalper=round($totalper/1024/1024/1024/1024,2);
           $totalper.="TB";
       }elseif($totalper>(1024*1024*1024)){
           $totalper=round($totalper/1024/1024/1024,2);
           $totalper.="GB";
       }elseif($totalper>(1024*1024)){
           $totalper=round($totalper/1024/1024,2);
           $totalper.="MB";
       }else{
           $totalper=round($totalper/1024,2);
           $totalper.="KB";
           }
       $rain.="<tr bgcolor=$readcolortwo onmouseover=\"this.style.backgroundColor='$readcolorone'\" onmouseout=\"this.style.backgroundColor='$readcolortwo'\"><td class=smalltxt width=88% ><input type=\"checkbox\" name=\"EM42a795bb4b7d5$num\" value=\"$emule\" onclick=\"em_size('EM42a795bb4b7d5$num');\" checked=\"checked\"/><A href=\"$emule\"><font color='blue'><script language=\"javascript\">document.write(unescape(decodeURIComponent(\"$emule_array[2]\")));</script></font></a></td><td class=smalltxt align=center>$totalper</td></tr>";
   }}
   if($total>(1024*1024*1024*1024)){
       $total=round($total/1024/1024/1024/1024,2);
       $total.="TB";
   }elseif($total>(1024*1024*1024)){
       $total=round($total/1024/1024/1024,2);
       $total.="GB";
   }elseif($total>(1024*1024)){
       $total=round($total/1024/1024,2);
       $total.="MB";
   }else{
       $total=round($total/1024,2);
       $total.="KB";
       }
   $rain.="<tr bgcolor=$readcolorone><td align=left><input type=\"checkbox\" id=\"checkall_EM42a795bb4b7d5$num\" onclick=\"checkAll('EM42a795bb4b7d5$num',this.checked)\" checked=\"checked\"/> <label for=\"checkall_EM42a795bb4b7d5$num\">全选</label> <input type=\"button\" value=\"下载选中的文件\" onclick=\"download('EM42a795bb4b7d5$num',0,1)\"> <input type=\"button\" value=\"复制选中的链接\" onclick=\"copy('EM42a795bb4b7d5$num')\"><div id=\"ed2kcopy_EM42a795bb4b7d5$num\" style=\"position:absolute;height:0px;width:0px;overflow:hidden;\"></div></td><td class=smalltxt align=center id=\"size_EM42a795bb4b7d5$num\">$total</td></tr></table>";
       $code_num++; 
       $code_htm[7][$code_num]=$rain;
       $num++;
       unset($emule_array,$total,$rain,$code);
   return "<\twind_code_$code_num\t>";
}
?>