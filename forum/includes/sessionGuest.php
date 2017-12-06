<?php
//session page for the guest user
//we will check if the user connect s it will be taken to the logged space
session_start();
include('../classes/classPack.php');

$user = new User();
if(isset($_SESSION['UID'] )){
	 $user->setUID($_SESSION['UID']);
	header("Location: ../home/");
 }	
	

?>