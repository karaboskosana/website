<?php
//Admin session script
//We will check if the Admin has a session if not he will be taken to the Form login page
//just for security
session_start();
include('../classes/classPack.php');

$admin = new Admin();
if(isset($_SESSION['AID'] )){
	 $admin->setAID($_SESSION['AID']);
	 $token = Token::GenerateToken();
	 $_SESSION["tokenClassPackApi"]= $token;
 }else
	header("Location: ../admin/");
	

?>