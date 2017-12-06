<?php
/*
user class is the class tha will allow us to manipulate all users
By using this class you will be able to do the following
1-	Create a new user
2-	Connect a user to the website
3-	Update the user info email and name
4-	Return a user information by it's ID
5-	Change the User Avatar
*/

class User
{	
	private $UID,$FullName,$Avatar,$FacebookId,$Email,$Password,$Notification,$Status,$PasswordToken;
	
	public function getUID(){ return $this->UID; }
	public function setUID($UID){ $this->UID = $UID; }
	
	public function getFullName(){ return $this->FullName; }
	public function setFullName($FullName){ $this->FullName = $FullName; }
	
	public function getAvatar(){ return $this->Avatar; }
	public function setAvatar($Avatar){ $this->Avatar = $Avatar; }
	
	public function getFacebookId(){ return $this->FacebookId; }
	public function setFacebookId($FacebookId){ $this->FacebookId = $FacebookId; }
	
	public function getEmail(){ return $this->Email; }
	public function setEmail($Email){ $this->Email = $Email; }

	public function getPassword(){ return $this->Password; }
	public function setPassword($Password){ $this->Password = $Password; }

	public function getNotification(){ return $this->Notification; }
	public function setNotification($Notification){ $this->Notification = $Notification; }

	public function getStatus(){ return $this->Status; }
	public function setStatus($Status){ $this->Status = $Status; }

	public function getPasswordToken(){ return $this->PasswordToken; }
	public function setPasswordToken($PasswordToken){ $this->PasswordToken = $PasswordToken; }


	//FUNCTION YO INSERT USER TO DATABASE
	public function InsertUser(){
		include 'connect.php';
		$req = $bdd->prepare("INSERT into user(FullName,Avatar,FacebookId,Email,Password,Notification,Status) VALUES(:FullName,:Avatar,:FacebookId,:Email,:Password,:Notification,'enable') ");
		$req->execute(array(
			"FullName"=>$this->getFullName(),
			"Avatar"=>$this->getAvatar(),
			"FacebookId"=>$this->getFacebookId(),
			"Email"=>$this->getEmail(),
			"Password"=>$this->getPassword(),
			"Notification"=>0
		));	
		echo "success";
	}

