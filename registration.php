
<?php
session_start();
include "init.php";

	
    $fullName =$_POST['name_surname'];
    $email =$_POST['email'];
    $phone =$_POST['phone'];
    $selectProgramme =$_POST['selectProgramme'];
    $registerType =$_POST['registerType'];
    $nearestTown =$_POST['nearestTown'];

   mysql_query("INSERT INTO registration (fullName,email,phone,selectProgramme ,registerType ,nearestTown )
                        VALUES ('$fullName','$email','$phone','$selectProgramme','$registerType','$nearestTown')");


?>
