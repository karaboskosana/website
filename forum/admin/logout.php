<?php
//PHP script o log an Admin out and destroy his sesion
session_start();
$_SESSION = array();
session_destroy();
header('location:../index.php');
?>
<?php
	if(!isset($_SESSION['AID']))
		{
			header('Location:../admin/');
		}

?>