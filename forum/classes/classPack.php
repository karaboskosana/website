<?php
//this file is for including all the classes just once
include 'userClass.php';
include 'categoryClass.php';
include 'slikeClass.php';
include 'clikeClass.php';
include 'commentClass.php';
include 'roomClass.php';
include 'subjectClass.php';
include 'followersClass.php';
include 'notificationClass.php';
include 'adminClass.php';
include 'reportClass.php';
include 'Token.php';
include 'Smiley.php';

//A PHP script that will allow us to upload an image and change it's dimension
function SaveThumbImage($img_dir,$IMAGE, $newwidth,$newheight,$NAME,$TMPNAME){	
	$img = explode('.', $NAME );
	$image_filePath = $TMPNAME;
	$img_fileName =$IMAGE;
	$img_thumb = $img_dir . $img_fileName;
	$extension=strtolower($img[sizeof($img)-1]);

	if(in_array($extension , array('jpg','jpeg', 'png', 'bmp')))
		{
			list($gotwidth, $gotheight, $gottype, $gotattr)= getimagesize($image_filePath); 	
			if($extension=="jpg" || $extension=="jpeg" )
				{
					$src = imagecreatefromjpeg($TMPNAME);
				}
			else if($extension=="png"){
					$src = imagecreatefrompng($TMPNAME);
				}
			else{
					$src = imagecreatefromgif($TMPNAME);
				}
			list($width,$height)=getimagesize($TMPNAME);	
			$tmp=imagecreatetruecolor($newwidth,$newheight);
			imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight, $width,$height);
			imagejpeg($tmp,$img_thumb,100);										
        }
}
?>