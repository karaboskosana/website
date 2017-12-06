<?php
//Script to log a user out and destroy his Session
session_start();
$_SESSION = array();
session_destroy();
header('location:../index.php');
?>
<?php
	if(!isset($_SESSION['UID']))
		{
			header('Location:../');
		}

?>