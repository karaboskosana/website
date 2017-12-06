<?php

try {
	$bdd = new PDO("mysql:host=eu-cdbr-azure-west-b.cloudapp.net;dbname=trainers","bd66f373d35b12","6d4dd2ba");
} catch (Exception $e) {
	die("Error : ".$e->getMessage());
}

?>