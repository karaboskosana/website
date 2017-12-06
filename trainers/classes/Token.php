<?php 
/*
	this class is for creating a tokect to prevent the Cross Site Request Forgery Vulnerability..
	it will provide  the website some sort of protection fro hackers and attackers
	for further informaton of this problem please visit this website
	http://en.wikipedia.org/wiki/Cross-site_request_forgery
*/
class Token
{
	public static function GenerateToken()
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$key = str_shuffle($chars);	
		return $_SESSION["token"] = $key;
	}


	public static function CheckToken($token){
		if (isset($_SESSION["token"] ) && $_SESSION["token"] === $token) {			
			return true;
		}
		return false;
	}


}


?>