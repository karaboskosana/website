<?php
/*
Followers class is the class that will us to  handle the users that are following a certain room
by using this class we will be able to do the followin
1-	Add a new follower to a room
2-	Delete a follower from a room
3-	Return a list of a room followers

*/
class Followers
{
	private $FID,$User,$Room;
	
	public function getFID(){ return $this->FID; }
	public function setFID($FID){ $this->FID = $FID; }
	
	public function getUser(){ return $this->User; }
	public function setUser($User){ $this->User = $User; }
	
	public function getRoom(){ return $this->Room; }
	public function setRoom($Room){ $this->Room = $Room; }

	//INSERT A FOLLOW
	public function InsertFollow(){
		include 'connect.php';
		$req = $bdd->prepare("INSERT INTO followers(User,Room) VALUES(:User,:Room)");
		$req->execute(array(
			"User"=>$this->getUser(),
			"Room"=>$this->getRoom()
		));	
	}

	//DELETE FOLLOW
	public function DeleteFollow(){
		include 'connect.php';
		$req = $bdd->prepare("DELETE FROM followers WHERE User=:User AND Room=:Room");
		$req->execute(array(
			"User"=>$this->getUser(),
			"Room"=>$this->getRoom()
		));	
		
	}

	//CHECK FOLLOW
	public function CheckFollow(){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM followers WHERE User=:User AND Room=:Room");
		$req->execute(array(
			"User"=>$this->getUser(),
			"Room"=>$this->getRoom()
		));
		return $req->rowCount();
		 
	}

	//FOLLOWERS LIST
	public function FollowersList($first,$second){
		include 'connect.php';
		$json = "";
		$req = $bdd->prepare("SELECT * FROM followers f JOIN user u ON u.UID = f.User WHERE Room=:Room ORDER BY FID DESC LIMIT ".$first." , ".$second);
		$req->execute(array(
			"Room"=>$this->getRoom()
		));

		if($req->rowCount()==0)
			return "empty";
		else{
			$json = array();
			while ($data = $req->fetch()) {
				array_push($json, array(
					"UID"=>$data["UID"],
					"FullName"=>$data["FullName"],
					"Avatar"=>$data["Avatar"],
				));
			}
			return json_encode($json);
		}
	}

	
}

?>