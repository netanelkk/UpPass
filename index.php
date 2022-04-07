<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once('config.php'); 
$q1 = $mysqli->query("SELECT * FROM `up2_settings` where `id`='1'");
$h = $q1->fetch_assoc(); $url = $h['url']; $adminmail = $h['adminmail']; $sname = $h['sname']; $maxsize = $h['maxsize']; $maxfiles = $h['maxfiles']; $rules = $h['rules']; 

if (isset($_COOKIE['up2log'])) {
$us = $mysqli->query("SELECT * FROM `up2_users`");
while($pw = $us->fetch_assoc()) { $mkcookie = md5(md5($pw['username']).','.$pw['password'].'-'.$pw['salt']); $cpw = $_COOKIE['up2log'];
if($cpw == $mkcookie) {
$canlogin = 1; $myid = $pw['id']; $mymail = $pw['mail']; $myuser = $pw['username']; $mytype = $pw['group']; $mydate = $pw['date']; $mypri = $pw['private']; $myban = $pw['ban'];
} } }

$do = $_GET['do']; if($myban > time() || $myban == 'f') { $bh = 'min-height:0'; } 
echo <<<Print
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="rtl">
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script> 
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/blitzer/jquery-ui.css" type="text/css" media="all"/>
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
<script src="$url/script.js"></script>
<link rel="icon" href="$url/images/favicon.png" type="image/png"> 
<title>$sname - העלאת קבצים</title>
<link rel="stylesheet" href="$url/style.css" type="text/css" media="all"/>
<style type="text/css">
@font-face {font-family:site; src:url("$url/images/default.woff"); }
@font-face {font-family:menu; src:url("$url/images/menu.woff"); }
@font-face {font-family:icon; src:url("$url/images/icons.woff"); }
body {font-family:site; $bh}
.menu {font-family:menu;}
.icon {font-family:icon;display:inline;}
</style>
</head>
<script type="text/javascript">
$(document).ready(function() {
$(".icon,.dotitle").tooltip({show:{effect:"drop",direction:"up",duration: 100},hide:{effect:"drop",direction:"up",duration: 100},track:false,position:{my:"center bottom-10",at:"center top"} });
$("#cap").attr("autocomplete","off");
if($(".title").text() != '') { $("title").text("$sname - " + $(".title").text()); }
$(".openoti").click(function() { $(".noti").show("drop",{direction:"up",easing:"easeOutExpo"},700); $(".bg").fadeIn(700); $(".reddot").hide("scale",200); $.ajax({type:"POST",url:"$url/action.php?do=viewed"}); });
$(".bg").click(function() { $(".noti").hide("drop",{direction:"up",easing:"easeOutExpo"},700); $(".bg").fadeOut(700); });
setInterval(function() { $.ajax({type:"POST",url:"$url/action.php?do=newnoti",success:function(data) { if(data != '0') { $(".reddot").show("scale",{easing:"easeOutElastic"},700); $(".notis").html(data);  } } });
},3000);
});
function abheight(c,an) { if(an == 1) { an = 350; } if(c == 0) { $(".abbox").animate({"height":$(".ab").css("height")},an); }else{ $(".abbox").animate({"height":$(".afterab").css("height")},an); }  }
function shower(content) { $(".er").show(); abheight(0,1); if($(".error").css("display") == "none") { $(".er").hide().slideDown(200); $(".error").html(content).slideDown(200);  }else{ $(".error").html(content).effect("shake",500); } }
function shownext() { $(".error").hide(); $(".ab").hide('slide', {direction: 'right'}, 350); $(".afterab").show('slide', {direction: 'right'}, 350);
abheight(1,1); }
</script>
<div class="bg"></div>
Print;
if($myban > time() || $myban == 'f') { if($myban != 'f') { $sec = $myban-time();
if($sec > 86400) { $left = round($sec/86400).' ימים'; }else{ if($sec > 3600) { $left = round($sec/3600).' שעות'; }else{ if($sec > 60) { $left = round($sec/60).' דקות'; }else{  $left = $sec.' שניות'; } } } $lefty = 'בעוד '.$left; }else{ $lefty = 'אף פעם'; }
echo <<<Print
<div class="page"><div class="title">הגישה נחסמה</div><div style="padding:10px;"> נחסמת עקב עבירה על חוקי האתר. <BR> החסימה תפוג $lefty. </div></div>
Print;
}else{
if($canlogin != 1) {
echo <<<Print
<script type="text/javascript">
$(document).ready(function() {
$("#logon").click(function() {
$(".bg").fadeIn(400);
$(".logbox").show("drop",{direction:"up",easing:"easeOutExpo"},400);
});
$(".bg").click(function() {
$(".bg").fadeOut(400);
$(".logbox").hide("drop",{direction:"up",easing:"easeOutExpo"},400);
});

$("#login").click(function(eventObject) {
eventObject.preventDefault();
var usr = $("#usr").val(),pw = $("#pw").val();
$.ajax({type:"POST",url:"$url/action.php?do=login",data:({usr:usr,pw:pw}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == "e1") {
$(".loginerror").html("שם משתמש או סיסמא לא נכונים!").slideDown(200).effect("shake",500);
}else{
if(data == "e2") {
$(".loginerror").html("המשתמש שלך לא אושר עדיין, אשר אותו מהמכתב שנשלח לך למייל.").slideDown(200).effect("shake",500);
}else{
$(".loginerror").removeClass("loginerror").css({"background":"#CFF1FD","padding":"5px"}).text("מתחבר..");
window.location.reload();
} }
}
});
});

});
</script>
<div class="logbox" style="background:url($url/images/login.png) no-repeat;">
<div class="loginerror" style="position:absolute;margin:-30px 35px 0 0;"></div>
<form action='' method="POST">
<table style="font-size:10pt;"><tr><td>שם משתמש:</td><td><input type="text" id="usr"></td></tr>
<tr><td>סיסמא:</td><td><input type="password" id="pw"></td></tr>
</table>
<input type="submit" value="התחבר" id="login"><BR>
<a href="$url/forgot" style="color:black; text-decoration:none; font-size:10pt;">שכחתי סיסמא</a>
</form></div>
Print;
}
if($canlogin == 1) { $q3 = $mysqli->query("SELECT * FROM `up2_noti` WHERE `to`='$myid' OR (`to`='0' AND '$mytype'='2') ORDER BY `id` DESC LIMIT 20"); $numnoti = $q3->num_rows; 
$q4 = $mysqli->query("SELECT * FROM `up2_noti` WHERE (`to`='$myid' OR (`to`='0' AND '$mytype'='2')) AND `viewed`='0' ORDER BY `id` DESC"); if($q4->num_rows == 0) { $redot = 'style="display:none;"'; } 
echo '<div class="userbar"><div style="float:right;padding-left:10px;font-size:13pt;">ברוך הבא, <a href="'.$url.'/profile/'.$myid.'" style="color:white;text-decoration:none;">'.$myuser.'</a>. </div><div style="float:right;">';
if($mytype == 2) {
echo '<div class="icon" title="פאנל ניהול" style="padding:0 7px;"><a href="'.$url.'/admin">&#xf084;</a></div>';
}
$stay = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
echo <<<Print
<script type="text/javascript">
$(document).ready(function() {
$(".clearnoti").click(function() {
$.ajax({type:"POST",url:"$url/action.php?do=clearnoti",beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200); $(".notis tr,.noticlear").remove(); $(".notis").html("<tr><td>אין התראות.</td></tr>"); }
}); });
});
</script>
Print;
if($numnoti == 0) { $cn = 'style="display:none;"'; }
echo '<div class="icon" title="התראות" style="padding:0 7px;position:relative;"><a href="javascript:void(0);" class="openoti"><div class="reddot" '.$redot.'></div>&#xf0f3;</a></div><div class="noti"><div class="windowtitle" style="font-size:13pt;">התראות <div class="clearnoti" '.$cn.'>נקה התראות</div><div style="clear:both;"></div></div><table class="notis">';
if($numnoti == 0) { echo '<tr><td>אין התראות.</td></tr>'; }
while($sz = $q3->fetch_assoc()) { $from = $sz['from']; $img = $sz['img']; $date = $sz['date']; $more = $sz['more']; $moremess = ''; $yellow = '';
$q4 = $mysqli->query("SELECT * FROM `up2_users` WHERE `id`='$from'")->fetch_assoc(); $f = $q4['username']; 
$q5 = $mysqli->query("SELECT * FROM `up2_images` WHERE `page`='$img'")->fetch_assoc(); $n = htmlspecialchars($q5['name']); $p = $q5['page']; $fname = $q5['fname']; if(mb_strlen($n,'UTF-8') > 15) { $n = substr($n,0,15).'..';}
if($more != '') { $numore = explode(",",$more); $moremess = 'ועוד '.(sizeof($numore)).' משתמשים'; }
if($sz['viewed'] == 0) { $yellow = 'background:rgb(254, 255, 204);'; } $task = 'הגיב/ו לתמונה'; if($sz['type'] == 1) { $task = 'השיב להודעתך'; }
echo '<tr>';
if($sz['type'] == 3 && $mytype == 2) {
echo '<td style="'.$yellow.'" colspan="2"><a href="'.$url.'/admin/reports" style="color:#7F3939;font-weight:bold;">דיווח חדש ממתין בפאנל הניהול.<div style="font-size:10pt;">'.$date.'</div></a></td>';
}else{
echo '<td style="'.$yellow.'padding:5px;"><img src="'.$url.'/i/'.$fname.'" style="max-width:60px;max-height:60px;"></td><td style="'.$yellow.'"><a href="'.$url.'/links/'.$p.'" style="color:black;"><b>'.$f.' '.$moremess.' </b> '.$task.' <b>'.$n.'</b>.<div style="font-size:10pt;">'.$date.'</div></a></td>';
}
echo '</tr>';
}
echo '</table></div>  <div class="icon" title="הגדרות" style="padding:0 7px;"><a href="'.$url.'/settings">&#xf013;</a></div>  <div class="icon" title="התנתק" style="padding:0 5px;"><a href="'.$url.'/logout?stay='.$stay.'">&#xf08b;</a></div></div><div style="clear:both;"></div></div>';
}else{
echo '<div class="userbar">ברוך הבא אורח! <a href="javascript:void(0);" id="logon">התחבר</a> או <a href="'.$url.'/register">הירשם</a>.</div>';
}
echo <<<Print
<div class="loadbar"><img src="$url/images/loading.gif" style="width:15px; height:15px;"> טוען..</div>
<div style="width:90%;margin:auto;"><div style="float:left; margin:15px 0 0 15px;"><a href="$url"><img src="$url/images/logo.png"></a></div>
<div class="menu">
<li><a href="$url">ראשי</a></li>
<li><a href="$url/myfiles">ניהול קבצים</a></li>
<li><a href="$url/rules">תקנון</a></li>
<li><a href="$url/contact">צור קשר</a></li>
</div><div style="clear:both;"></div></div>
Print;

