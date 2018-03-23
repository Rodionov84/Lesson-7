<?php

$text = substr($_GET['first_name'], 0, 10);
$text1 = substr($_GET['second_name'], 0, 10);
$percent = floatval($_GET['persent']);
/*$percent = $_GET['percent'];
var_dump($percent);*/
$image = imagecreatetruecolor(745,530);
$backColor = imagecolorallocate($image, 255, 224, 221);
$textColor = imagecolorallocate($image, 0, 0, 0);
$boxFile = __DIR__ . '/image.png'; 
if (!file_exists($boxFile)) {
	echo 'File not found!';
	exit;
}
$imBox = imagecreatefrompng($boxFile);

imagefill($image, 0, 0, $backColor);
imagecopy($image, $imBox, 0, 0, 0, 0, 750, 530);

$fontFile = __DIR__ . '/lobster.ttf';
if (!file_exists($fontFile)) {
	echo 'File with fonts not found!';
	exit;
}
imagettftext($image, 30, 0, 180, 268, $textColor, $fontFile, $text);
imagettftext($image, 30, 0, 380, 268, $textColor, $fontFile, $text1);
imagettftext($image, 30, 0, 180, 468, $textColor, $fontFile, $percent . "%");
header('content-Type: image/png');

imagepng($image);
imagedestroy($image);

?>