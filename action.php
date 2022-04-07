<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
header('Content-Type: text/html; charset=UTF-8');
require_once('config.php');
$q1 = $mysqli->query("SELECT * FROM `up2_settings` where `id`='1'");
$h = $q1->fetch_assoc(); $url = $h['url']; $adminmail = $h['adminmail']; $sname = $h['sname']; $maxsize = $h['maxsize']; $maxfiles = $h['maxfiles']; $rules = $h['rules']; 
if (isset($_COOKIE['up2log'])) {
$us = $mysqli->query("SELECT * FROM `up2_users`"); 
while($pw = $us->fetch_assoc()) { $mkcookie = md5(md5($pw['username']).','.$pw['password'].'-'.$pw['salt']); $cpw = $_COOKIE['up2log'];
if($cpw == $mkcookie) {
$canlogin = 1; $myid = $pw['id']; $mymail = $pw['mail']; $myuser = $pw['username']; $mypw = $_pw['password']; $mytype = $pw['group']; $mysalt = $pw['salt']; $uimg = $pw['img']; $myimg = $url.'/i/'.$pw['img']; if($pw['img'] == '') { $myimg = $url.'/images/noimg.png'; }
} } }
$do = $_GET['do'];
if($do == clearnoti && $canlogin == 1) {
$mysqli->query("DELETE FROM `up2_noti` WHERE `to`='$myid' OR (`to`='0' AND '$mytype'='2')");
}else{
if($do == newnoti && $canlogin == 1) {
$q103 = $mysqli->query("SELECT * FROM `up2_noti` WHERE (`to`='$myid' OR (`to`='0' AND '$mytype'='2')) AND `viewed`='0'");
if($q103->num_rows > 0) { $q3 = $mysqli->query("SELECT * FROM `up2_noti` WHERE `to`='$myid' OR (`to`='0' AND '$mytype'='2') ORDER BY `id` DESC LIMIT 20");
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
}else{
echo '0';
}
}else{
if($do == viewed && $canlogin == 1) {
$mysqli->query("UPDATE `up2_noti` SET `viewed`='1' WHERE `to`='$myid' OR (`to`='0' AND '$mytype'='2')");
}else{
if($do == rotate && $canlogin == 1) { $fgid = $mysqli->real_escape_string($_POST['fgid']); $deg = intval($_POST['deg']);
$q94 = $mysqli->query("SELECT * FROM `up2_images` WHERE `id`='$fgid' AND `user`='$myid'");
if($q94->num_rows > 0 && $deg >= 0 && $deg <= 2) { $qx = $q94->fetch_assoc(); $fname = 'i/'.$qx['fname']; $ar = explode(".",$fname); $format = $ar[1]; 
if($format == 'gif') { $source = imagecreatefromgif($fname); }
if($format == 'jpg' || $format == 'jpeg') { $source = imagecreatefromjpeg($fname); }
if($format == 'png') { $source = imagecreatefrompng($fname); }
if($source) {
$transparency = imagecolorallocatealpha($source,0,0,0,127);
$rotate = imagerotate($source, (1+$deg)*90 , $transparency, 1);
imagealphablending($rotate,false); imagesavealpha($rotate,true);
if($format == 'gif') { imagegif($rotate,$fname); }
if($format == 'jpg' || $format == 'jpeg') { imagejpeg($rotate,$fname); }
if($format == 'png') { imagepng($rotate,$fname); }
} }
}else{
if($do == report && $canlogin == 1) { $cid = $mysqli->real_escape_string($_POST['cid']); $reason = $mysqli->real_escape_string($_POST['reason']); $cap = intval($_POST['cap']);
if(mb_strlen($reason,'UTF-8') < 3 || mb_strlen($reason,'UTF-8') > 400) {
echo 'e1';
}else{
if($mysqli->query("SELECT * FROM `up2_comments` WHERE `id`='$cid'")->num_rows == 0) {
echo 'e2';
}else{
if($cap != $_SESSION['cap']) {
echo 'e3';
}else{ $date = date("j.n.y - H:i");
$mysqli->query("INSERT INTO `up2_reports`(`com`,`reason`,`user`,`date`) VALUES('$cid','$reason','$myid','$date')");
$mysqli->query("INSERT INTO `up2_noti`(`date`,`type`) VALUES('$date','3')");
} } }
}else{
if($do == newpimg && $canlogin == 1) { $tmp_name = $_FILES["img"]["tmp_name"]; $name = $_FILES["img"]["name"]; $size = $_FILES["img"]["size"];
$filetype = strtolower($_FILES["img"]["type"]); $fulltype = substr($name, strrpos($name, '.') + 1); 
if($size < $maxsize*1024*1024 && ($filetype == "image/jpg" || $filetype == "image/jpeg" || $filetype == "image/png" || $filetype == "image/gif" || $filetype == "image/bmp")) {
$tmp = rand(10000,99999); unlink('i/'.$uimg); $fullname = 'profile'.$myid.'-'.$tmp.'.'.$fulltype;
move_uploaded_file($tmp_name, "i/$fullname");
$mysqli->query("UPDATE `up2_users` SET `img`='$fullname' WHERE `id`='$myid'");
echo $url.'/i/'.$fullname;
}
}else{
if($do == loadcom) { $perpage = 20; $id = $mysqli->real_escape_string($_POST['img']);
$lpage = $mysqli->real_escape_string($_POST['lpage']*$perpage); if($_POST['lpage'] == '') { $lpage = 0; } 
$q3 = $mysqli->query("SELECT * FROM `up2_comments` WHERE `img`='$id' ORDER BY `id` DESC LIMIT $lpage,$perpage");
$numrw = $mysqli->query("SELECT * FROM `up2_comments` WHERE `img`='$id'")->num_rows;
if($mytype == 2) { $adminoptions = '<li class="delcom"><div class="icon" style="font-size:8pt;">&#xf1f8;</div> מחק תגובה</li>'; }
if($canlogin == 1) { $options = '<div class="icon tag" title="השב להודעה" style="font-size:9pt;cursor:pointer;">&#xf112;</div> <div class="icon openoptions" title="אפשרויות" style="font-size:9pt;cursor:pointer;">&#xf0d7;</div>'; }
while($cm = $q3->fetch_assoc()) { $comid = $cm['id']; $user = $cm['user']; $date = $cm['date']; $content = $cm['content'];
$q4 = $mysqli->query("SELECT * FROM `up2_users` WHERE `id`='$user'"); $fw = $q4->fetch_assoc(); $uname = $fw['username']; $img = $url.'/i/'.$fw['img']; if($fw['img'] == '') { $img = $url.'/images/noimg.png'; }
echo <<<Print
<div class="commentbox" id="com$comid"><div style="float:right;padding:5px;"><img src="$img" style="width:32px;height:32px;"></div><div style="float:right;width:calc(100% - 42px);"><div class="cname"><a href="$url/profile/$user">$uname:</a></div><div class="cdate">$date $options
<ul class="options" data-comid="$comid"><li class="reportcom"><div class="icon" style="font-size:8pt;">&#xf071;</div> דווח</li>$adminoptions</ul></div>
<div style="clear:both;"></div><div class="ctext">$content</div></div><div style="clear:both;"></div></div>
Print;
}
if($numrw > $lpage+$perpage) { echo '<div class="loadmorecom"><div style="clear:both;"></div>טען עוד תגובות</div>'; }
}else{
if($do == delrep && $mytype == 2) { $rid = $mysqli->real_escape_string($_POST['rid']);
$mysqli->query("DELETE FROM `up2_reports` WHERE `id`='$rid'");
}else{
if($do == delcom && $mytype == 2) { $id = $mysqli->real_escape_string($_POST['comid']); $rid = $mysqli->real_escape_string($_POST['rid']); $ban = $mysqli->real_escape_string($_POST['ban']);
if($ban == '1') { $qw = $mysqli->query("SELECT * FROM `up2_comments` WHERE `id`='$id'")->fetch_assoc(); $uid = $qw['user']; if($uid == $myid) { echo 'e1'; }else{ $b = time()+(7*60*60*24); 
$mysqli->query("UPDATE `up2_users` SET `ban`='$b' WHERE `id`='$uid'"); } }
$mysqli->query("DELETE FROM `up2_comments` WHERE `id`='$id'");
if($rid) { $mysqli->query("DELETE FROM `up2_reports` WHERE `id`='$rid'"); }
}else{
if($do == addcom && $canlogin == 1) { $content = $mysqli->real_escape_string($_POST['content']); $img = $mysqli->real_escape_string($_POST['img']);
if($mysqli->query("SELECT * FROM `up2_images` WHERE `page`='$img'")->num_rows == 0) {
echo 'e2';
}else{ 
$q89 = $mysqli->query("SELECT * FROM `up2_comments` WHERE `img`='$img' ORDER BY `id` DESC LIMIT 5"); $i = 0;
while($jn = $q89->fetch_assoc()) { if($jn['user'] == $myid) { $i++; } }
if($i >= 5) {
echo 'e3';
}else{
if(mb_strlen($content,'UTF-8') < 2 || mb_strlen($content,'UTF-8') > 120) {
echo 'e1';
}else{
if(substr_count($content,'#') > 10) {
echo 'e4';
}else{ $hs = ''; $newcontent = $content; $addednoti = 0; $q90 = $mysqli->query("SELECT * FROM `up2_images` WHERE `page`='$img'")->fetch_assoc();   $hisid = $q90['user'];
for($i = 0; $i < strlen($content); $i++) { if($hs != '' && $content[$i] != '#') { $hs .= $content[$i]; if($content[$i] == ' ') { $hs = ''; } }  
if(($content[$i+1] == ' ' || $content[$i] == '#' || $i+1 == strlen($content)) && $hs != '' && $hs != '#') { $usertag = $mysqli->real_escape_string(substr($hs,1));
$q83 = $mysqli->query("SELECT * FROM `up2_users` WHERE `username`='$usertag'"); if($q83->num_rows > 0) { $fe = $q83->fetch_assoc(); $usrid = $fe['id'];  $newcontent = str_replace($hs,'<a href="../profile/'.$usrid.'" style="color:#1F849E;font-style:italic;">'.$hs.'</a>',$newcontent); if($usrid != $hisid && $usrid != $myid) { $mysqli->query("INSERT INTO `up2_noti`(`from`,`to`,`img`,`date`,`type`) VALUES('$myid','$usrid','$img','$date','1')"); } }
 $hs = ''; }
if($content[$i] == '#') { $hs = '#'; }
}
$date = date('j.n.y - H:i'); 
$mysqli->query("INSERT INTO `up2_comments`(`user`,`img`,`content`,`date`) VALUES('$myid','$img','$newcontent','$date')");
$comid = $mysqli->insert_id;
if($hisid != $myid) { 
$q91 = $mysqli->query("SELECT * FROM `up2_noti` WHERE `to`='$hisid' AND `viewed`='0' ORDER BY `id` DESC LIMIT 1")->fetch_assoc();
if($q91['img'] == $img) { if(!in_array($myid,explode(",",$q91['more'])) && $myid != $q91['from']) { $notid = $q91['id']; $more = $q91['more']; if($more == '') { $more = $myid; }else{ $more .= ','.$myid; } $mysqli->query("UPDATE `up2_noti` SET `more`='$more' WHERE `id`='$notid'"); } }else{ $mysqli->query("INSERT INTO `up2_noti`(`from`,`to`,`img`,`date`,`type`) VALUES('$myid','$hisid','$img','$date','0')"); } 
}
if($mytype == 2) { $adminoptions = '<li class="delcom"><div class="icon" style="font-size:8pt;">&#xf1f8;</div> מחק תגובה</li>'; }
if($canlogin == 1) { $options = '<div class="icon tag" title="השב להודעה" style="font-size:9pt;cursor:pointer;">&#xf112;</div> <div class="icon openoptions" title="אפשרויות" style="font-size:9pt;cursor:pointer;">&#xf0d7;</div>'; }
echo <<<Print
<div class="commentbox newcb" style="display:none;" id="com$comid"><div style="float:right;padding:5px;"><img src="$myimg" style="width:32px;height:32px;"></div><div style="float:right;width:calc(100% - 42px);"><div class="cname"><a href="$url/profile/$myid">$myuser:</a></div><div class="cdate">$date $options
<ul class="options" data-comid="$comid"><li class="reportcom"><div class="icon" style="font-size:8pt;">&#xf071;</div> דווח</li>$adminoptions</ul></div>
<div style="clear:both;"></div><div class="ctext">$newcontent</div></div><div style="clear:both;"></div></div>
Print;
} } } }
}else{
if($do == loadpgallery) { $perpage = 30; $pid = $mysqli->real_escape_string($_POST['pid']); $cat = $mysqli->real_escape_string($_POST['gid']); $catl = ','.$cat; $catm = ','.$cat.','; $catr = $cat.',';
$lpage = $mysqli->real_escape_string($_POST['lpage']*$perpage); if($_POST['lpage'] == '') { $lpage = 0; } 
$q63 = $mysqli->query("SELECT * FROM `up2_users` WHERE `id`='$pid' AND `private`='0'");
if($q63->num_rows > 0 || ($q63->num_rows == 0 && $pid == $myid)) {
if($cat == 0) { $q62 = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$pid' AND `private`='0' ORDER BY `id` DESC LIMIT $lpage,$perpage");
$numrw = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$pid' AND `private`='0'")->num_rows;
}else{
$q62 = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$pid' AND `private`='0' AND (`cat` LIKE '%$catl' OR `cat` LIKE '$catr%' OR `cat` LIKE '%$catm%' OR `cat`='$cat') ORDER BY `id` DESC LIMIT $lpage,$perpage");
$numrw = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$pid' AND `private`='0' AND (`cat` LIKE '%$catl' OR `cat` LIKE '$catr%' OR `cat` LIKE '%$catm%' OR `cat`='$cat')")->num_rows;
}
while($rq = $q62->fetch_assoc()) { $page = $rq['page']; $fname = $rq['fname']; $img = $url.'/i/'.$fname; $name = htmlspecialchars($rq['name']);
echo <<<Print
<div class="pimgbox"><a href="$url/links/$page" target="_blank"><div class="pimage" style="background:url($img) no-repeat;background-size:cover;background-position:center;"></div><div class="pimgname">$name</div></a></div>
Print;
}
if($numrw > $lpage+$perpage) { echo '<div class="loadmoreimg"><div style="clear:both;"></div><BR>טען עוד תמונות</div>'; }
}
}else{
if($do == restore) {
$uom = $mysqli->real_escape_string($_POST['uom']); $cap = intval($_POST['cap']);
$q60 = $mysqli->query("SELECT * FROM `up2_users` WHERE `username`='$uom' OR `mail`='$uom'");
if($q60->num_rows == 0) {
echo 'e1';
}else{
if($cap != $_SESSION['cap']) {
echo 'e2';
}else{
while($ui = $q60->fetch_assoc()) { $tid = $ui['id']; $tuser = $ui['username']; $tmail = $ui['mail']; $tip = $_SERVER['REMOTE_ADDR'];
$chars = "abcdefghigklmnopqrstuvmxyz0123456789"; $recode = substr(str_shuffle($chars),0,10);
$mysqli->query("UPDATE `up2_users` SET `re`='$recode' WHERE `id`='$tid'");
$to=$tmail; $sub = 'שחזור סיסמא'; $headers  = 'MIME-Version: 1.0' . "\r\n"; $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n"; $sender = parse_url($url);  $headers .= 'from: '.$sname.' <noreply@'.$sender["host"].'>';
$message= '<div style="background:white;direction:rtl; font-family:arial; width:750px; margin:auto;margin-top:30px;color:#3b3636;border:1px solid rgb(139, 199, 201);">
<div style="background:#59D0FA;padding:20px;color:white;font-size:14pt;">'.$tuser.', התקבלה בקשה לשיחזור סיסמתך.</div> <div style=" padding:10px; ">
קישור לשיחזור הסיסמא: <a href="'.$url.'/restore/'.$recode.'">לחץ כאן</a>.<BR>במידה ואינך ביקשת זאת, התעלם מהודעה זו.<BR>
האייפי שממנו נשלחה הבקשה: '.$tip.'
<BR><BR>
בברכה, צוות האתר.
</div></div>';
$sentmail = mail($to,$sub,$message,$headers);
} } }
}else{
if($do == siteset && $mytype == 2) {
$ul = $_POST['url']; $am = $_POST['adminmail']; $sn = $_POST['sname']; $ms = $_POST['maxsize']; 
$mf = $_POST['maxfiles']; $rules = $_POST['rules'];
if($ul == Null) {
echo 'e1';
}else{
if($am == Null) {
echo 'e2';
}else{
if($sn == Null) {
echo 'e3';
}else{
if($ms == Null) {
echo 'e4';
}else{
if($mf == Null) {
echo 'e5';
}else{
if($rules == Null) {
echo 'e6';
}else{
$mysqli->query("UPDATE `up2_settings` SET `url`='$ul',`adminmail`='$am',`sname`='$sn',`maxsize`='$ms',`maxfiles`='$mf',`rules`='$rules' WHERE `id`='1'");
} } } } } }
}else{
if($do == numimg && $canlogin == 1) {
$gid = $_POST['gid']; $nimgs = 0;
if($gid == "allimg") {
$q19 = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$myid'");
$nimgs = $q19->num_rows;
}else{
$q11 = $mysqli->query("SELECT * FROM `up2_gallery` WHERE `id`='$gid' AND `user`='$myid'");
if($q11->num_rows > 0) {
$q12 = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$myid' AND `cat`!=''");
while($t = $q12->fetch_assoc()) { $cati = explode(",",$t['cat']);
for($i = 0; $i < sizeof($cati); $i++) {
if($cati[$i] == $gid) {
$nimgs++;
} }
} } }
echo ceil($nimgs/50);
}else{
if($do == limg && $canlogin == 1) { $page = (intval($_POST['page'])*50); $gid = $mysqli->real_escape_string($_POST['cat']); 
if($gid == 'allimg') { 
$cunt = $mysqli->query("SELECT * FROM `up2_images` where `user`='$myid'")->num_rows;
$q19 = $mysqli->query("SELECT * FROM `up2_images` where `user`='$myid' ORDER BY `id` DESC LIMIT $page,50");
}else{ 
$cat = $gid; $catl = ','.$cat; $catm = ','.$cat.','; $catr = $cat.',';
$cunt = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$myid' AND (`cat` LIKE '%$catl' OR `cat` LIKE '$catr%' OR `cat` LIKE '%$catm%' OR `cat`='$cat')")->num_rows;
$q19 = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$myid' AND (`cat` LIKE '%$catl' OR `cat` LIKE '$catr%' OR `cat` LIKE '%$catm%' OR `cat`='$cat') ORDER BY `id` DESC LIMIT $page,50");
}
if($q19->num_rows == 0) {
echo 'e1';
}else{
while($t = $q19->fetch_assoc()) { $fid = $t['id']; $finame = htmlspecialchars($t['name']);  $fpage = $t['page']; $fpri = $t['private'];  $fname = $t['fname']; $fisize = round($t['size']/(1024*1024),3); $mdate = $t['date'];
$phpdate = strtotime($mdate); $fidate = date('j.n.y - H:i', $phpdate); if(mb_strlen($finame,'UTF-8') > 30) { $finame = mb_substr($finame,0,30,'UTF-8').'..'; }
$lock = ''; if($fpri == 1) { $lock = '<div class="icon" style="font-size:8pt;display:inline-block;">&#xf023;</div>';  }
echo <<<Print
<div class="filebox" id="f$fid" data-page="$fpage" style="display:none;"><div class="fileimg"><img src="$url/i/$fname"></div>$lock<div class="filename">$finame</div></div>
Print;
} 
if($cunt > (50+intval($_POST['page'])*50)) { echo '<div style="clear:both;"></div><div class="load" id="loallimg" style="background:#E0EDF6;">טען עוד תמונות</div>'; }
}
}else{
if($do == loadusr && $mytype == 2) {
$page = $_POST['page']*50;
$q32 = $mysqli->query("SELECT * FROM `up2_users` ORDER BY `id` DESC LIMIT $page,50");
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
}else{
if($do == loadgall && $mytype == 2) {
$page = $_POST['page']*50;
$q59 = $mysqli->query("SELECT * FROM `up2_gallery` ORDER BY `id` DESC LIMIT $page,50");
while($t = $q59->fetch_assoc()) { $fid = $t['id']; $fname = $t['name']; $muser = $t['user']; $mdate = $t['date']; $phpdate = strtotime($mdate); $fidate = date('j.n.y', $phpdate);
echo <<<Print
<tr id="g$fid"><td>$fname</td><td>$fidate</td><td>$muser</td><td><img src="$url/images/remove.png" title="&#1502;&#1495;&#1511; &#1490;&#1500;&#1512;&#1497;&#1492;" class="remove"></td></tr>
Print;
}
}else{
if($do == loadimg && $mytype == 2) {
$page = $_POST['page']*60;
$q50 = $mysqli->query("SELECT * FROM `up2_images` ORDER BY `id` DESC LIMIT $page,60");
while($t = $q50->fetch_assoc()) {$fid = $t['id']; $finame = htmlspecialchars($t['name']); $fname = $t['fname']; $fisize = round($t['size']/(1024*1024),3); $mdate = $t['date']; $mip = $t['ip']; $muser = $t['user']; $pg = $t['page'];
$phpdate = strtotime($mdate); $fidate = date('j.n.y - H:i', $phpdate);
if($muser == '0') { $username = '<b>אורח</b>'; }else{ $udet = $mysqli->query("SELECT * FROM `up2_users` WHERE `id`='$muser'")->fetch_assoc(); $username = '<a href="'.$url.'/profile/'.$muser.'" target="_blank">'.$udet['username'].'</a>'; }
if(mb_strlen($finame,'UTF-8') > 15) { $finame = mb_substr($finame,0,15,'UTF-8').'..';}
echo <<<Print
<div class="newimgs" id="f$fid" style="text-align:center;float:right;display:none;"><div style="background:url($url/i/$fname) no-repeat; background-position:center; background-size:cover;width:200px;height:200px;margin:10px;position:relative;"><div style="background:#D87575;font-size:8pt;border-radius:5px;padding:2px 6px;position:absolute;bottom:-10px;left:-10px;color:white;font-weight:bold;" class="remove"><div class="icon">&#xf014;</div> מחק</div></div><a href="$url/links/$pg" target="_blank">$finame <BR> <div style="background:#EAEAEA;font-size:10pt;border-radius:5px;padding:2px 6px;display:inline-block;">$username</div> $fidate </a></div>
Print;
}
}else{
if($do == ban && $mytype == 2) { $days = $_POST['days']; $uid = $_POST['uid']; 
if($days != 'f' && $days > 0) { $d = time()+($days*60*60*24); }else{ if($days == '0') { $d = '0'; }else{ $d = 'f'; } }
if($uid == $myid) {
echo 'e1'; 
}else{
$mysqli->query("UPDATE `up2_users` SET `ban`='$d' WHERE `id`='$uid'");
}
}else{
if($do == gadel && $mytype == 2) {
$id = $_POST['id'];
$mysqli->query("DELETE FROM `up2_gallery` WHERE `id`='$id'");
}else{
if($do == imdel && $mytype == 2) {
$id = $_POST['id'];
$q69 = $mysqli->query("SELECT * FROM `up2_images` WHERE `id`='$id'");
$query = $q69->fetch_assoc(); $fname = $query['fname'];
$mysqli->query("DELETE FROM `up2_images` WHERE `id`='$id'");
$filename = 'i/'.$fname;
unlink($filename);
}else{
if($do == adel && $mytype == 2) {
$id = $_POST['id'];
if($id == $myid) {
echo 'e1';
}else{
$mysqli->query("DELETE FROM `up2_users` WHERE `id`='$id'");
$mysqli->query("DELETE FROM `up2_gallery` WHERE `user`='$id'");
$mysqli->query("DELETE FROM `up2_noti` WHERE `from`='$id' OR `to`='$id'");
$mysqli->query("DELETE FROM `up2_reports` WHERE `user`='$id'");
$mysqli->query("DELETE FROM `up2_comments` WHERE `user`='$id'");
$q111 = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$id'");
while($is = $q111->fetch_assoc()) { $mid = $is['id']; $fname = $is['fname'];
$mysqli->query("DELETE FROM `up2_images` WHERE `id`='$mid'");
$filename = 'i/'.$fname;
unlink($filename);
}
}
}else{
if($do == agroup && $mytype == 2) {
$id = $mysqli->real_escape_string($_POST['id']);
if($id == $myid) { echo 'e1'; }else{
$q41 = $mysqli->query("SELECT * FROM `up2_users` WHERE `id`='$id'");
$tr = $q41->fetch_assoc(); $type = $tr['group'];
if($type == 1) {
$mysqli->query("UPDATE `up2_users` SET `group`='2' WHERE `id`='$id'");
echo 'אדמין';
}else{
$mysqli->query("UPDATE `up2_users` SET `group`='1' WHERE `id`='$id'");
echo 'משתמש';
} }
}else{
if($do == contact) {
$name = $_POST['name']; $mail = $_POST['mail']; $sub = $_POST['sub']; $mess = $_POST['mess']; $cap = intval($_POST['cap']);
if(mb_strlen($name,'UTF-8') < 3 || mb_strlen($name,'UTF-8') > 18) {
echo 'e1';
}else{
if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $mail)) {
echo 'e2';
}else{
if(mb_strlen($sub,'UTF-8') < 3 || mb_strlen($sub,'UTF-8') > 35) {
echo 'e3';
}else{
if(mb_strlen($mess,'UTF-8') < 2 || mb_strlen($mess,'UTF-8') > 1500) {
echo 'e4';
}else{
if($cap != $_SESSION['cap']) {
echo 'e5';
}else{
$sub = $_POST['sub']; $name = $_POST['name']; $mess = nl2br(htmlspecialchars($_POST['mess']));
$to=$adminmail; $headers  = 'MIME-Version: 1.0' . "\r\n"; $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";  $headers .= 'from: '.$name.' <'.$mail.'>';
$message= '<div style="background:white;direction:rtl; font-family:arial; width:750px; margin:auto;margin-top:30px;color:#3b3636;border:1px solid rgb(139, 199, 201);">
<div style="background:#59D0FA;padding:20px;color:white;font-size:14pt;">'.$sub.'</div> <div style=" padding:10px; ">'.$mess.'<BR> <b> מאת: '.$name.' - '.$mail.'.</b></div></div>';
$sentmail = mail($to,$sub,$message,$headers);
} } } } }
}else{
if($do == set && $canlogin == 1) {
$pw = $_POST['pw']; $newpw = $_POST['newpw']; $newmail = $mysqli->real_escape_string($_POST['newmail']); $pw5 = md5($pw); $pri = intval($_POST['pri']);
if($pri != 1) { $pri = '0'; }
$q30 = $mysqli->query("SELECT * FROM `up2_users` WHERE `id`='$myid' AND `password`='$pw5'");
if($newpw == Null) { $newpw = $pw; }
if($q30->num_rows == 0) {
echo 'e1';
}else{
if(mb_strlen($newpw,'UTF-8') < 6 && mb_strlen($newpw,'UTF-8') > 18 && $newpw != Null) {
echo 'e2';
}else{
if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $newmail)) {
echo 'e3';
}else{
$q31 = $mysqli->query("SELECT * FROM `up2_users` WHERE `mail`='$newmail' AND `id`!='$myid'");
if($q31->num_rows > 0) {
echo 'e4';
}else{
$newpw = md5($newpw);
$mysqli->query("UPDATE `up2_users` SET `password`='$newpw',`mail`='$newmail',`private`='$pri' WHERE `id`='$myid'");
if($newpw != $pw5) { $pass = md5(md5($myuser).','.$newpw.'-'.$mysalt);
setcookie("up2log","$pass", time() +86400000); }
} } } }
}else{
if($do == logout && $canlogin == 1) { $stay = htmlspecialchars($_GET['stay']);
$pass = md5(md5($myid).','.$mypw);
setcookie("up2log","$pass", time() - 86400000);
header('location: '.$stay);
}else{
if($do == remove && $canlogin == 1) { $fileid = $_POST['gid']; $nowat = $_POST['nowat'];
if(is_array($fileid) == "Array") {
for($j = 0; $j < sizeof($fileid); $j++) { $gid = $fileid[$j];
$q21 = $mysqli->query("SELECT * FROM `up2_gallery` WHERE `id`='$nowat' AND `user`='$myid'");
if($q21->num_rows > 0) {
$c = 0; 
$q22 = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$myid' AND `id`='$gid'"); $t = $q22->fetch_assoc();
$nowid = $t['id']; $cati = explode(",",$t['cat']); $newcat = ""; $fname = $t['fname'];
for($i = 0; $i < sizeof($cati); $i++) {
if($cati[$i] != $nowat) {
if($i+1 == sizeof($cati)) { $newcat .= $cati[$i]; }else{ $newcat .= $cati[$i].","; }
} }
if($newcat[strlen($newcat)-1] == ',') { $newcat = substr($newcat, 0, -1); }
$mysqli->query("UPDATE `up2_images` SET `cat`='$newcat' WHERE `id`='$gid'");
}
} }
}else{
if($do == regal && $canlogin == 1) {
$newname = $mysqli->real_escape_string(htmlspecialchars($_POST['newname'])); $galid = $mysqli->real_escape_string($_POST['galid']);
$q23 = $mysqli->query("SELECT * FROM `up2_gallery` WHERE `id`='$galid' AND `user`='$myid'");
if($q23->num_rows > 0) {
if(mb_strlen($newname,'UTF-8') == 0 || mb_strlen($newname,'UTF-8') > 25) {
echo 'e1';
}else{
$mysqli->query("UPDATE `up2_gallery` SET `name`='$newname' WHERE `id`='$galid'");
} }
}else{
if($do == rename && $canlogin == 1) {
$newname = $mysqli->real_escape_string($_POST['newname']); $imgid = $mysqli->real_escape_string($_POST['imgid']);
$q22 = $mysqli->query("SELECT * FROM `up2_images` WHERE `id`='$imgid' AND `user`='$myid'");
if($q22->num_rows > 0) {
if(mb_strlen($newname,'UTF-8') == 0 || mb_strlen($newname,'UTF-8') > 35) {
echo 'e1';
}else{
$mysqli->query("UPDATE `up2_images` SET `name`='$newname' WHERE `id`='$imgid'");
} }
}else{
if($do == gdel && $canlogin == 1) {
$gid = $_POST['gid']; $dimg = $_POST['dimg'];
$q11 = $mysqli->query("SELECT * FROM `up2_gallery` WHERE `id`='$gid' AND `user`='$myid'");
if($q11->num_rows > 0) {
$c = 0; 
$q12 = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$myid' AND `cat`!=''");
while($t = $q12->fetch_assoc()) { 
$nowid = $t['id']; $cati = explode(",",$t['cat']); $newcat = ""; $fname = $t['fname'];
for($i = 0; $i < sizeof($cati); $i++) {
if($cati[$i] != $gid) {
if($i+1 == sizeof($cati)) { $newcat .= $cati[$i]; }else{ $newcat .= $cati[$i].","; }
}else{
if($dimg == 1) {
$mysqli->query("DELETE FROM `up2_images` WHERE `id`='$nowid'");
$filename = 'i/'.$fname;
unlink($filename);
}
} }
$mysqli->query("UPDATE `up2_images` SET `cat`='$newcat' WHERE `id`='$nowid'"); } 
$mysqli->query("DELETE FROM `up2_gallery` WHERE `id`='$gid'"); }
}else{
if($do == pastef && $canlogin == 1) {
$copied = $_POST['copied']; $nowat = $_POST['nowat'];
$q15 = $mysqli->query("SELECT * FROM `up2_gallery` WHERE `user`='$myid' AND `id`='$nowat'");
if($q15->num_rows == 0) {
echo 'e1';
}else{
if(sizeof($copied) == 0) {
echo 'e1';
}else{
for($i = 0; $i < sizeof($copied); $i++) {
$copy = $copied[$i];
$q16 = $mysqli->query("SELECT * FROM `up2_images` WHERE `id`='$copy'");
$sc = $q16->fetch_assoc(); $getcat = $sc['cat'];
if($getcat != Null && $getcat != $nowat) {
$nowati = $getcat.','.$nowat;
}else{
$nowati = $nowat;
}
$mysqli->query("UPDATE `up2_images` SET `cat`='$nowati' WHERE `id`='$copy'");
} } }
}else{
if($do == loadga && $canlogin == 1) {
$gid = $mysqli->real_escape_string($_POST['gid']);
if($gid == 'allimg') { 
$cunt = $mysqli->query("SELECT * FROM `up2_images` where `user`='$myid'")->num_rows;
$q19 = $mysqli->query("SELECT * FROM `up2_images` where `user`='$myid' ORDER BY `id` DESC LIMIT 50");
if($q19->num_rows == 0) { $mess = 'אין לך תמונות  במשתמש זה.'; } 
}else{ 
$q11 = $mysqli->query("SELECT * FROM `up2_gallery` WHERE `id`='$gid' AND `user`='$myid'");
if($q11->num_rows == 0) { $mess = 'לא ניתן לטעון גלריה זו.'; }else{ $cat = $gid; $catl = ','.$cat; $catm = ','.$cat.','; $catr = $cat.',';
$cunt = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$myid' AND (`cat` LIKE '%$catl' OR `cat` LIKE '$catr%' OR `cat` LIKE '%$catm%' OR `cat`='$cat')")->num_rows;
$q19 = $mysqli->query("SELECT * FROM `up2_images` WHERE `user`='$myid' AND (`cat` LIKE '%$catl' OR `cat` LIKE '$catr%' OR `cat` LIKE '%$catm%' OR `cat`='$cat') ORDER BY `id` DESC LIMIT 50");
if($q19->num_rows == 0) { $mess = '<div class="pastehere">אין לך תמונות בגלריה זו. <BR><span style="font-size:10pt;">לחץ מקש ימני של העכבר להדבקת קבצים</span></div>'; } }
}
if($mess) {
echo '<div style="padding:10px;">'.$mess.'</div>';
}else{
while($t = $q19->fetch_assoc()) { $fid = $t['id']; $finame = htmlspecialchars($t['name']);  $fpage = $t['page']; $fpri = $t['private'];  $fname = $t['fname']; $fisize = round($t['size']/(1024*1024),3); $mdate = $t['date'];
$phpdate = strtotime($mdate); $fidate = date('j.n.y - H:i', $phpdate); if(mb_strlen($finame,'UTF-8') > 30) { $finame = mb_substr($finame,0,30,'UTF-8').'..'; }
$lock = ''; if($fpri == 1) { $lock = '<div class="icon" style="font-size:8pt;display:inline-block;">&#xf023;</div>';  }
echo <<<Print
<div class="filebox" id="f$fid" data-page="$fpage" style="display:none;"><div class="fileimg"><img src="$url/i/$fname"></div>$lock<div class="filename">$finame</div></div>
Print;
} 
if($cunt > 50) { echo '<div style="clear:both;"></div><div class="load" id="loallimg" style="background:#E0EDF6;">טען עוד תמונות</div>'; }
}
}else{
if($do == newga && $canlogin == 1) {
$ganame = $mysqli->real_escape_string(htmlspecialchars($_POST['ganame'])); $date = date("y.n.j - H:i");
if(mb_strlen($ganame,'UTF-8') == 0 || mb_strlen($ganame,'UTF-8') > 30) {
echo 'e1';
}else{
$mysqli->query("INSERT INTO `up2_gallery`(`name`,`date`,`user`) VALUES('$ganame','$date','$myid')");
echo $mysqli->insert_id;
}
}else{
if($do == delfile && $canlogin == 1) { $fileid = $_POST['fileid'];
if(is_array($fileid) == "Array") {
for($i = 0; $i < sizeof($fileid); $i++) {
$thisid = $mysqli->real_escape_string($fileid[$i]);
$q6 = $mysqli->query("SELECT * FROM `up2_images` WHERE `id`='$thisid' AND `user`='$myid'");
if($q6->num_rows == 1) {
$qu = $q6->fetch_assoc(); $fname = $qu['fname']; 
$mysqli->query("DELETE FROM `up2_images` WHERE `id`='$thisid'");
$filename = 'i/'.$fname;
unlink($filename);
} }
}else{ $fileid = $mysqli->real_escape_string($fileid);
$q6 = $mysqli->query("SELECT * FROM `up2_images` WHERE `id`='$fileid' AND `user`='$myid'");
if($q6->num_rows == 1) {
$qu = $q6->fetch_assoc(); $fname = $qu['fname']; 
$mysqli->query("DELETE FROM `up2_images` WHERE `id`='$fileid'");
$filename = 'i/'.$fname;
unlink($filename);
} }
}else{
if($do == privatefile && $canlogin == 1) { function dopri($fileid) { global $mysqli,$myid; $fileid = $mysqli->real_escape_string($fileid); if($mysqli->query("SELECT * FROM `up2_images` WHERE `id`='$fileid' AND `user`='$myid'")->num_rows > 0) {
$to = '1'; if($mysqli->query("SELECT * FROM `up2_images` WHERE `id`='$fileid' AND `user`='$myid' AND `private`='0'")->num_rows == 0) { $to = '0'; }
$mysqli->query("UPDATE `up2_images` SET `private`='$to' WHERE `id`='$fileid' AND `user`='$myid'");
} } $fileid = $_POST['fileid']; 
if(is_array($fileid) == "Array") {
for($i = 0; $i < sizeof($fileid); $i++) { $thisid = $fileid[$i]; dopri($thisid); }
}else{ 
dopri($fileid);
}

}else{
if($do == upload) {
$uploads_dir = "i";
if(isset($_FILES["file"])) {
$once = 0; $output = ''; 
for($i = 0; $i < sizeof($_FILES["file"]["tmp_name"]); $i++) { if($i%5==0) { if($i > 0) { $output .= "</tr>"; } $output .= "<tr>"; }
$tmp_name = $_FILES["file"]["tmp_name"][$i];
$name = $mysqli->real_escape_string($_FILES["file"]["name"][$i]);  $size = $_FILES["file"]["size"][$i];
$filetype = strtolower($_FILES["file"]["type"][$i]); $date = date("y.n.j - H:i");
$fulltype = substr($name, strrpos($name, '.') + 1); $ip = $_SERVER['REMOTE_ADDR'];
$chars = "abcdefghigklmnopqrstuvmxyz0123456789"; $pagech = "ABCDEFGHIGKLMNOPQRSTUVMXYZ0123456789"; 
do { $newname = substr(str_shuffle($chars),0,7); $fullname = $newname.'.'.$fulltype;
$numrw = $mysqli->query("SELECT * FROM `up2_images` WHERE `fname`='$fullname'")->num_rows;
} while($numrw > 0);
do { $page = substr(str_shuffle($pagech),0,7); 
$pnumrw = $mysqli->query("SELECT * FROM `up2_images` WHERE `page`='$page'")->num_rows;
} while($pnumrw > 0);
if($size > $maxsize*1024*1024) {
echo 'הקובץ גדול מידי!';
die;
}else{
if($filetype != "image/jpg" && $filetype != "image/jpeg" && $filetype != "image/png" && $filetype != "image/gif" && $filetype != "image/bmp" && $filetype != "image/vnd.microsoft.icon") {
echo 'הקובץ אינו מסוג תמונה!';
die;
}else{
if(sizeof($_FILES["file"]["tmp_name"]) > $maxfiles) {
echo 'לא ניתן להעלות יותר תמונות מהכמות המקסימלית!';
die; 
}else{
move_uploaded_file($tmp_name, "$uploads_dir/$fullname"); 
if($canlogin == 1) {$user = $myid;}else{$user = 0;} $pup = '0'; if($_POST['privateup'] == 'yes') { $pup = '1'; }
$mysqli->query("INSERT INTO `up2_images`(`name`,`fname`,`size`,`date`,`ip`,`user`,`page`,`private`) VALUES('$name','$fullname','$size','$date','$ip','$user','$page','$pup')");
$fileid = $mysqli->insert_id; if(mb_strlen($name,'UTF-8') > 15) { $name = substr($name,0,15).'..'; } 
$output .= '<td><a href="'.$url.'/i/'.$fullname.'" target="_blank"><img src="i/'.$fullname.'"></a><div style="font-size:10pt;">'.$name.'<BR><a href="'.$url.'/links/'.$page.'" target="_blank"><div class="icon" title="קישור לדף התמונה">&#xf0c1;</div></a> - <a href="'.$url.'/i/'.$fullname.'" target="_blank"><div class="icon" title="קישור לתמונה">&#xf03e;</div></a></div></td>';
} } } }
echo $_POST['privateup'].'-'.$output;
die;
}
}else{
if($do == login) {
$usr = $mysqli->real_escape_string($_POST['usr']); $pw = md5($_POST['pw']);
$q2 = $mysqli->query("SELECT * FROM `up2_users` WHERE `username`='$usr' AND `password`='$pw'");
if($q2->num_rows == 0) {
echo 'e1';
}else{
$q = $q2->fetch_assoc(); $type = $q['group'];
if($type != 1 && $type != 2) {
echo 'e2';
}else{
$salt = $q['salt']; $pass = md5(md5($usr).','.$pw.'-'.$salt);
setcookie("up2log","$pass", time() +86400000);
echo 'log';
} }
}else{
if($do == register) {
$user = $mysqli->real_escape_string($_POST['username']); $pass = $_POST['password']; $mail = $_POST['mail']; $ip = $_SERVER['REMOTE_ADDR']; $date = date("j.n.y"); $pr = $_POST['pr'];
if($pr == 'true') { $pr = 1; }else{ $pr = 0; } $cap = intval($_POST['cap']);
if(mb_strlen($user,'UTF-8') < 4 || mb_strlen($user,'UTF-8') > 15) {
echo 'e1';
}else{
if(preg_match('/[\'"^£$%&*()}{@#~?><>,|=_+¬-]/',$user) || preg_match("/\\s/", $user)) {
echo 'e2';
}else{
$q1 = $mysqli->query("SELECT * FROM `up2_users` WHERE `username`='$user'");
if($q1->num_rows > 0) {
echo 'e3';
}else{
if(mb_strlen($pass,'UTF-8') < 6 || mb_strlen($pass,'UTF-8') > 18) {
echo 'e4';
}else{
if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $mail)) {
echo 'e5';
}else{
$q1 = $mysqli->query("SELECT * FROM `up2_users` WHERE `mail`='$mail'");
if($q1->num_rows > 0) {
echo 'e6';
}else{
if($cap != $_SESSION['cap']) {
echo 'e7';
}else{
$chars = "abcdefghigklmnopqrstuvmxyzABCDEFGHIGKLMNOPQRSTUVMXYZ0123456789"; $active = substr(str_shuffle($chars),0,5); $salt = substr(str_shuffle($chars),0,6);
$to=$mail; $subject = "אישור משתמש"; $headers  = 'MIME-Version: 1.0' . "\r\n"; $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";  $headers .= 'from: '.$sname.' <noreply@'.$url.'>';
$message= '<div style="background:white;direction:rtl; font-family:arial; width:750px; margin:auto;margin-top:30px;color:#3b3636;border:1px solid rgb(139, 199, 201);">
<div style="background:#59D0FA;padding:20px;color:white;font-size:14pt;">'.$user.', תודה על הרשמתך לאתר!</div> <div style=" padding:10px; ">
כדי שנוכל לאמת את המשתמש שלך, היכנס לכתובת הבאה: <BR>
<a href="'.$url.'/active/'.$active.'" style="text-decoration:none;color:#2C6A7F;">'.$url.'/active/'.$active.'</a>
<BR><BR>
בברכה, צוות האתר.
</div></div>';
$sentmail = mail($to,$subject,$message,$headers);
$pass = md5($pass);
$mysqli->query("INSERT INTO `up2_users`(`username`,`password`,`mail`,`ip`,`date`,`group`,`private`,`salt`) VALUES('$user','$pass','$mail','$ip','$date','$active','$pr','$salt')");
} } } } } } }
}else{
echo 'Error!';
} } } } } } } } } } } } } } } } } } } } } } } } } } } } } } } } } } } } } }
?>