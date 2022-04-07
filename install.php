<?php
header('Content-Type: text/html; charset=UTF-8');
if(!function_exists(mb_strlen)) { echo '<div style="color:red;">השרת איננו תומך בפונקציה mb_strlen. <BR> בקש מבעל השרת להתקינה.</div>'; die(); }
$make = $_GET['make'];
if($make == 'actions') {
$do = $_GET['do'];
if($do == step1) {
$server = $_POST['server']; $duser = $_POST['duser']; $dname = $_POST['dname']; $dpass = $_POST['dpass'];
if($server == Null || $duser == Null || $dname == Null || $dpass == Null) {
echo 'e1';
}else{
$mysqli = new mysqli($server,$duser,$dpass,$dname);
if($mysqli->connect_error) {
echo 'e2';
}else{
if(!is_writable('config.php')) {
echo 'e3';
}else{
$file = fopen('config.php', 'w');
$text = '<?php
$dbserver = "'.$server.'"; $dbusername = "'.$duser.'"; $dbname = "'.$dname.'"; $dbpassword = "'.$dpass.'";
$mysqli = new mysqli("$dbserver", "$dbusername", "$dbpassword","$dbname");
?>';
fwrite($file, $text); 
require_once('config.php');
$mysqli->query("CREATE TABLE IF NOT EXISTS `up2_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `img` text NOT NULL,
  `content` text NOT NULL,
  `date` text NOT NULL,
  PRIMARY KEY (`id`)
)");
$mysqli->query("CREATE TABLE IF NOT EXISTS `up2_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `date` date NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)");
$mysqli->query("CREATE TABLE IF NOT EXISTS `up2_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `fname` text NOT NULL,
  `size` text NOT NULL,
  `date` datetime NOT NULL,
  `ip` text NOT NULL,
  `user` int(11) NOT NULL,
  `cat` text NOT NULL,
  `page` text NOT NULL,
  `private` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)");
$mysqli->query("CREATE TABLE IF NOT EXISTS `up2_noti` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `img` text NOT NULL,
  `date` text NOT NULL,
  `viewed` int(11) NOT NULL,
  `more` text NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)");
$mysqli->query("CREATE TABLE IF NOT EXISTS `up2_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `com` int(11) NOT NULL,
  `reason` text NOT NULL,
  `user` int(11) NOT NULL,
  `date` text NOT NULL,
  PRIMARY KEY (`id`)
)");
$mysqli->query("CREATE TABLE IF NOT EXISTS `up2_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `adminmail` text NOT NULL,
  `sname` text NOT NULL,
  `maxsize` text NOT NULL,
  `maxfiles` text NOT NULL,
  `rules` text NOT NULL,
  PRIMARY KEY (`id`)
)");
$mysqli->query("CREATE TABLE IF NOT EXISTS `up2_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `mail` text NOT NULL,
  `ip` text NOT NULL,
  `date` text NOT NULL,
  `group` text NOT NULL,
  `re` text NOT NULL,
  `private` int(11) NOT NULL,
  `img` text NOT NULL,
  `salt` text NOT NULL,
  `ban` text NOT NULL,
  PRIMARY KEY (`id`)
)");
} } }
}else{
require_once('config.php');
if($do == step2) {
$url = $_POST['url']; $am = $_POST['adminmail']; $sname = $_POST['sname']; $rules = $_POST['rules']; $maxsize = $_POST['maxsize']; $maxfiles = $_POST['maxfiles'];
if($url == Null || $am == Null || $sname == Null || $rules == Null || $maxsize == Null || $maxfiles == Null) {
echo 'e1';
}else{
$mysqli->query("INSERT INTO `up2_settings`(`url`,`adminmail`,`sname`,`rules`,`maxsize`,`maxfiles`) VALUES('$url','$am','$sname','$rules','$maxsize','$maxfiles')"); 
}
}else{
if($do == step3) {
$username = $_POST['username']; $password = $_POST['password']; $mail = $_POST['mail']; $ip = $_SERVER['REMOTE_ADDR']; $date = date("j.n.y");
if($username == Null || $password == Null || $mail == Null) {
echo 'e1';
}else{
$pass = md5($password);
$mysqli->query("INSERT INTO `up2_users`(`username`,`password`,`mail`,`ip`,`date`,`group`) VALUES('$username','$pass','$mail','$ip','$date','2')");
if(!unlink("install.php")) { echo '<BR><span style="color:#843E43;font-weight:bold;">השרת לא הצליח למחוק את הקובץ install.php. <BR> עשה זאת ידנית.</span>'; }
if(!chmod("i",0777)) { echo '<BR><span style="color:#843E43;font-weight:bold;">השרת לא הצליח לתת גישת 0777 לתיקיה i. <BR> עשה זאת ידנית.</span>'; }
}
}else{
echo 'Error!';
} } }
die;
}
$rules = '<ol>
<li>
אין להעלות לאתר קבצים המפרים את חוקי מדינת ישראל.
</li>
<li>
הקבצים המועלים לאתר הינם באחריותו של המעלה בלבד, ואין צוות האתר ישא את התוצאות.
</li>
<li>
אין להעלות לאתר קבצים שאינם קבצי תמונה (להלן: jpg,jpeg,png,gif,bmp).</li>
<li>
בעת העלאת הקובץ הינך מאשר כי הקובץ אינו שמור ע"י זכויות יוצרים, וכי צוות האתר לא ישא בתוצאות.
</li>
</ol>';
if(!chmod("config.php",0666)) { $econ = '<span style="color:#843E43;font-weight:bold;">השרת לא הצליח לתת גישת 0666 לקובץ config.php. <BR> עשה זאת ידנית.</span>'; }
$siteu = str_replace("/install.php","","http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
echo <<<Print
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="rtl">
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<title>התקנת המערכת UpPASS3</title>
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script> 
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/blitzer/jquery-ui.css" type="text/css" media="all"/>
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
<style type="text/css">
body {background:#7BC1C6; font-family:arial; font-size:10pt;}
.page,.page2,.page3,.page4 {background:#FFFFFF; width:800px; position:absolute;}
.title {background:#00B5FA; padding:10px; font-size:15pt; color:white; font-weight:bold;}
input[type=text],textarea,input[type=password] {background:white; border:1px solid #777777; padding:3px; color:#010101; font-family:arial;}
input[type=text]:hover,input[type=text]:focus,textarea:hover,textarea:focus,input[type=password]:hover,input[type=password]:focus {border:1px solid #3399FF;}
.next {background:#EBEBEB; padding:12px; margin-top:5px;}
input[type=submit] {background:#F05100; border:none; padding:7px 10px; color:white; font-weight:bold; font-size:14pt; font-family:arial; float:left;}
input[type=submit]:hover {opacity:0.9;}
.done {background:#0440B8; border:none; padding:7px 10px; color:white; font-weight:bold; font-size:14pt; font-family:arial; float:left; cursor:default;}
.done:hover {opacity:0.9;}
.fullwin {background:black; opacity:0.4; width:100%; height:100%; position:absolute; top:0; right:0; z-index:5; display:none;}
.error {background:#696969; width:100%; padding:20px 0 20px; color:white; position:absolute; 
top:40px; right:0; left:0; margin:auto; z-index:6; font-size:13pt; display:none;}
.ok {background:#26A0DA; border:none; padding:5px 15px; color:white; font-size:10pt; font-family:arial; float:left; cursor:default;}
.ok:hover {opacity:0.9;}
</style>
<script type="text/javascript">
$(document).ready(function() {
var surl;
$(".ok").click(function() {
$(".fullwin,.error").fadeOut(300);
});
$(".nextstep").click(function(eventObject) { $(".page").animate({"opacity":"0.3"},300);
eventObject.preventDefault();
var server = $("#server").val(),duser = $("#duser").val(),dname = $("#dname").val(),dpass = $("#dpass").val();
$.ajax({type:"POST",url:"?make=actions&do=step1",data:({server:server,duser:duser,dname:dname,dpass:dpass}),success:function(data) { $(".page").animate({"opacity":"1"},300);
if(data == "e1") {
$(".fullwin,.error").fadeIn(300);
$(".err").html("אחד מהשדות נשאר ריק.");
}else{
if(data == "e2") {
$(".fullwin,.error").fadeIn(300);
$(".err").html("לא ניתן להתחבר למסד.");
}else{
if(data == "e3") {
$(".fullwin,.error").fadeIn(300);
$(".err").html("לא ניתן לערוך את config.php. בדוק כי נתת הרשאת 666 לקובץ.");
}else{
$(".page").hide("slide",{direction:"up",easing:"easeInOutCirc"},800);
$(".page2").show("slide",{direction:"down",easing:"easeInOutCirc"},800);
} } } }
});
});
$(".nextstep2").click(function(eventObject) { $(".page2").animate({"opacity":"0.3"},300);
eventObject.preventDefault();
var url = $("#url").val(),adminmail = $("#adminmail").val(),sname = $("#sname").val(),rules = $("#rules").val(),maxsize = $("#maxsize").val(),maxfiles = $("#maxfiles").val();
surl = url;
$.ajax({type:"POST",url:"?make=actions&do=step2",data:({url:url,adminmail:adminmail,sname:sname,rules:rules,maxsize:maxsize,maxfiles:maxfiles}),success:function(data) { $(".page2").animate({"opacity":"1"},300);
if(data == "e1") {
$(".fullwin,.error").fadeIn(300);
$(".err").html("אחד מהשדות ריק.");
}else{
$(".page2").hide("slide",{direction:"up",easing:"easeInOutCirc"},800);
$(".page3").show("slide",{direction:"down",easing:"easeInOutCirc"},800);
} }
});
});
$(".nextstep3").click(function(eventObject) { $(".page3").animate({"opacity":"0.3"},300);
eventObject.preventDefault();
var username = $("#username").val(), password = $("#password").val(), mail = $("#mail").val();
$.ajax({type:"POST",url:"?make=actions&do=step3",data:({username:username,password:password,mail:mail}),success:function(data) { $(".page3").animate({"opacity":"1"},300);
if(data == "e1") {
$(".fullwin,.error").fadeIn(300);
$(".err").html("אחד מהשדות ריק.");
}else{
$(".steperrors").html(data);
$(".page3").hide("slide",{direction:"up",easing:"easeInOutCirc"},800);
$(".page4").show("slide",{direction:"down",easing:"easeInOutCirc"},800);
} }
});
});
$(".done").click(function() {
window.location.assign(surl);
});

$(".firstintro img").delay(300).show(1000);
$(".firstintro div").delay(2000).show("drop",{direction:"up"},1000);
$(".firstintro").delay(4000).fadeOut(1000);
$(".second").delay(5200).show("drop",{direction:"up"},1000);


});
</script>
<div class="firstintro" style="margin:15% auto;text-align:center;position:absolute;right:0;left:0;"><img src="images/logo.png" style="display:none;"><div style="font-size:20pt;color:white;display:none;">ברוכים הבאים להתקנה של UpPASS3!</div></div>
<div class="second" style="display:none;">
<div style="margin:10px 0 10px;">
<div style="background:rgb(81, 176, 172); width:770px; margin:auto; font-size:25pt; text-align:center; color:white; padding:10px 15px; font-weight:bold;">
התקנת המערכת UpPASS 3
</div></div>
<div class="fullwin"></div><div class="error"><div style="width:700px; margin:auto;"><span style="font-size:30pt;">שגיאה!</span><div class="err"></div><BR><div class="ok">אישור</div></div></div>
<div style="width:800px; margin:auto;">
<div class="page">
<div class="title">שלב 1 - תחילת ההתקנה</div>
<form action='' method="POST">
<div style="padding:5px 10px;">
ברוכים הבאים להתקנה של UpPASS 3! <BR>
$econ
<BR>
<b>פרטי SQL:</b><BR>
<table style="margin:auto;">
<tr><td>שרת: </td><td><input type="text" id="server" value="localhost" autocomplete="off" required></td></tr>
<tr><td>שם משתמש: </td><td><input type="text" id="duser" autocomplete="off"></td></tr>
<tr><td>שם: </td><td><input type="text" id="dname" autocomplete="off"></td></tr>
<tr><td>סיסמא: </td><td><input type="password" id="dpass" autocomplete="off"> </td></tr>
</table>
</div>
<div class="next"><input type="submit" value="הבא" name="submit" class="nextstep"><div style="clear:both;"></div></div>
</form>
</div>
<div class="page2" style="display:none;">
<div class="title">שלב 2 - הגדרות בסיסיות</div>
<form action='' method="POST">
<div style="padding:5px 10px;">
מלא את ההגדרות הבאות. ניתן לאחר מכן לערוך אותן דרך הפאנל. <BR>
<table style="margin:auto;">
<tr><td>כתובת האתר:</td><td><input type="text" id="url" placeholder="http://example.com" value="$siteu"></td></tr>
<tr><td>מייל לצור קשר:</td><td><input type="text" id="adminmail" placeholder="admin@example.com"></td></tr>
<tr><td>שם האתר:</td><td><input type="text" id="sname" placeholder="UpPASS 3"></td></tr>
<tr><td>גודל מקסימלי לקובץ:</td><td><input type="text" id="maxsize" value="5" size="2"> MB</td></tr>
<tr><td>מקסימום קבצים להעלאה בבת אחת:</td><td><input type="text" id="maxfiles" value="10"></td></tr>
<tr><td>תקנון:</td><td><textarea cols="30" rows="5" id="rules">$rules</textarea></td></tr>
</table>
</div>
<div class="next">
<input type="submit" value="הבא" name="submit" class="nextstep2"><div style="clear:both;"></div>
</div>
</form>
</div>
<div class="page3" style="display:none;">
<div class="title">שלב 3 - הגדרת האדמין</div>
<form action='' method="POST">
<div style="padding:5px 10px;">
הכנס את פרטי המשתמש הראשי שלך.
<table style="margin:auto;">
<tr><td>שם משתמש:</td><td><input type="text" id="username"></td></tr>
<tr><td>סיסמא:</td><td><input type="password" id="password"></td></tr>
<tr><td>מייל:</td><td><input type="text" id="mail"></td></tr>
</table></div>
<div class="next">
<input type="submit" value="הבא" class="nextstep3"><div style="clear:both;"></div>
</div>
</form>
</div>
<div class="page4" style="display:none;">
<div class="title">שלב 4 - סיום ההתקנה</div>
<div style="padding:5px 10px;">
ההתקנה הושלמה בהצלחה! 
<div class="steperrors"></div>
</div>
<div class="next">
<div class="done">לאתר</div><div style="clear:both;"></div>
</div></div>
</div>
</div>

Print;
?>