	//FUNCTION TO RETURN THE USER ID WHEN IT"S CREATED
	public function ReturnUserId(){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM user WHERE FullName=:FullName AND Avatar=:Avatar AND FacebookId=:FacebookId AND Email=:Email AND Password=:Password) ");
		$req->execute(array(
			"FullName"=>$this->getFullName(),
			"Avatar"=>$this->getAvatar(),
			"FacebookId"=>$this->getFacebookId(),
			"Email"=>$this->getEmail(),
			"Password"=>$this->getPassword()
		));	
		$data = $req->fetch();
		return $data["UID"];
	}

	//Update The Status of the user
	//The Admin Can make This action to prevent spammers
	public function UpdateStatus(){
		include 'connect.php';
		$req =$bdd->prepare('UPDATE user SET Status=:Status WHERE UID=:UID');
		$req->execute(array(
			'Status'=>$this->getStatus(),
			'UID'=>$this->getUID()
		));
	}



	//FUNCTION CHECK USER EMAIL IF EXISTS
	public function CheckEmail(){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM user WHERE Email=:Email");
		$req->execute(array(
			"Email"=>$this->getEmail()
		));
		return $req->rowCount();
	}

	//FUNCTION TO CHECK IF USER FACEBOOK ID EXISTS
	public function CheckFacebookID(){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM user WHERE FacebookId=:FacebookId");
		$req->execute(array(
			"FacebookId"=>$this->getFacebookId()
		));
		return $req->rowCount();
	}


	//UPDATE PASSWORD TOKEN
	//this will be used when a user has forgot his PAssword 
	public function UpdatePasswordToken(){
		include 'connect.php';
		$r = $bdd->prepare("SELECT * FROM user WHERE Email=:Email");
		$r->execute(array(
			'Email'=>$this->getEmail()
		));
		$data = $r->fetch();
		$UID = $data["UID"];

		$req =$bdd->prepare('UPDATE user SET PasswordToken=:PasswordToken WHERE Email=:Email');
		$req->execute(array(
			'PasswordToken'=>$this->getPasswordToken(),
			'Email'=>$this->getEmail()
		));

		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$header .= "From:noreply@nabil-lahssine.com" ;
		$message = 'You have asked to reset your password please follow this link to do so <a href="'.$_SERVER['HTTP_HOST'].'/demo/easyforum/reset.php?Key='.$this->getPasswordToken().'&UID='.$UID.'&Email='.$this->getEmail().'">Click me</a>.';
		mail($this->getEmail(),"EasyForum: Password reset",$message,$header);


	}

	//this function will check for the generated key on the user
	public function CheckUserGeneratedKey($UID,$Email,$Token){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM user WHERE UID=:UID AND Email=:Email AND PasswordToken=:PasswordToken");
		$req->execute(array(
			"UID"=>$UID,
			"Email"=>$Email,
			"PasswordToken"=>$key
		));
		return $req->rowCount();
	}

	//return the user tocken
	public function ReturnUserToken($UID){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM user WHERE UID=:UID");
		$req->execute(array(
			"UID"=>$UID		
		));
		$data = $req->fetch();
		return $data["PasswordToken"];
	}

	//function to update the forgotten password
	public function UpdateForgottenPassword(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE user SET Password=:Password , PasswordToken='' WHERE UID=:UID");
		$req->execute(array(
			"Password"=>sha1($this->getPassword()),
			"UID"=>$this->getUID()
		));
	}



	//FUNCTION O CONNECT THE USER
	public function ConnectUser(){
		include 'connect.php';
		$json ="";
		$req = $bdd->prepare("SELECT * FROM user WHERE Email=:Email AND Password=:Password AND Status=:Status ");
		$req->execute(array(
			"Email"=>$this->getEmail(),
			"Password"=>$this->getPassword(),
			'Status'=>'enable'
		));
		if ($req->rowCount()==0) {
			echo "error";
		}else{
			$data = $req->fetch();
			$_SESSION["UID"]=$data['UID'];
			$_SESSION["FacebookId"]=$data['FacebookId'];
			$_SESSION["FullName"]=$data['FullName'];
			$_SESSION["Avatar"]=$data['Avatar'];
			$_SESSION["Email"]=$data['Email'];
			$_SESSION["Notification"]=$data["Notification"];
			//this part will create a SESSION variable that will have a list of rooms that a user is subscribed too
			//It will help us to push some notifications of the new subjets (of those rooms) to the user
			$_SESSION['UserRoomSubscription'] ="";
			$request = $bdd->prepare("SELECT * FROM followers WHERE User=:User");
			$request->execute(array(
				'User'=>$data["UID"]
			));
			while ($dataF = $request->fetch()) {
				$_SESSION['UserRoomSubscription'] .=$dataF["Room"].",";
			}
			echo "good";
		}
		
	}

	
	//FUNCTION TO RETURN USER INFO BY HIS FACEBOOK ID
	public function UserInfoByFacebookId(){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM user WHERE FacebookId=:FacebookId");
		$req->execute(array(
			"FacebookId"=>$this->getFacebookId()
		));
		if ($req->rowCount()!=0) {
			$data = $req->fetch();
			$json = array(
				"state"=>"true",
				"UID"=>$data['UID'],
				"FacebookId"=>$data['FacebookId'],
				"FullName"=>$data['FullName'],
				"Avatar"=>$data['Avatar'],
				"Email"=>$data['Email'],
				"Notification"=>$data["Notification"]
			);
			echo json_encode($json);
		}else
			echo json_encode(array("state"=>"No user Found"));
		
	}

	//FUNCTION TO RETURN USER INFO BY HIS ID
	public function UserInfoById(){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM user WHERE UID=:UID");
		$req->execute(array(
			"UID"=>$this->getUID()
		));
		if ($req->rowCount()!=0) {
			$data = $req->fetch();
			$json = array(
				"state"=>"true",
				"UID"=>$data['UID'],
				"FacebookId"=>$data['FacebookId'],
				"FullName"=>$data['FullName'],
				"Avatar"=>$data['Avatar'],
				"Email"=>$data['Email'],
				"Notification"=>$data["Notification"]
			);
			echo json_encode($json);
		}else
			echo json_encode(array("state"=>"No user Found"));
		
	}


	//FUNCTION TO UPDATE FACEBOOK USER PICTURE
	public function UpdateFacebookUserPicture(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE user SET Avatar=:Avatar WHERE FacebookId=:FacebookId");
		$req->execute(array(
			"Avatar"=>$this->getAvatar(),
			"FacebookId"=>$this->getFacebookId()
		));
	}


	//FUNCTION TO UPDATE USER INFORMATIONS
	public function UpdateUserInfo(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE user SET FullName=:FullName , Email=:Email  WHERE UID=:UID");
		$req->execute(array(
			"FullName"=>$this->getFullName(),
			"Email"=>$this->getEmail(),
			"UID"=>$this->getUID()
		));
		$_SESSION["FullName"] = $this->getFullName();
		$_SESSION["Email"] = $this->getEmail();
	}

	//Update The User Password
	public function UpdateUserPassword($oldpassword){
		include 'connect.php';
		$r = $bdd->prepare("SELECT * FROM user WHERE UID=:UID");
		$r->execute(array(
			"UID"=>$this->getUID()
		));
		$data = $r->fetch();
		if (sha1($oldpassword) == $data["Password"]) {//we check if the user has provided the right password so it will be updated
			$req = $bdd->prepare("UPDATE user SET  Password=:Password WHERE UID=:UID");
			$req->execute(array(
				"Password"=>$this->getPassword(),
				"UID"=>$this->getUID()
			));	
		}else
			echo "nomatch";

			
	}

	//Update the user Avatar
	public function UpdateUserAvatar(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE user SET  Avatar=:Avatar WHERE UID=:UID");
		$req->execute(array(
			"Avatar"=>$this->getUID().".jpg",
			"UID"=>$this->getUID()
		));	
	}

	//function to add a new notification
	public function NotificationAdd(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE user SET Notification=Notification+1 WHERE UID=:UID");
		$req->execute(array(
			"UID"=>$this->getUID()
		));
	}

	//function to delete notifications
	public function NotificationRead(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE user SET Notification=0 WHERE UID=:UID");
		$req->execute(array(
			"UID"=>$this->getUID()
		));
	}

	//check if for notifications
	public function checkNotification($UID){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM user WHERE UID=:UID");
		$req->execute(array(
			"UID"=>$UID
		));

		$data = $req->fetch();
		echo $data["Notification"];
	}

	//This functio is for Admins to delete a user By it's ID
	public function DeleteByUID(){
		include 'connect.php';
		$req =$bdd->prepare('DELETE FROM user WHERE UID=:UID');
		$req->execute(array(
			'UID'=>$this->getUID()
		));
	}



}

?>