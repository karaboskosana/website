<?php
/*
this file is for the database connection in this case:
The Database Name is easyforum
The login is root
The password is empty
You can change to macth your phpMyadmin configuration
*/
try {
	$bdd = new PDO("mysql:host=eu-cdbr-azure-north-e.cloudapp.net;dbname=digititan_forum","b106e94ad03945","8bceb7f4");
} catch (Exception $e) {
	die("Error : ".$e->getMessage());
}

?>