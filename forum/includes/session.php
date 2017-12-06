<?php
//Session script fot users
//we will check if a ssession is started if ot the user will be taken to the Guest space
session_start();
include('../classes/classPack.php');

$user = new User();
if(isset($_SESSION['UID'] )){
	 $user->setUID($_SESSION['UID']);
	 $token = Token::GenerateToken();
	 $_SESSION["tokenClassPackApi"] = $token;
 }else
	header("Location: ../");
	

?>