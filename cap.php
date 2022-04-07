<?php
session_start();
function utf8_strrev($str){ preg_match_all('/./us', $str, $ar);
return join('',array_reverse($ar[0]));
} 
function henum($num) {
$n1 = array("אחת","שתיים","שלוש","ארבע","חמש","שש","שבע","שמונה","תשע");
$n2 = array("עשר","עשרים","שלושים","ארבעים","חמישים","שישים","שבעים","שמונים","תשעים");
$n3 = array("מאה","מאתיים","שלוש מאות", "ארבע מאות","חמש מאות","שש מאות","שבע מאות","שמונה מאות","תשע מאות");
if($num > 99 && $num < 1000) {
if($num%100 < 20 && $num%100 > 10) {
return $n3[floor($num/100)-1]." ו".$n1[$num%10-1]." עשרה";
}else{
if($num%10 == 0 && floor($num/10%10) == 0) {
return $n3[floor($num/100)-1];
}else{
if($num%10 == 0) {
return $n3[floor($num/100)-1]." ".$n2[$num/10%10-1];
}else{
if(floor($num/10%10) == 0) {
return $n3[floor($num/100)-1]." ו".$n1[$num%10-1];
}else{
return $n3[floor($num/100)-1]." ".$n2[floor($num/10%10)-1]." ו".$n1[$num%10-1];
} } } } 
}else{
if($num < 10 && $num > 0) {
return $n1[$num-1];
}else{
if($num < 100 && $num > 9) {
if($num%10 == 0) {
return $n2[floor($num/10)-1];
}else{
if($num < 20 && $num > 10) {
return $n1[$num%10-1]." עשרה";
}else{
return $n2[floor($num/10)-1]." ו".$n1[$num%10-1];
} }
}else{
if($num > 99 && $num < 1000) {
return $n3[floor($num/100)-1]." ".$n2[floor($num/10%10)-1]." ו".$n1[$num%10-1];
}else{
return "שגיאה";
} } } } 
}


$mkrand = rand(1,999); $_SESSION['cap'] = $mkrand;
$font = 'images/captcha.ttf'; $text = utf8_strrev(henum($mkrand));

$im = imagecreatefrompng('images/captcha.png'); $black = imagecolorallocate($im, 0, 0, 0); $size = 20;
$image_width = imagesx($im);  $image_height = imagesy($im);
$text_box = imagettfbbox($size,0,$font,$text);
$text_width = $text_box[2]-$text_box[0]; $text_height = $text_box[3]-$text_box[1];
$size = ($image_width / $text_width)*16;
if($size > 30) { $size = 30; }
$text_box = imagettfbbox($size,0,$font,$text);
$text_width = $text_box[2]-$text_box[0]; $text_height = $text_box[3]-$text_box[1];
$x = ($image_width/2) - ($text_width/2);
$y = ($image_height/2) - ($text_height/2) + 5;
imagettftext($im, $size, 0, $x, $y, $black, $font, $text);
header('Content-type: image/png');
imagepng($im);
imagedestroy($im);

?>