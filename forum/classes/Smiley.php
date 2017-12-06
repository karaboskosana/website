
<?php
//This script for the smiley thing
function smiley($text){
	$text =stripcslashes(" ".$text." ");

	//$text= preg_replace('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', '<a href="$1" class="linkTe" target="_blank">$1</a>', $text);
	
	$text = preg_replace("# :D #siU","<div  class='smileypng' style='background-position: -18px 0px'></div>",$text);
	$text = preg_replace("# :d #siU","<div  class='smileypng' style='background-position: -18px 0px'></div>",$text);
	$text = preg_replace("# =D #siU","<div  class='smileypng' style='background-position: -18px 0px'></div>",$text);
	$text = preg_replace("# =d #siU","<div  class='smileypng' style='background-position: -18px 0px'></div>",$text);



	$text = preg_replace("# ;\) #siU","<div class='smileypng' style='background-position:-130px 0px'></div>",$text);

	$text = preg_replace("# :\) #siU","<div class='smileypng' style='background-position: 0px 0px'></div>",$text);
	$text = preg_replace("# :\] #siU","<div class='smileypng' style='background-position: 0px 0px'></div>",$text);
	$text = preg_replace("# =\) #siU","<div class='smileypng' style='background-position: 0px 0px'></div>",$text);
	
	$text = preg_replace("# :\| #siU","<div class='smileypng' style='background-position: px px'></div>",$text);

	$text = preg_replace("# :\( #siU","<div class='smileypng' style='background-position: -36px 0px'></div>",$text);
	
	$text = preg_replace("# :o #siU","<div class='smileypng' style='background-position: -92px 0px'></div>",$text);
	$text = preg_replace("# :O #siU","<div class='smileypng' style='background-position: -92px 0px'></div>",$text);
	$text = preg_replace("# :0 #siU","<div class='smileypng' style='background-position: -92px 0px'></div>",$text);

	$text = preg_replace("# :8 #siU","<div class='smileypng' style='background-position: -149px 0px'></div>",$text);
	$text = preg_replace("# 8-\) #siU","<div class='smileypng' style='background-position: -149px 0px'></div>",$text);

	$text = preg_replace("# :p #siU","<div class='smileypng' style='background-position: -111px 0px'></div>",$text);
	$text = preg_replace("# :P #siU","<div class='smileypng' style='background-position: -111px 0px'></div>",$text);

	$text = preg_replace("# :\/ #siU","<div class='smileypng' style='background-position: -55px 0px'></div>",$text);
	

	$text = preg_replace("# 3:\) #siU","<div  class='smileypng' style='background-position: 0px -18px'></div>",$text);
	$text = preg_replace("# 3:\] #siU","<div  class='smileypng' style='background-position: 0px -18px'></div>",$text);

	$text = preg_replace("# :'\( #siU","<div class='smileypng' style='background-position: -73px 0px'></div>",$text);

	$text = preg_replace("# O:\) #siU","<div class='smileypng' style='background-position: -19px -18px; width:16px;'></div>",$text);


	$text= preg_replace("# :3 #siU","<div class='smileypng' style='background-position:-38px -18px'></div>",$text);
	$text = preg_replace("# &gt;:\( #siU","<div class='smileypng' style='background-position: -55px -18px'></div>",$text);
	$text = preg_replace("# &lt;:o #siU","<div class='smileypng' style='background-position: -93px -18px'></div>",$text);
	$text = preg_replace("# o.O #siU","<div class='smileypng' style='background-position: -74px -18px'></div>",$text);

	$text = preg_replace("# B\| #siU","<div class='smileypng' style='background-position: -111px -18px'></div>",$text);

	$text = preg_replace("# &lt;3 #siU","<div class='smileypng' style='background-position: -129px -18px'></div>",$text);

	$text = preg_replace("# \^\_\^ #siU","<div class='smileypng' style='background-position: -148px -18px'></div>",$text);

	$text = preg_replace("# :\* #siU","<div class='smileypng' style='background-position: -165px -18px'></div>",$text);

	$text = preg_replace("# :v #siU","<div class='smileypng' style='background-position: 0px -37px'></div>",$text);


	return ($text);
}

function LinkMe($text){
	$text= preg_replace('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', '<a href="$1" class="linkTe" target="_blank">$1</a>', $text);
	return $text;
}


 ?>