if($do == admin && $mytype == 2) {
$act = $_GET['act'];
$fsize = '40pt'; if($act == '') { $fsize = '80pt'; }
echo <<<Print
<script type="text/javascript">
$(document).ready(function() {
$(".adminmenu").click(function() {
$(".adminmenu .icon").animate({"font-size":"40pt"},100);
});
});
</script>
<div class="page"> <div class="title">פאנל ניהול</div>
<div style="padding:10px;">
<div class="adminmenu"><a href="$url/admin/users"><div class="icon" style="padding:0 7px;font-size:$fsize;">&#xf007;</div><div>משתמשים</div></a></div>
<div class="adminmenu"><a href="$url/admin/imgs"><div class="icon" style="padding:0 7px;font-size:$fsize;">&#xf03e;</div><div>קבצים</div></a></div>
<div class="adminmenu"><a href="$url/admin/reports"><div class="icon" style="padding:0 7px;font-size:$fsize;">&#xf06a;</div><div>דיווחים</div></a></div>
<div class="adminmenu"><a href="$url/admin/settings"><div class="icon" style="padding:0 7px;font-size:$fsize;">&#xf013;</div><div>הגדרות</div></a></div>
Print;
if($act == settings) {
echo <<<Print
<script type="text/javascript">
$(document).ready(function() {
$("#set").click(function(eventObject) {
eventObject.preventDefault();
var url = $("#url").val(),adminmail = $("#adminmail").val(),sname = $("#sname").val(),maxsize = $("#maxsize").val(),maxfiles = $("#maxfiles").val(),rules = $("#rules").val();
$.ajax({type:"POST",url:"$url/action.php?do=siteset",data:({url:url,adminmail:adminmail,sname:sname,maxsize:maxsize,maxfiles:maxfiles,rules:rules}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == 'e1') {
$(".error").html("אנא הקש את כתובת האתר.").slideDown(200).effect("shake",500);
}else{
if(data == 'e2') {
$(".error").html("אנא הקש את מייל האדמין.").slideDown(200).effect("shake",500);
}else{
if(data == 'e3') {
$(".error").html("אנא הקש את שם האתר.").slideDown(200).effect("shake",500);
}else{
if(data == 'e4') {
$(".error").html("אנא הקש את הגודל המקסימלי.").slideDown(200).effect("shake",500);
}else{
if(data == 'e5') {
$(".error").html("אנא הקש את מקסימום הקבצים להעלאה.").slideDown(200).effect("shake",500);
}else{
if(data == 'e6') {
$(".error").html("אנא הקש את תקנון האתר.").slideDown(200).effect("shake",500);
}else{
$(".error").hide();
$("#sett").hide(300);
$("#afters").delay(300).show(300);
} } } } } } }
});
});
});
</script>
Print;
$servermaxfiles = ini_get('max_file_uploads');
$q49 = $mysqli->query("SELECT * FROM `up2_settings` WHERE `id`='1'");
while($se = $q49->fetch_assoc()) { $yurl = $se['url']; $yam = $se['adminmail']; $ynm = $se['sname']; $yms = $se['maxsize'];
$ymf = $se['maxfiles']; $yrules = $se['rules'];
echo <<<Print
<div id="afters" style="display:none;">&#1492;&#1513;&#1497;&#1504;&#1493;&#1497;&#1497;&#1501; &#1489;&#1493;&#1510;&#1506;&#1493; &#1489;&#1492;&#1510;&#1500;&#1495;&#1492;!</div>
<div id="sett">
<div class="error"></div>
<form action='' method="POST">
<div style="padding:7px;">שם האתר:<BR><input type="text" id="sname" value="$ynm" style="width:50%;"></div>
<div style="padding:7px;">כתובת האתר:<BR><input type="text" id="url" value="$yurl" style="width:50%;"></div>
<div style="padding:7px;">מייל לצור קשר:<BR><input type="text" id="adminmail" value="$yam" style="width:50%;"></div>
<div style="padding:7px;float:right;">גודל מקסימלי לקובץ (במגה-בייטים):<BR><input type="text" id="maxsize" value="$yms" style="width:100%;"></div>
<div style="padding:7px;float:right;">כמות מקסימלית של קבצים בהעלאה בודדת (מקסימום $servermaxfiles קבצים) :<BR><input type="text" id="maxfiles" value="$ymf" style="width:100%;"></div>
<div style="clear:both;"></div>
<div style="padding:7px;">תקנון האתר: <BR><textarea rows="7" id="rules" style="width:50%;">$yrules</textarea></div>
<BR>
<input type="submit" id="set" value="&#1513;&#1504;&#1492;">
</form>
</div>
Print;
}
}else{
if($act == reports) {
echo <<<Print
<script type="text/javascript">
$(document).ready(function() {
$("body").on("click",".adelcom",function() { var ban = 0; if(confirm("האם לתת גם באן לשבוע לכותב התגובה?")) { ban = 1; }
var id = $(this).parent().attr("data-comid"), rid = $(this).parent().attr("id").slice(1); 
$.ajax({type:"POST",url:"$url/action.php?do=delcom",data:({comid:id,ban:ban,rid:rid}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == 'e1') { alert("אינך רשאי לחסום את עצמך"); }
$("#r"+rid).hide(400);
}
});
});
$("body").on("click",".delrep",function() { var rid = $(this).parent().attr("id").slice(1); 
$.ajax({type:"POST",url:"$url/action.php?do=delrep",data:({rid:rid}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
$("#r"+rid).hide(400);
}
});
});
});
</script>
Print;
$q58 = $mysqli->query("SELECT * FROM `up2_reports` ORDER BY `id` DESC");
if($q58->num_rows == 0) { echo 'אין דיווחים'; } $e = 2;
while($sa = $q58->fetch_assoc()) { $rid = $sa['id']; $com = $sa['com']; $reporter = $sa['user']; $redate = $sa['date']; $reason = $sa['reason']; 
$q59 = $mysqli->query("SELECT * FROM `up2_comments` WHERE `id`='$com'"); 
if($q59->num_rows == 0) { $mysqli->query("DELETE FROM `up2_reports` WHERE `id`='$rid'"); $mysqli->query("DELETE FROM `up2_comments` WHERE `id`='$com'"); if($e != 0) { $e = 1; } }else{ $sb = $q59->fetch_assoc(); 
$content = $sb['content']; $poster = $sb['user']; $imgurl = $sb['img']; $q60 = $mysqli->query("SELECT * FROM `up2_images` WHERE `page`='$imgurl'"); 
if($q60->num_rows == 0) { $mysqli->query("DELETE FROM `up2_reports` WHERE `id`='$rid'"); $mysqli->query("DELETE FROM `up2_comments` WHERE `id`='$com'"); if($e != 0) { $e = 1; } }else{
$sc = $mysqli->query("SELECT * FROM `up2_users` WHERE `id`='$poster'")->fetch_assoc(); $puser = $sc['username'];
$sd = $mysqli->query("SELECT * FROM `up2_users` WHERE `id`='$reporter'")->fetch_assoc(); $ruser = $sd['username']; $e = 0;
echo <<<Print
<div style="width:190px;float:right;margin:5px;padding:5px;border-left:1px solid #EFEFEF;" id="r$rid" data-comid="$com"><div style="margin-right:9px;">
<span style="font-size:15pt;">"</span>$content<span style="font-size:15pt;">"</span><div style="font-size:8pt;color:#666666;"><a href="$url/links/$imgurl" target="_blank">~ פורסם ע"י</a> <a href="$url/profile/$poster" target="_blank">$puser</a></div></div>
<div style="font-size:10pt;padding:7px 3px;margin-top:2px;"><div style="float:right;">דווח ע"י <a href="$url/profile/$reporter" target="_blank">$ruser</a>.</div><div style="float:left;font-size:8pt;margin-top: 2px;">$redate</div><div style="clear:both;"></div> <b>סיבת הדיווח:</b> $reason</div>
<div class="adelcom">מחק תגובה</div> <div class="delrep">מחק דיווח</div>
</div>
Print;
} } }
if($e == 1) { echo 'אין דיווחים'; }
echo '<div style="clear:both;"></div>';
}else{
if($act == imgs) {
$q51 = $mysqli->query("SELECT * FROM `up2_images`"); $n51 = ceil($q51->num_rows/60);
echo <<<Print
<script type="text/javascript">
$(document).ready(function() {
var page = 1,pages = $n51;
$(".remove,.view").tooltip({content: function () {return $(this).prop('title');}, track: true, show: {effect: "fadeIn", duration: 200},hide: {effect: "fadeOut", duration: 200}});
$("body").on("click",".remove",function() { if(confirm("האם אתה בטוח שברצונך למחוק את התמונה? פעולה זו בלתי הפיכה!")) {
var id = $(this).parent().parent().attr("id").slice(1);
$.ajax({type:"POST",url:"$url/action.php?do=imdel",data:({id:id}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
$("#f"+id).hide(400);
}
});
}
});
$("#load").click(function() {
$.ajax({type:"POST",url:"$url/action.php?do=loadimg",data:({page:page}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
$(data).appendTo(".allimgs"); $(".newimgs").show("drop",{direction:"down"},450);
if(page >= pages) { $("#load").hide(); }
$(".remove,.view").tooltip({content: function () {return $(this).prop('title');}, track: true, show: {effect: "fadeIn", duration: 200},hide: {effect: "fadeOut", duration: 200}});
}
});
page++;
});
$(window).scroll(function() {
if($(window).scrollTop() == $(document).height() - $(window).height() && $("#load").css("display") != 'none') { $("#load").trigger("click"); }
});
});
</script>
<div class="allimgs">
Print;
$q50 = $mysqli->query("SELECT * FROM `up2_images` ORDER BY `id` DESC LIMIT 60");
if($q50->num_rows == 0) { 
echo '<div style="width:800px; text-align:right; font-weight:bold;">&#1488;&#1497;&#1503; &#1514;&#1502;&#1493;&#1504;&#1493;&#1514; &#1489;&#1513;&#1512;&#1514;</div>';
}else{
while($t = $q50->fetch_assoc()) { $fid = $t['id']; $finame = htmlspecialchars($t['name']); $fname = $t['fname']; $fisize = round($t['size']/(1024*1024),3); $mdate = $t['date']; $mip = $t['ip']; $muser = $t['user']; $pg = $t['page'];
$phpdate = strtotime($mdate); $fidate = date('j.n.y - H:i', $phpdate);
if($muser == '0') { $username = '<b>אורח</b>'; }else{ $udet = $mysqli->query("SELECT * FROM `up2_users` WHERE `id`='$muser'")->fetch_assoc(); $username = '<a href="'.$url.'/profile/'.$muser.'" target="_blank">'.$udet['username'].'</a>'; }
if(mb_strlen($finame,'UTF-8') > 15) { $finame = mb_substr($finame,0,15,'UTF-8').'..';}
echo <<<Print
<div id="f$fid" style="text-align:center;float:right;"><div style="background:url($url/i/$fname) no-repeat; background-position:center; background-size:cover;width:200px;height:200px;margin:10px;position:relative;"><div style="background:#D87575;font-size:8pt;border-radius:5px;padding:2px 6px;position:absolute;bottom:-10px;left:-10px;color:white;font-weight:bold;" class="remove"><div class="icon">&#xf014;</div> מחק</div></div><a href="$url/links/$pg" target="_blank">$finame <BR> <div style="background:#EAEAEA;font-size:10pt;border-radius:5px;padding:2px 6px;display:inline-block;">$username</div> $fidate </a></div>
Print;
} }
echo '</div>';
if($n51 > 1) {
echo '<div style="clear:both;"></div><div id="load">טען עוד תמונות</div>';
}
}else{
if($act == users) { $now = time(); $mysqli->query("UPDATE `up2_users` SET `ban`='0' WHERE `ban`<'$now' AND `ban`!='f'");
$q61 = $mysqli->query("SELECT * FROM `up2_users`"); $n61 = ceil($q61->num_rows/50);
echo <<<Print
<script type="text/javascript">
$(document).ready(function() {
var page = 1,pages = $n61;
$(".change,.remove").tooltip({track: true, show: {effect: "fadeIn", duration: 200},hide: {effect: "fadeOut", duration: 200}});
$("#load").click(function() {
$.ajax({type:"POST",url:"$url/action.php?do=loadusr",data:({page:page}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
$(data).appendTo("table");
if(page >= pages) { $("#load").hide(); }
$(".change,.remove").tooltip({track: true, show: {effect: "fadeIn", duration: 200},hide: {effect: "fadeOut", duration: 200}});
}
});
page++;
});
$("body").on("click",".change",function() {
var uid = $(this).parent().parent().attr("id").slice(1);
$.ajax({type:"POST",url:"$url/action.php?do=agroup",data:({id:uid}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == 'e1') { alert("לא ניתן להפוך את עצמך למשתמש.") }else{
$("#u"+uid+" #group").html(data); }
}
});
});
$("body").on("click",".remove",function() { if(confirm("אתה בטוח שאתה רוצה למחוק את המשתמש? פעולה זו תמחוק כל זכר למשתמש זה, כולל כל תמונותיו.")) { 
var uid = $(this).parent().parent().attr("id").slice(1);
$.ajax({type:"POST",url:"$url/action.php?do=adel",data:({id:uid}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == 'e1') {
alert("לא ניתן למחוק את עצמך.");
}else{
$("#u"+uid).hide(300);
} }
});
}
});
$(window).scroll(function() {
if($(window).scrollTop() == $(document).height() - $(window).height() && $("#load").css("display") != 'none') { $("#load").trigger("click"); }
});
$("body").on("change",".ban",function() { var days = $(this).val(), uid = $(this).parent().parent().attr("id").slice(1);
$.ajax({type:"POST",url:"$url/action.php?do=ban",data:({days:days,uid:uid}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == 'e1') { alert("אינך יכול לחסום את עצמך."); }else{ } }
});
});
});
</script>
<table class="usr"><thead><tr><td>שם משתמש</td><td>מייל</td><td style="width:150px;">אייפי</td><td style="width:100px;">תאריך הרשמה</td><td style="width:70px;">תמונות</td><td style="width:70px;">קבוצה</td><td style="width:50px;">אפשרויות</td></tr></thead><tbody>
Print;
$q32 = $mysqli->query("SELECT * FROM `up2_users` ORDER BY `id` DESC LIMIT 50");
while($gt = $q32->fetch_assoc()) { $id = $gt['id']; $usr = $gt['username']; $mail = $gt['mail']; $ip = $gt['ip']; $date = $gt['date']; $group = $gt['group']; $ban = '';
if($gt['ban'] == 'f') { $ban = '<span style="color:#874242;font-size:8pt;">חסום לנצח</span>'; }else{
if($gt['ban'] != '' && $gt['ban'] != '0') { $ban = '<span style="color:#874242;font-size:8pt;">חסום לעוד<BR>'.round((intval($gt['ban'])-time())/86400).' ימים</span>'; } }
$numimg = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$id'")->num_rows;
if($group == 2) { $group = 'אדמין'; }else{
if($group == 1) { $group = 'משתמש'; }else{ $group = 'לא מאושר'; } }
echo <<<Print
<tr id="u$id"><td><a href="$url/profile/$id" target="_blank" style="color:#2B6A91">$usr</a></td><td>$mail</td><td>$ip</td><td>$date</td><td>$numimg</td><td id="group">$group<BR>$ban</td><td><select style="font-size:8pt;font-family:arial;" class="ban"><option disabled selected>חסום..</option><option value="0">בטל באן</option><option value="1">יום</option><option value="3">3 ימים</option><option value="7">שבוע</option><option value="30">חודש</option><option value="365">שנה</option><option value="f">לנצח</option></select><img src="$url/images/change.png" title="החלף קבוצה" class="change"> <img src="$url/images/remove.png" title="מחק משתמש" class="remove"></td></tr>
Print;
}
echo '</tbody></table>';
if($n61 > 1) {
echo '<div id="load">טען עוד משתמשים</div>';
}
} } } }
echo '</div><div style="clear:both;"></div></div>';
}else{
if($do == restore) {
$id = $mysqli->real_escape_string($_GET['id']);
echo <<<Print
<div class="page">
<div class="title">שחזור סיסמא</div><div style="padding:10px;">
Print;
$q96 = $mysqli->query("SELECT * FROM `up2_users` WHERE `re`='$id'");
if($q96->num_rows == 0) {
echo <<<Print
לא ניתן לשחזר את הסיסמא. <BR>
בדוק כי הקישור שהוקש נכון וכי לא נכנסת לקישור זה בעבר.
</div></div>
Print;
}else{
$chars = "abcdefghigklmnopqrstuvmxyz0123456789"; $newpw = substr(str_shuffle($chars),0,9); $new5 = md5($newpw);
$mysqli->query("UPDATE `up2_users` SET `password`='$new5',`re`='' WHERE `re`='$id'");
echo <<<Print
<script type="text/javascript">
$(document).ready(function() {
var clicked = 0;
$(".showpw").click(function() {
if(clicked == 0) {
$(this).fadeOut(200,function() {
$(this).html("הסיסמא החדשה שלך היא: " + $(this).attr("id")+ ". <BR> תוכל לשנות אותה בהגדרות המשתמש בכל עת.").fadeIn(200).css("cursor","default");
});
clicked = 1; }
});
});
</script>
הסיסמא שוחזרה בהצלחה! <BR>
<div class="showpw" id="$newpw" style="cursor:pointer;">לחץ כאן כדי להציג את הסיסמא</div>
</div></div>
Print;
}
}else{
if($do == forgot) {
echo <<<Print
<script type="text/javascript">
$(document).ready(function() {
abheight(0,0);
$("#restore").click(function(eventObject) {
eventObject.preventDefault();
var uom = $("#uom").val(), cap = $("#cap").val();
$.ajax({type:"POST",url:"$url/action.php?do=restore",data:({uom:uom,cap:cap}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == "e1") {
shower("לא קיים משתמש כזה.");
}else{
if(data == "e2") {
shower("האימות נכשל. נסה שוב.");
}else{
shownext();
} } }
}); });
});
</script>
<div class="page">
<div class="title">שכחתי סיסמא</div>
<div class="abbox">
<div style="display:none; float:right;position:absolute;" id="afterf" class="afterab">קישור לאיפוס הסיסמא נשלח למייל.</div>
<div style="width:880px;float:right; position:absolute;" id="fgot" class="ab">
<div class="er"><div class="error"></div></div>
<form action='' method="POST">
שם משתמש או מייל: <input type="text" id="uom"> <BR><BR>אימות אבטחה:<BR>
<div style="width:350px;float:right;">
<div style="float:right;height:58px;"><img style="background:white;padding:5px;border-radius:3px;" src="$url/captcha.jpg" id="capimg"></div>
<div style="float:right;"><input type="text" id="cap" maxlength="3" style="width:50px;background:white;margin:12px 15px 0 0;text-align:center;border-radius:7px;" title="הכנס את המספר המופיע בצד ימין (בספרות)" class="dotitle"></div><div style="clear:both;"></div>
</div><div style="clear:both;"></div>
<input type="submit" value="שחזר סיסמא" id="restore">
</form>
</div></div>
<div style="clear:both;"></div>
</div>
Print;
}else{
if($do == profile) {
$id = $mysqli->real_escape_string($_GET['id']);
$q77 = $mysqli->query("SELECT * FROM `up2_users` WHERE `id`='$id' AND (`private`='0' OR `id`='$myid')");
if($q77->num_rows == 0 && $mytype != 2) {
echo <<<Print
<div class="page"><div class="title">משתמש לא נמצא</div><div style="padding:5px;">
משתמש כזה לא נמצא. <BR> ייתכן כי הוא הגדיר את חשבונו כפרטי.</div>
</div>
Print;
}else{
if($mytype == 2) { $q77 = $mysqli->query("SELECT * FROM `up2_users` WHERE `id`='$id'");  }
if($myid == $id) { $editphoto = '<div class="ephoto" style="display:none;width:128px;height:22px;position:absolute;bottom:0;">
<form action="'.$url.'/action.php?do=newpimg" method="post" enctype="multipart/form-data" class="newpimg"><input type="file" id="img" name="img" style="width:128px;height:22px;position:absolute;bottom:0;z-index:10;cursor:pointer;opacity:0;" accept="image/*"></form><div class="editphoto">שנה תמונת פרופיל</div></div>'; 
$jsedit = '$("#img").change(function() { var finfo = this.files[0], ftype = finfo.type; if(ftype != "image/jpg" && ftype != "image/jpeg" && ftype != "image/png" && ftype != "image/gif" && ftype != "image/bmp") { alert("יש לבחור קובץ מסוג תמונה בלבד."); }else{ if(Math.round(finfo.size/1024)/1000 > '.$maxsize.') { alert("הגודל המקסימלי לתמונה הוא '.$maxsize.' MB."); }else{ $(".newpimg").ajaxForm({beforeSend: function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); },complete: function(xhr) { var imgurl = xhr.responseText;  $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200); $(".prophoto").fadeOut(200,function() { $(".prophoto").css("background-image","url("+imgurl+")").fadeIn(200); }); } }); $(".newpimg").submit(); } } });'; }
$fr = $q77->fetch_assoc(); $uname = $fr['username']; $group = $fr['group']; $date = $fr['date']; $img = $url.'/i/'.$fr['img']; if($fr['img'] == '') { $img = $url.'/images/noimg.png'; }
if($group != 1 && $group != 2) { $group = 0; } if($fr['private'] == '1') {  $pmess = '<span style="color:gray;font-style:italic;font-size:10pt;">~משתמש פרטי (רק אתה יכול לצפות בו)</span>'; }
$g = array('<span style="color:gray;">לא מאושר</span>','<span style="color:black;">משתמש רשום</span>','<span style="color:#7C3434;">אדמין</span>');
$imgcount = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$id' AND `private`='0'")->num_rows;
echo <<<Print
<script type="text/javascript">
$(document).ready(function() {
var lpage = 0,nowon = 0, pid = "$id", diff = 1, c = 0;
$(".out").hover(function() {
$(".gallerybox").stop().animate({"width":"150px","height":"150px","top":"0px","left":"0px"},300,"easeOutExpo"); $(".galleryname").stop().animate({"font-size":"12pt"},300,"easeOutExpo"); $(this).find(".gallerybox").stop().animate({"width":"180px","height":"180px","top":"-30px","left":"-30px"},300,"easeOutExpo");
$(this).find(".galleryname").stop().animate({"font-size":"20pt"},300,"easeOutExpo"); 
}).on("mouseleave",function() {
$(".gallerybox").stop().animate({"width":"150px","height":"150px","top":"0px","left":"0px"},300,"easeOutExpo"); $(".galleryname").stop().animate({"font-size":"12pt"},300,"easeOutExpo"); 
});
$(".gallerybox").click(function() { var gid = $(this).attr("data-gid"), thispg = '$url/profile/'+pid+'#'+gid; lpage = 0; history.pushState({id:c}, '', '#'+gid); c++;
$(".out").animate({"width":"100px","height":"100px"},350); diff = 1.5; $(".pimglist").html('<div class="pload">טוען..</div>').show("puff",350);
$.ajax({type:"POST",url:"$url/action.php?do=loadpgallery",data:({gid:gid,pid:pid}),success:function(data) { nowon = gid; 
var dt = data, he = 20+Math.ceil((dt.split('<div class="pimgbox">').length-1)/5)*180+dt.split('<div class="loadmoreimg">').length*20; $(".pimglist").animate({"height":he+"px"},200,"easeOutExpo",function() { $(".pimglist").html('<div style="display:block;font-size:10pt;padding:5px;">קישור לגלריה זו: <a href="'+thispg+'" target="_blank">'+thispg+'</a></div>'+data); 
$(".pimgname").each(function() {
if($(this).text().length > 15) { $(this).text($(this).text().substring(0,15)+".."); } 
}); }); }
});
});
$("body").on("click",".loadmoreimg",function() { var gid = nowon; lpage++;
$.ajax({type:"POST",url:"$url/action.php?do=loadpgallery",data:({pid:pid,gid:gid,lpage:lpage}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200); $(".loadmoreimg").remove();
var dt = data, he = 20+Math.ceil((dt.split('<div class="pimgbox">').length-1+$(".pimgbox").length)/5)*180+dt.split('<div class="loadmoreimg">').length*20; $(".pimglist").animate({"height":he+"px"},200,"easeOutExpo",function() { $(".pimglist").append(data); 
$(".pimgname").each(function() { if($(this).text().length > 15) { $(this).text($(this).text().substring(0,15)+".."); } }); 
}); }
});
});
$(window).scroll(function() {
if($(window).scrollTop() == $(document).height() - $(window).height()) { $(".loadmoreimg").trigger("click"); }
});
$(".prophoto").hover(function() { 
$(".ephoto").show("slide",{direction:"down",easing:"easeOutExpo"},300);
}).on("mouseleave",function() {
$(".ephoto").hide("slide",{direction:"down",easing:"easeOutExpo"},300);
}); 

$jsedit

function dohash() { var hashtag = window.location.hash.substring(1); $("div[data-gid='"+hashtag+"']").trigger("click"); }
$(window).on('hashchange',dohash);
if(window.location.hash) { dohash(); }

});
</script>
<div style="width:900px;margin:auto;">
<div style="float:right;margin-left:10px;"><div style="background:url($img) white no-repeat;background-size:cover;width:128px;height:128px;position:relative;" class="prophoto">$editphoto</div></div>
<div class="page" style="width:757px;float:right;margin:0;height:128px;"><div class="title">$uname $pmess</div>  <div style="padding:9px;">
תאריך הצטרפות: $date. <BR> קבוצה: {$g[$group]}. <BR> מספר תמונות: $imgcount. 
</div></div><div style="clear:both;"></div>
Print;
echo '<div class="page pgalist">';
$q79 = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$id' AND `private`='0' ORDER BY `id` DESC LIMIT 1"); 
if($q79->num_rows == 0) { echo 'אין תמונות למשתמש זה'; }else{ $fom = $q79->fetch_assoc();  $img = $url.'/i/'.$fom['fname'];
echo <<<Print
<div class="out"><div class="gallerybox" data-gid="0" style="background:url($img) no-repeat;background-size:cover;background-position:center;"><div class="galleryname">כל התמונות</div></div></div>
Print;
$q78 = $mysqli->query("SELECT * FROM `up2_gallery` WHERE `user`='$id'"); 
while($ga = $q78->fetch_assoc()) { $gid = $ga['id']; $catl = ','.$gid; $catm = ','.$gid.','; $catr = $gid.',';
$iminga = $mysqli->query("SELECT * FROM `up2_images` WHERE (`cat` LIKE '%$catl' OR `cat` LIKE '$catr%' OR `cat` LIKE '%$catm%' OR `cat`='$gid') AND `private`='0' ORDER BY `id` DESC LIMIT 1")->num_rows;
if($iminga > 0) {
$gname = $ga['name']; $fim = $mysqli->query("SELECT * FROM `up2_images` WHERE `cat`='$gid' AND `private`='0' ORDER BY `id` DESC LIMIT 1")->fetch_assoc(); $img = $url.'/i/'.$fim['fname'];
echo <<<Print
<div class="out"><div class="gallerybox" data-gid="$gid" style="background:url($img) no-repeat;background-size:cover;background-position:center;position:absolute;"></div><div class="galleryname">$gname</div></div>
Print;
} } }
echo '<div style="clear:both;"></div></div><div class="page pimglist"></div></div>';

}
}else{
if($do == contact) {
echo <<<Print
<script type="text/javascript">
$(document).ready(function() { abheight(0,0);
$("#send").click(function(eventObject) {
eventObject.preventDefault();
var name = $("#name").val(),mail = $("#mail").val(),sub = $("#sub").val(),mess = $("#mess").val(), cap = $("#cap").val();
$.ajax({type:"POST",url:"$url/action.php?do=contact",data:({name:name,mail:mail,sub:sub,mess:mess,cap:cap}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == "e1") {
shower("השם שהוקש קצר/ארוך מדי.");
}else{
if(data == "e2") {
shower("המייל שהוקש לא תקין.");
}else{
if(data == "e3") {
shower("הנושא שהוקש קצר/ארוך מדי.");
}else{
if(data == "e4") {
shower("ההודעה שהוקשה קצרה/ארוכה מדי.");
}else{
if(data == "e5") {
shower("אימות האבטחה נכשל. נסה שנית.");
}else{
shownext();
} } } } } }
});
});
});
</script>
<div class="page"><div class="title">צור קשר</div><div class="abbox">
<div class="afterab">ההודעה נשלחה בהצלחה!</div>
<div class="ab"><div class="er"><div class="error"></div></div>
<form action='' method="POST">
<table>
<tr><td>שם:</td><td><input type="text" id="name" value="$myuser"></td></tr>
<tr><td>מייל:</td><td><input type="text" id="mail" value="$mymail"></td></tr>
<tr><td>נושא:</td><td><input type="text" id="sub"></td></tr>
<tr><td>הודעה:</td><td><textarea cols="60" rows="5" id="mess"></textarea></td></tr>
</table><BR>אימות אבטחה:<BR>
<div style="width:350px;float:right;">
<div style="float:right;height:58px;"><img style="background:white;padding:5px;border-radius:3px;" src="$url/captcha.jpg" id="capimg"></div>
<div style="float:right;"><input type="text" id="cap" maxlength="3" style="width:50px;background:white;margin:12px 15px 0 0;text-align:center;border-radius:7px;" title="הכנס את המספר המופיע בצד ימין (בספרות)" class="dotitle"></div><div style="clear:both;"></div>
</div><div style="clear:both;"></div>
<input type="submit" value="שלח" id="send">
</form>
</div></div>
Print;
}else{
if($do == rules) {
echo <<<Print
<div class="page"><div class="title">&#1514;&#1511;&#1504;&#1493;&#1503; &#1492;&#1488;&#1514;&#1512;</div>
<div style="padding:5px;">$rules</div></div>
Print;
}else{
if($do == settings && $canlogin == 1) {
echo <<<Print
<script type="text/javascript">
$(document).ready(function() { abheight(0,0);
var pri = "$mypri";
$("#change").click(function(eventObject) {
eventObject.preventDefault();
var pw = $("#currpw").val(), newpw = $("#newpw").val(), newmail = $("#newmail").val();
$.ajax({type:"POST",url:"$url/action.php?do=set",data:({pw:pw,newpw:newpw,newmail:newmail,pri:pri}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == "e1") {
shower("הסיסמא הנוכחית לא נכונה!");
}else{
if(data == "e2") {
shower("הסיסמא החדשה שהוקשה קצרה/ארוכה מדי!");
}else{
if(data == "e3") {
shower("המייל שהוקש לא תקין!");
}else{
if(data == "e4") {
shower("מייל זה קיים כבר במערכת!");
}else{
shownext();
} } } } }
});
});
var anim = 200;
$("body").on("click",".exp",function(event) { event.stopImmediatePropagation();
if($(this).css("background-color") != 'rgb(190, 237, 161)') {
$(this).children().animate({"margin-right":"-5px"},anim); $(this).animate({"backgroundColor":"#BEEDA1"},anim); pri = 1;
}else{
$(this).children().animate({"margin-right":"10px"},anim); $(this).animate({"backgroundColor":"white"},anim); pri = 0;
}
anim = 200;
});
if(pri == "0") { anim = 0; $(".exp").trigger("click"); }
});
</script>
<div class="page">
<div class="title">הגדרות משתמש</div><div class="abbox">
<div class="afterab">ההגדרות שונו בהצלחה!</div>
<div class="ab"><div class="er"><div class="error"></div></div>
<form action='' method="POST">
<table>
<tr><td>סיסמא נוכחית:</td><td><input type="password" id="currpw"></td></tr>
<tr><td>תאריך הרשמה:</td><td><input type="text" value="$mydate" disabled></td></tr>
<tr><td>סיסמא חדשה (השאר ריק כדי לא לשנות):</td><td><input type="password" id="newpw"></td></tr>
<tr><td>מייל חדש:</td><td><input type="text" value="$mymail" id="newmail"></td></tr>
</table><BR>
מצב חשבון פרטי: <div class="radio exp"><div class="ring"></div></div> <div style="font-size:10pt;">במצב חשבון פרטי יהיה ניתן לצפות בתמונותיך רק עם קישור לתמונה ספציפית. <BR>
לא יהיה ניתן לצפות בכל תמונותיך בפרופיל המשתמש. </div>
<BR><input type="submit" id="change" value="בצע שינויים">
</form>
</div></div>
Print;
}else{
if($do == myfiles) {
if($canlogin != 1) {
echo '<div class="page"><div class="title">אינך מחובר</div>
<div style="padding:10px 10px 10px 10px;">רק משתמשים רשומים רשאים לנהל את הקבצים שלהם.</div></div>';
}else{
$q52 = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$myid'"); $n52 = ceil($q52->num_rows/5);
echo <<<Print
<div class="title" style="display:none;">ניהול קבצים</div>
<script type="text/javascript">
$(document).ready(function() {
var selected = [], copied = [], nowat = "allimg", mgid = 0, fgid = 0, pid = 0, page = 1, editga = 0, ss = [], henofl = parseInt($(".page").css("height").slice(0,-2));
$(".page").css("height",parseInt($(".filelist").css("height").slice(0,-2)-5)+parseInt($(".page").css("height").slice(0,-2))+"px");
function loadga(loadgid) { page = 1; $(".filebox,.filelist div").fadeOut(200); $(".load").remove(); $.ajax({type:"POST",url:"$url/action.php?do=loadga",data:({gid:loadgid}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
var dt = data, he = 10+henofl+Math.ceil((dt.split('<div class="filebox"').length-1)/5)*190+(dt.split('<div class="load"').length-1)*15+(dt.split('<div style="padding:10px;">').length-1)*50;  $(".page").animate({"height":he+"px"},200,"easeOutExpo",function() { $(".filelist").html(dt); $(".filebox").show("drop",{direction:"up"},500); $("#firem").show(); }); 
if(loadgid == 'allimg') { $("#firem").hide(); }  } });
}
$(".filelist").selectable({filter:".filebox",cancel: ".cancelfname,input,.load,.gachange", selected: function() { selected = [];
$(".ui-selected", this).each(function() { var index = $(".filebox").index(this), tid = $(".filebox").eq(index).attr("id").slice(1);
selected.push(tid); }); }, unselected: function(event,ui) { var uns = ui.unselected.id.slice(1); for(var i = 0; i < selected.length; i++) { if(selected[i] == uns) { selected.splice(i,1); } } }
});
$('body:not(".filebox")').click(function() { $(".filebox").removeClass('ui-selected'); selected = []; $(".delmenu,.filemenu,.pagemenu").fadeOut(200); });
$("#ficopy").click(function() { if(selected.length == 0) { copied = [fgid]; }else{ copied = selected.slice(0); } $(".fipaste").show(); });
$(".fipaste").click(function() { $.ajax({type:"POST",url:"$url/action.php?do=pastef",data:({copied:copied,nowat:nowat}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == "e1") { alert("לא ניתן להדביק תמונה זו פה."); }else{ loadga(nowat); $(".fipaste").hide(); } } });
});
$("#fidel").click(function() { if(confirm("למחוק קבצים שנבחרו לצמיתות?")) { if(selected.length == 0) { selected = [fgid]; } ss = selected; $.ajax({type:"POST",url:"$url/action.php?do=delfile",data:({fileid:selected}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200); for(var i = 0; i < ss.length; i++) { $("#f" + ss[i]).hide(200); } } }); }
});
$("#fipri").click(function() { if(selected.length == 0) { selected = [fgid]; } ss = selected; $.ajax({type:"POST",url:"$url/action.php?do=privatefile",data:({fileid:selected}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200); for(var i = 0; i < ss.length; i++) { if($("#f" + ss[i] + " .icon").length == 0) { $("#f" + ss[i] + " .filename").before('<div class="icon" style="font-size:8pt;display:inline-block;">&#xf023;</div>'); }else{ $("#f" + ss[i] + " .icon").remove(); } } } }); 
});
$("body").on("click",".folderbox",function() { if(editga == 0) { var gid = $(this).attr("id").slice(2); nowat = gid;
$.ajax({type:"POST",url:"$url/action.php?do=numimg",data:({gid:nowat}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200); pages = data; $(".load").hide();
if(pages > 1) { setTimeout(function() {  $(".load").attr("id","lo"+gid).show(); page = 1;  },500); } } });
loadga(gid); }
});
$("body").on("contextmenu",".folderbox",function(e) { e.preventDefault(); $(".filemenu,.pagemenu").hide(); mgid = $(this).attr("id").slice(2); if(mgid != "allimg") { var dleft = e.pageX, dtop = e.pageY; $(".delmenu").css({"left":dleft-180+"px","top":dtop+"px"}).show("puff",{easing:"easeOutExpo"},700); } });
$("body").on("contextmenu",".filebox",function(e) { e.preventDefault(); $(".foldermenu,.pagemenu").hide(); fgid = $(this).attr("id").slice(1); var fleft = e.pageX, ftop = e.pageY; $(".filemenu").css({"left":fleft-150+"px","top":ftop+"px"}).show("puff",{easing:"easeOutExpo"},700); });
$("body").on("contextmenu",".page,.pastehere,.pastehere span",function(e) { e.preventDefault(); if (e.target !== this || copied.length == 0) { return; }  $(".filemenu,.foldermenu").hide(); var fleft = e.pageX, ftop = e.pageY; $(".pagemenu").css({"left":fleft-40+"px","top":ftop+"px"}).show("puff",{easing:"easeOutExpo"},400); });
$("#gonly,#gnimg").click(function() { if(mgid != 0) { var dimg = 0, fullg = "ga"+mgid; if($(this).attr("id") == "gnimg") { dimg = 1; if(!confirm("למחוק קבצים בגלריה זו לצמיתות?")) { return false; } } 
$.ajax({type:"POST",url:"$url/action.php?do=gdel",data:({gid:mgid,dimg:dimg}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
$("#ga"+mgid).hide(500,"easeOutExpo",function() { $(this).remove(); }); if(dimg == 1) { loadga("allimg"); }
} }); } });
function newname(newn) { $.ajax({type:"POST",url:"$url/action.php?do=rename",data:({newname:newn,imgid:fgid}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == 'e1') { alert("השם קצר/ארוך מדי."); }else{ $("#f"+fgid+" .filename").text(newn); } } });
}
$("body").on("keypress",".newname",function(e) { if(e.which == 13) { newname($(this).val()); } });
$("body").on("click","#fichange",function() { newname($(this).parent().find(".newname").val()); });
$("body").on("click",".cancelfname",function() { $("#f"+fgid+" .filename").text(nownms);  });
var nownms = '';
$("#finame").click(function() { var nowname = $("#f"+fgid+" .filename").text(); nownms = nowname;
$("#f"+fgid+" .filename").html('<input type="text" class="newname" value="'+nowname+'" style="width:50px;height:10px;font-size:8pt;" autofocus><input type="submit" value="שנה" id="fichange">  <a href="javascript:void(0);" style="font-size:8pt;color:red;" class="cancelfname">בטל</a>');  $(".filebox").css("cursor","default");
});
$("body").on("click","#anew",function(eventObject) { eventObject.preventDefault(); var ganame = $("#ganame").val();
$.ajax({type:"POST",url:"$url/action.php?do=newga",data:({ganame:ganame}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == 'e1') { alert("השם שהוקש קצר/ארוך מידי!"); }else{ $(".foldertemp").remove(); ganame = ganame.replace("<", "&#60;");
$(".folderbox").last().after('<div class="folderbox" id="ga'+data+'"><div class="folderimg"><div class="icon" style="font-size:50pt;">&#xf07b;</div></div><div class="foldername">'+ganame+'</div></div>');
} } });
});
$(".bg").css("opacity","0.7");
$("#filink").click(function() { var win=window.open('$url/links/'+$("#f"+fgid).attr("data-page"), '_blank'); win.focus(); });
$("#firotate").click(function() { var img = $("#f"+fgid).find("img").attr("src"); $(".wcontent").html('<div class="imgrotate" style="transform:rotate(-90deg);"><img src="'+img+'" style="max-width:160px;max-height:160px;"></div><div class="imgrotate" style="transform:rotate(180deg);"><img src="'+img+'" style="max-width:160px;max-height:160px;"></div><div class="imgrotate" style="transform:rotate(-270deg);"><img src="'+img+'" style="max-width:160px;max-height:160px;"></div><div style="clear:both;"></div>'); $(".window").show("drop",{direction:"up",easing:"easeOutExpo"},700); $(".bg").fadeIn(700);  });
$("body").on("click",".wcontent .imgrotate",function() { var deg = $(this).index(); 
$.ajax({type:"POST",url:"$url/action.php?do=rotate",data:({deg:deg,fgid:fgid}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200); var d = new Date();
$("#f"+fgid+" img").attr("src",($("#f"+fgid).find("img").attr("src"))+"?"+d.getTime()); $(".close").trigger("click"); }
});
});
$(".close,.bg").click(function() { $(".window").hide("drop",{direction:"up",easing:"easeOutExpo"},700); $(".bg").fadeOut(700); });
$("#glink").click(function() { var win=window.open('$url/profile/$myid#'+mgid, '_blank'); win.focus(); });
function galname(newn) { $.ajax({type:"POST",url:"$url/action.php?do=regal",data:({newname:newn,galid:mgid}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == 'e1') { alert("השם שהוקש קצר/ארוך מדי."); }else{ $("#ga"+mgid+" .foldername").text(newn); editga = 0; } } }); 
}
var nownames = '';
$("body").on("keypress",".galname",function(e) { if(e.which == 13) { galname($(this).val()); } });
$("body").on("click",".gachange",function() { galname($(this).parent().find(".galname").val()); });
$("body").on("click",".cancelgname",function() { $("#ga"+mgid+" .foldername").html(nownames); editga = 0; });
$("body").on("click","#gname",function() { editga = 1; var nowname = $("#ga"+mgid+" .foldername").text(); nownames = nowname; $("#ga"+mgid+" .foldername").html('<input type="text" class="galname" value="'+nowname+'" style="width:50px;height:10px;font-size:8pt;"><input type="submit" value="שנה" class="gachange" style="padding:2px 3px;margin:5px;font-size:8pt;"> <a href="javascript:void(0);" style="font-size:8pt;color:red;" class="cancelgname">בטל</a>'); $(".galname").focus();
});
$("#firem").click(function() { if(selected.length == 0) { selected = [fgid]; } ss = selected; if(nowat != "allimg") {
$.ajax({type:"POST",url:"$url/action.php?do=remove",data:({gid:selected,nowat:nowat}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == "e1") { alert("לא ניתן לבצע פעולה זו."); }else{ for(var i = 0; i < ss.length; i++) { $("#f" + ss[i]).hide(200); } } } });
}else{ alert("מחק את התמונה מהגלריה."); }
});
$("body").on("click",".load",function() { $(".load").remove(); $.ajax({type:"POST",url:"$url/action.php?do=limg",data:({page:page,cat:nowat}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200); if(data == 'e1') { alert("לא ניתן לטעון עוד."); }else{
var dt = data, he = parseInt($(".page").css("height").slice(0,-2))+Math.ceil((dt.split('<div class="filebox"').length-1)/5)*190;  $(".page").animate({"height":he+"px"},200,"easeOutExpo",function() { $(".filelist").append(data); $(".filebox").show("drop",{direction:"up"},500); });
page++;
} } });
});
$(window).scroll(function() { if($(window).scrollTop() == $(document).height() - $(window).height()) { $(".load").trigger("click"); } });
$(".addga").click(function() { if($(".foldertemp").length == 0) {
$(".folderbox").last().after('<div class="foldertemp" style="display:none;"><form action="" method="POST"><div class="folderimg"><div class="icon" style="font-size:50pt;">&#xf07b;</div></div><input type="text" id="ganame" style="width:50px;height:10px;font-size:8pt;" placeholder="שם הגלריה.."><input type="submit" value="הוסף" id="anew"> <a href="javascript:void(0);" style="font-size:8pt;color:red;" class="cancelfolder">בטל</a></form></div>');  $("#ganame").focus();
$(".foldertemp").show(500,"easeOutExpo"); if(($(".folderbox").length+1)%6==0) { $(".page").animate({"height":"+=120px"},500); } }
});
$("body").on("click",".cancelfolder",function() { $(".foldertemp").hide(500,"easeOutExpo",function() { $(this).remove(); }); if(($(".folderbox").length+1)%6==0) { $(".page").animate({"height":"-=120px"},500); } });

});
</script>
<div style="clear:both;"></div>
<ul class="delmenu" style="display:none;">
<a href="javascript:void(0);" id="gname"><li>שנה שם לגלריה</li></a>
<a href="javascript:void(0);" id="gonly"><li>מחק גלריה</li></a>
<a href="javascript:void(0);" id="gnimg"><li>מחק גלריה ואת כל התמונות שבה</li></a>
<a href="javascript:void(0);" id="glink"><li>קישור לגלריה</li></a>
</ul>
<ul class="filemenu" style="display:none;width:141px;">
<a href="javascript:void(0);" id="ficopy"><li>העתק</li></a>
<a href="javascript:void(0);" class="fipaste" style="display:none;"><li>הדבק</li></a>
<a href="javascript:void(0);" id="firotate"><li>סובב</li></a>
<a href="javascript:void(0);" id="finame"><li>שנה שם</li></a>
<a href="javascript:void(0);" id="firem" style="display:none;"><li>הסר את התמונות מהגלריה</li></a>
<a href="javascript:void(0);" id="fidel"><li>מחק קבצים</li></a>
<a href="javascript:void(0);" id="filink"><li>קישור לקובץ</li></a>
<a href="javascript:void(0);" id="fipri"><li>הפוך/הסר מצב פרטי</li></a>
</ul>
<ul class="pagemenu" style="display:none;">
<a href="javascript:void(0);" class="fipaste"><li>הדבק</li></a>
</ul>
<div class="window"><div class="icon close" title="סגור חלון">&#xf00d;</div><div class="windowtitle">סובב תמונה</div><div style="font-size:10pt;">לחץ על התמונה כדי לבצע לה סיבוב. <BR> שים לב! סיבוב תמונה מונפשת יגרום לשיבושה. נתמך רק בפורמטים: jpg (jpeg), png, gif בלבד!</div><div class="wcontent"></div></div>
<div class="page">
<div class="addga"><div class="folderimg"><div class="icon" style="font-size:50pt;">&#xf0fe;</div></div>הוסף גלריה</div>
<div class="folderbox" id="gaallimg"><div class="folderimg"><div class="icon" style="font-size:50pt;">&#xf07b;</div></div>כל התמונות</div>
Print;
$q9 = $mysqli->query("SELECT * FROM `up2_gallery` WHERE `user`='$myid' ORDER BY `id` ASC");
if($q9->num_rows > 0) {
while($c = $q9->fetch_assoc()) { $gid = $c['id']; $gname = $c['name'];
echo '<div class="folderbox" id="ga'.$gid.'"><div class="folderimg"><div class="icon" style="font-size:50pt;">&#xf07b;</div></div><div class="foldername">'.$gname.'</div></div>';
} }
echo '<div style="clear:both;"></div><div class="filelist">';
$q5 = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$myid' ORDER BY `id` DESC LIMIT 50");
if($q5->num_rows == 0) { 
echo '<div style="padding:10px;">אין לך תמונות  במשתמש זה.</div>';
}else{
while($t = $q5->fetch_assoc()) { $fid = $t['id']; $finame = htmlspecialchars($t['name']);  $fpage = $t['page']; $fpri = $t['private'];  $fname = $t['fname']; $fisize = round($t['size']/(1024*1024),3); $mdate = $t['date'];
$phpdate = strtotime($mdate); $fidate = date('j.n.y - H:i', $phpdate); if(mb_strlen($finame,'UTF-8') > 30) { $finame = mb_substr($finame,0,30,'UTF-8').'..'; }
$lock = ''; if($fpri == 1) { $lock = '<div class="icon" style="font-size:8pt;display:inline-block;">&#xf023;</div>';  }
echo <<<Print
<div class="filebox" id="f$fid" data-page="$fpage"><div class="fileimg"><img src="$url/i/$fname"></div>$lock<div class="filename">$finame</div></div>
Print;
} }
echo '<div style="clear:both;"></div>';
if($q52->num_rows > 50) { echo '<div class="load" id="loallimg" style="background:#E0EDF6;">טען עוד תמונות</div>'; }
echo '<div style="clear:both;"></div></div></div>';
}
}else{
if($do == active) {
$id = $mysqli->real_escape_string($_GET['id']);
echo '<div class="page"><div class="title">אישור משתמש</div><div style="padding:10px;">';
$q2 = $mysqli->query("SELECT * FROM `up2_users` WHERE `group`='$id'");
if($q2->num_rows == 0) {
echo <<<Print
<b>המשתמש לא אושר!</b><BR>
בדוק כי הכתובת שנכנסת היא כפי שנשלחה למייל, וכי המשתמש שלך לא אושר בעבר.
Print;
}else{
$mysqli->query("UPDATE `up2_users` SET `group`='1' WHERE `group`='$id'");
echo <<<Print
<b>המשתמש אושר!</b><BR>
תהליך אישור המשתמש בוצע בהצלחה! <BR>
אתה רשאי להתחבר לאתר.
Print;
}
echo '</div></div>';
}else{
if($do == register && $canlogin != 1) {
echo <<<Print
<script type="text/javascript">
$(document).ready(function() {
$("#register").click(function(eventObject) {
eventObject.preventDefault();
var user = $("#username").val(), pass = $("#password").val(), mail = $("#mail").val(), pr = $("#private").is(':checked'), cap = $("#cap").val();
$.ajax({type:"POST",url:"$url/action.php?do=register",data:({username:user,password:pass,mail:mail,pr:pr,cap:cap}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == "e1") {
shower("שם המשתמש קצר/ארוך מדי.");
}else{
if(data == "e2") {
shower("שם המשתמש לא יכול להכיל רווחים ותווים מיוחדים.");
}else{
if(data == "e3") {
shower("קיים כבר במערכת שם משתמש כזה, בחר אחר.");
}else{
if(data == "e4") {
shower("סיסמא קצרה/ארוכה מדי.");
}else{
if(data == "e5") {
shower("מייל לא תקין.");
}else{
if(data == "e6") {
shower("מייל כזה קיים כבר מערכת.");
}else{
if(data == "e7") {
shower("אימות האבטחה נכשל. נסה שנית.");
}else{
shownext();
} } } } } } } }
});
});
abheight(0,0);
});
</script>
<div class="page"><div class="title">הרשמה לאתר</div><div class="abbox">
<div class="afterab">
<b>נרשמת בהצלחה!</b><BR>
ההרשמה לאתר בוצעה בהצלחה! <BR>
בדוק את המייל שלך לאישור המשתמש.
</div>
<div class="ab">
<div class="er"><div class="error"></div></div>
<form action='' method="POST">
<table>
<tr><td>שם משתמש:</td><td><input type="text" id="username"></td></tr>
<tr><td>סיסמא:</td><td><input type="password" id="password"></td></tr>
<tr><td>מייל:</td><td><input type="text" id="mail"></td></tr>
<tr><td colspan="2"><label><input type="checkbox" id="private"> חשבון פרטי<div style="font-size:10pt;">במצב חשבון פרטי יהיה ניתן לצפות בתמונותיך רק עם קישור לתמונה ספציפית.<BR> לא יהיה ניתן לצפות בכל תמונותיך בפרופיל המשתמש. <BR> ניתן להחליף אופציה זו בכל עת בהגדרות משתמש.</div></label></td></tr>
</table><BR>אימות אבטחה:<BR>
<div style="width:350px;float:right;">
<div style="float:right;height:58px;"><img style="background:white;padding:5px;border-radius:3px;" src="$url/captcha.jpg" id="capimg"></div>
<div style="float:right;"><input type="text" id="cap" maxlength="3" style="width:50px;background:white;margin:12px 15px 0 0;text-align:center;border-radius:7px;" title="הכנס את המספר המופיע בצד ימין (בספרות)" class="dotitle"></div><div style="clear:both;"></div>
</div><div style="clear:both;"></div>
<input type="submit" value="הירשם" id="register">
</form>
</div></div></div>
Print;
}else{
if($do == links) {
$id = $mysqli->real_escape_string($_GET['id']);
$q1 = $mysqli->query("SELECT * FROM `up2_images` WHERE `page`='$id' AND `private`='0'");
if($q1->num_rows == 0) {
echo <<<Print
<div class="page">
<div class="title">תמונה לא נמצאה</div>
<div style="padding:10px 10px 10px 10px;">
מצטערים, התמונה לא קיימת. <BR>
רוב הסיכויים שהמשתמש מחק אותה או שהיא נמחקה עקב עבירה על חוקי האתר. 
</div></div>
Print;
}else{
while($d = $q1->fetch_assoc()) { $name = htmlspecialchars($d['name']); $fname = $d['fname']; $size = $d['size']; $date = $d['date']; $user = $d['user'];
if($user != '0') { $q2 = $mysqli->query("SELECT * FROM `up2_users` WHERE `id`='$user'"); $fw = $q2->fetch_assoc(); $uid = $fw['id']; $uname = $fw['username']; $by = '<a href="'.$url.'/profile/'.$uid.'">'.$uname.'</a>'; }else{ $by = '<i>אורח</i>'; }
$size = round($size/(1024*1000),3); $phpdate = strtotime($date);
$date = date('j.n.y - H:i', $phpdate);
echo <<<Print
<div style="width:900px;margin:auto;text-align:center;"><a href="$url/i/$fname"><img src="$url/i/$fname" style="max-width:900px;"></a></div>
<div class="page" style="margin:5px auto;">
<div style="padding:10px 10px 10px 10px;">
<div style="text-align:center;"><div class="title" style="display:none;">$name</div>
<div style="float:right;">$name</div> <div style="float:left;direction:ltr;">$size MB | $date</div> <div style="clear:both;"></div>
<div style="float:right;font-size:10pt;">- ע"י $by.</div>  <div style="clear:both;"></div>
</div></div></div><div class="page" style="margin:8px auto;"><div style="padding:10px;"><b>תמונות נוספות</b><BR>
Print;
$q4 = $mysqli->query("SELECT * FROM `up2_images` WHERE `private`='0' order by RAND() LIMIT 5");
while($rq = $q4->fetch_assoc()) { $page = $rq['page']; $fname = $rq['fname']; $img = $url.'/i/'.$fname; $name = htmlspecialchars($rq['name']); 
if(mb_strlen($name,'UTF-8') > 15) { $name = mb_substr($name,0,15,'UTF-8').'..'; }
echo <<<Print
<div class="pimgbox"><a href="$url/links/$page"><div class="pimage" style="background:url($img) no-repeat;background-size:cover;background-position:center;"></div><div class="pimgname">$name</div></a></div>
Print;
}
echo <<<Print
<div style="clear:both;"></div></div></div><div class="page" style="margin-top:8px;"><div style="padding:10px;">
Print;
if($canlogin == 1) {
if($mytype == 2) { $delcom = '$("body").on("click",".delcom",function() { var comid = $(this).parent().attr("data-comid"); $.ajax({type:"POST",url:"'.$url.'/action.php?do=delcom",data:({comid:comid}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200); $("#com"+comid).hide(300); $(".allcom").animate({"height":"-=57"},200); } }); });'; }
echo <<<Print
<script type="text/javascript">
$(document).ready(function() {
var img = "$id",lpage = 0;
$delcom
$("#add").click(function(eventObject) { eventObject.preventDefault();
var content = $("#content").val();
$.ajax({type:"POST",url:"$url/action.php?do=addcom",data:({content:content,img:img}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == "e1") {
shower("תוכן ההודעה קצר/ארוך מדי.");
}else{
if(data == "e2") {
shower("לא ניתן להגיב לתמונה זו.");
}else{
if(data == "e3") {
shower("לא ניתן להגיב יותר מ 5 פעמים ברציפות.");
}else{
if(data == "e4") {
shower("לא ניתן לתייג יותר מ-10 אנשים בהודעה אחת.");
}else{
$(".nocom").remove(); $("#content").val(""); $(".error").hide(); $(".allcom").prepend(data); $(".newcb").slideDown(300);
} } } } }
});
});
$("body").on("click",".openoptions",function(event) { event.stopPropagation(); $(".options").hide(); var clickid = $(".openoptions").index(this);
$(".options").eq(clickid).show(500,"easeOutExpo");
});
$("body").click(function() { $(".options").hide(200); });
$("body").on("click",".loadmorecom",function() { lpage++;
$.ajax({type:"POST",url:"$url/action.php?do=loadcom",data:({img:img,lpage:lpage}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200); $(".loadmorecom").remove();
var dt = data, he = (dt.split('<div class="commentbox"').length-1+$(".commentbox").length)*57+(dt.split('<div class="loadmorecom">').length-1)*20; 
$(".allcom").animate({"height":he+"px"},200,"easeOutExpo",function() { $(".allcom").append(data); }); }
});
});
$(window).scroll(function() {
if($(window).scrollTop() == $(document).height() - $(window).height()) { $(".loadmorecom").trigger("click"); }
});
$(".bg").css("opacity","0.7");
$(".close,.bg").click(function() { $(".window").hide("drop",{direction:"up",easing:"easeOutExpo"},700); $(".bg").fadeOut(700); });
var cid = 0;
$("body").on("click",".reportcom",function() { $(".reportform").show(); $(".reportsent").hide(); cid = $(this).parent().attr("data-comid"); var cbox = $("#com"+cid); $(".quote").text(cbox.find(".ctext").text()+" ~ "+cbox.find(".cname a").text().slice(0,-1)); $(".window").show("drop",{direction:"up",easing:"easeOutExpo"},700); $(".bg").fadeIn(700); });
$("#sendreport").click(function(eventObject) { eventObject.preventDefault(); var reason = $("#reason").val(),cap = $("#cap").val();
$.ajax({type:"POST",url:"$url/action.php?do=report",data:({cid:cid,reason:reason,cap:cap}),beforeSend:function() { $(".loadbar").show("slide",{direction:"up",easing:"easeOutExpo"},200); }, success:function(data) { $(".loadbar").hide("slide",{direction:"up",easing:"easeOutExpo"},200);
if(data == 'e1') {
$(".reporterror").html("אורך סיבת הדיווח קצר/ארוך מדי.").slideDown(200).effect("shake",500);
}else{
if(data == 'e2') {
$(".reporterror").html("לא קיימת תגובה כזו. כנראה נמחקה כבר.").slideDown(200).effect("shake",500);
}else{
if(data == 'e3') {
$(".reporterror").html("האימות נכשל. נסה שוב.").slideDown(200).effect("shake",500);
}else{ $(".reporterror").slideUp(200); $("#reason,#cap").val("");
$(".reportform").slideUp(500,function() { $(".reportsent").slideDown(300); setTimeout(function() { $(".close").trigger("click"); },2000); });
} } } }
});
});
$("body").on("click",".tag",function() { var nameposter = $(this).parent().parent().parent().find(".cname").text().slice(0, -1);
$("#content").focus().val($("#content").val() + "#" + nameposter);
});
});
</script>
<div class="window"><div class="icon close" title="סגור חלון">&#xf00d;</div><div class="windowtitle">דווח על תגובה</div><div class="reportform"><div class="reporterror"></div><div class="quote"></div><form action='' method="POST"><table><tr><td>סיבת הדיווח:</td><td><input type="text" id="reason"></td></tr></table><div style="width:350px;float:right;">
<div style="float:right;height:58px;"><img style="background:white;padding:5px;border-radius:3px;" src="$url/captcha.jpg" id="capimg"></div>
<div style="float:right;"><input type="text" id="cap" maxlength="3" style="width:50px;background:white;margin:12px 15px 0 0;text-align:center;border-radius:7px;" title="הכנס את המספר המופיע בצד ימין (בספרות)" class="dotitle"></div><div style="clear:both;"></div>
</div><div style="clear:both;"></div><input type="submit" value="שלח דיווח" id="sendreport" style="margin-top:15px;"></form></div><div class="reportsent">הדיווח נשלח בהצלחה!</div></div>
<div class="er"><div class="error"></div></div><b>הוסף תגובה</b><BR>
<form action='' method="POST">הודעה: <input type="text" id="content" style="width:730px;" autocomplete="off"><input type="submit" value="הוסף" id="add"></form><BR>
<div class="allcom">
Print;
}
$q3 = $mysqli->query("SELECT * FROM `up2_comments` WHERE `img`='$id' ORDER BY `id` DESC LIMIT 20");
if($mytype == 2) { $adminoptions = '<li class="delcom"><div class="icon" style="font-size:8pt;">&#xf1f8;</div> מחק תגובה</li>'; }
if($canlogin == 1) { $options = '<div class="icon tag" title="השב להודעה" style="font-size:9pt;cursor:pointer;">&#xf112;</div> <div class="icon openoptions" title="אפשרויות" style="font-size:9pt;cursor:pointer;">&#xf0d7;</div>'; }
if($q3->num_rows == 0) { echo '<div class="nocom">אין תגובות</div>'; }
while($cm = $q3->fetch_assoc()) { $comid = $cm['id']; $user = $cm['user']; $date = $cm['date']; $content = $cm['content'];
$q4 = $mysqli->query("SELECT * FROM `up2_users` WHERE `id`='$user'"); $fw = $q4->fetch_assoc(); $uname = $fw['username']; $img = $url.'/i/'.$fw['img']; if($fw['img'] == '') { $img = $url.'/images/noimg.png'; }
echo <<<Print
<div class="commentbox" id="com$comid"><div style="float:right;padding:5px;"><img src="$img" style="width:32px;height:32px;" alt="טוען.."></div><div style="float:right;width:calc(100% - 42px);"><div class="cname"><a href="$url/profile/$user">$uname:</a></div><div class="cdate">$date $options
<ul class="options" data-comid="$comid"><li class="reportcom"><div class="icon" style="font-size:8pt;">&#xf071;</div> דווח</li>$adminoptions</ul></div>
<div style="clear:both;"></div><div class="ctext">$content</div></div><div style="clear:both;"></div></div>
Print;
}
if($mysqli->query("SELECT * FROM `up2_comments` WHERE `img`='$id' ORDER BY `id` DESC")->num_rows > 20) { echo '<div class="loadmorecom"><div style="clear:both;"></div>טען עוד תגובות</div>'; }

echo '</div></div></div>';
} }
}else{
$blockheight = (ceil($maxfiles/5)*100)."px"; $upnext = (ceil($maxfiles/5)*100-(30*ceil($maxfiles/5)))."px"; $padding = (30*(ceil($maxfiles/5)))."px"; $toph = ($blockheight/2)."px";
echo <<<Print
<script type="text/javascript">
$(document).ready(function() {
var canload = false;
$("body").on("click","#cancelup",function() {
$(".flist").html(""); $(".droptext").animate({"font-size":"20pt"},200); $(".uprule").animate({"font-size":"10pt"},200); $("#file").show(); $(".upnext").fadeOut(200).animate({"font-size":"0pt"},200).html(""); $(".whiteupload").hide(200);
});
$("#file").change(function() { var flist = "";
canload = true;  
$("#text").text(""); 
for(var i = 0; (i < this.files.length && canload); i++) { if(i%5==0) { if(i > 0) { flist += "</tr>"; } flist += "<tr>"; }
var finfo = this.files[i], ftype = finfo.type;
$("#text").append('"' + finfo.name + '",');
if(this.files.length > $maxfiles) {
$(".uperror").html("לא ניתן להעלות יותר מ$maxfiles קבצים.").animate({"font-size":"20pt"},200).delay(1500).animate({"font-size":"0pt"},200);
$(".droptext").animate({"font-size":"0pt"},200).delay(1500).animate({"font-size":"20pt"},200); $(".uprule").animate({"font-size":"0pt"},200).delay(1500).animate({"font-size":"10pt"},200);
canload = false;
}else{
if(Math.round(finfo.size/1024)/1000 > $maxsize) {
$(".uperror").html("אחד או יותר מהקבצים חורגים מהגודל המותר.").animate({"font-size":"20pt"},200).delay(1500).animate({"font-size":"0pt"},200);
$(".droptext").animate({"font-size":"0pt"},200).delay(1500).animate({"font-size":"20pt"},200); $(".uprule").animate({"font-size":"0pt"},200).delay(1500).animate({"font-size":"10pt"},200);
canload = false;
}else{ 
if(ftype != "image/jpg" && ftype != "image/jpeg" && ftype != "image/png" && ftype != "image/gif" && ftype != "image/bmp" && ftype != "image/vnd.microsoft.icon") {
$(".uperror").html("יש להעלות תמונות בלבד.").animate({"font-size":"20pt"},200).delay(1500).animate({"font-size":"0pt"},200);
$(".droptext").animate({"font-size":"0pt"},200).delay(1500).animate({"font-size":"20pt"},200); $(".uprule").animate({"font-size":"0pt"},200).delay(1500).animate({"font-size":"10pt"},200);
canload = false;
}else{
var img = URL.createObjectURL(finfo), size = Math.round(finfo.size/1024)/1000, name = finfo.name; if(name.length > 15) { name = name.substring(1,15)+".."; } 
flist += '<td><img src="'+img+'"><div style="font-size:10pt;">'+name+'<BR>'+size+'MB</div></td>';
}
} } } 
if(canload && this.files.length > 0) { $(".flist").html(flist); $(".droptext,.uprule").animate({"font-size":"0pt"},200); $("#file").hide(); $(".upnext").fadeIn(200).append("<div class='bullshit'>נבחרו "+this.files.length+" קבצים<BR><input type='submit' value='העלה' class='whiteupload'> <a href='javascript:void(0);' style='color:white;font-size:10pt;' id='cancelup'>ביטול</a><div style='padding:3px;font-size:8pt;margin:auto;width:155px;'><label><div style='float:right;'><input type='checkbox' name='privateup' value='yes'></div><div style='float:right;margin-top:5px;'>  אל תציג תמונות אלו בפרופיל שלי</div></label></div><div style='clear:both;'></div></div>").animate({"font-size":"20pt"},200); $(".whiteupload").show(200); }
});
function movement() { $(".loadcircle").animate({"margin-top":"-40px"},1500,function() { $(this).animate({"margin-top":"-15px"},1500); }); }
var movem;
$('.fileload').submit(function() { if(!canload) { return false; } movem = setInterval(movement,3000); $(".bullshit").hide();
$(".upnext").append('<div class="loadcircle"><div class="circle"><div class="whitecircle"><div class="percent"></div></div></div></div>'); $(".loadcircle").fadeIn(200); });
var filesize = 0,pre = 0;
$('.fileload').ajaxForm({beforeSend: function() {  movement(); $(".radius").remove(); $('.percent').html("0%");  },
uploadProgress: function(event, position, total, percentComplete) {  filesize = total;
$('.percent').html(percentComplete + '% <div style="font-size:8pt;">'+(Math.round(position/1024)/1000)+"/"+(Math.round(total/1024)/1000)+"<div>MB</div></div>");
for(i = pre; i < percentComplete; i++) { $(".circle").append('<div class="radius dohl" style="transform:rotate('+(i*3.6)+'deg);background:rgb(196,72,'+i+');box-shadow:0 0 2px rgb(196,72,'+i+');display:none;"></div>'); } pre = percentComplete; $(".radius").show(500); $(".dohl").removeClass("dohl").effect("highlight",500);
},complete: function(xhr) { clearInterval(movem); $(".radius").remove(); $(".upnext").fadeOut(400); $(".complete").fadeIn(400);
$(".flist").html(xhr.responseText); $(".icon").tooltip({show:{effect:"drop",direction:"up",duration: 100},hide:{effect:"fade",duration:0},track:false,position:{my:"center bottom-10",at:"center top"} });
} }); 
$("#file").on('dragenter',function(e) { e.stopPropagation(); e.preventDefault();
$(".droptext").animate({"font-size":"30pt"},200);
});
$("#file").on('dragleave',function(e) { e.stopPropagation(); e.preventDefault();
$(".droptext").animate({"font-size":"20pt"},200);
});

});
</script>

<form action="action.php?do=upload" method="post" enctype="multipart/form-data" class="fileload">
<div style="border:2px dashed white; width:745px; height:$blockheight; position:absolute; right:0;left:0;margin:auto;top:calc(50% - $toph); z-index:2;">
<div class="complete"><div class="icon" title="ההעלאה הושלמה">&#xf00c;</div></div>
<div class="droptext upmess" style="padding:$padding 0 0 0;">גרור קבצים או לחץ לבחירה<div style="font-size:10pt;" class="uprule">גודל מקסימלי לתמונה: $maxsize MB. מקסימום $maxfiles תמונות בהעלאה.</div></div><div class="uperror upmess" style="font-size:0pt;color:#964343;padding:$padding 0 0 0;"></div>
<div class="upnext upmess" style="background:rgba(0,0,0,0.6);font-size:0pt;width:745px;height:$upnext;padding:$padding 0 0 0;display:none;"></div>
<div id="filelist" style="padding:5px;"><table class="flist"></table></div>
<input type="file" name="file[]" style=" width:745px; height:$blockheight;opacity:0;padding-top:4px; position:relative; z-index:3; cursor:default;" multiple id="file" accept="image/*"> </div>
</form>
<div style="clear:both;"></div>
Print;
} } } } } } } } } } } }
echo '</div><div style="position:fixed;bottom:10px;right:10px;color:white;font-size:10pt;z-index:-1;">&copy; כל הזכויות שמורות <a href="http://syspass.co" target="_blank" style="color:white;">UpPASS 3</a>.</div>';
?>