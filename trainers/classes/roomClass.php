<?php
/*
room class is the classes tha will allow us to manipulate everything about the website ooms
by using this class we will bea able to do the following
1-	Request adding a new room ==> any user can request a new room and the Admin will approve it or not
2-	Approve room request by Admin
3-	Decline room request by Admin
4-	Delete room by Admin
5-	Return random list of the last rooms
6-	Retrun a list of room by category
7-	Return a list of room that a user is following
8-	Add new follower number for a room
9-	Add new subject number for a room
*/
class Room
{
	private $ID,$User,$Title,$Image,$rDate,$Subjects,$State,$Followers,$Category;
	
	public function getID(){ return $this->ID; }
	public function setID($ID){ $this->ID = $ID; }
	
	public function getUser(){ return $this->User; }
	public function setUser($User){ $this->User = $User; }
	
	public function getTitle(){ return $this->Title; }
	public function setTitle($Title){ $this->Title = $Title; }
	
	public function getImage(){ return $this->Image; }
	public function setImage($Image){ $this->Image = $Image; }
	
	public function getRDate(){ return $this->rDate; }
	public function setRDate($rDate){ $this->rDate = $rDate; }
	
	public function getSubjects(){ return $this->Subjects; }
	public function setSubjects($Subjects){ $this->Subjects = $Subjects; }
	
	public function getState(){ return $this->State; }
	public function setState($State){ $this->State = $State; }
	
	public function getFollowers(){ return $this->Followers; }
	public function setFollowers($Followers){ $this->Followers = $Followers; }

	public function getCategory(){ return $this->Category; }
	public function setCategory($Category){ $this->Category = $Category; }


	//FUNCTION TO REQUEST ADDING NEW ROOM
	public function RequestRoom(){
		include 'connect.php';
		$req = $bdd->prepare("INSERT INTO room(User,Title,Image,rDate,Subjects,State,Followers,Category) VALUES(:User,:Title,:Image,:rDate,:Subjects,:State,:Followers,:Category) ");
		$req->execute(array(
			"User"=>$this->getUser(),
			"Title"=>htmlspecialchars($this->getTitle()),
			"Image"=>"r.png",
			"rDate"=>$this->getRDate(),
			"Subjects"=>0,
			"State"=>"no",
			"Followers"=>0,
			"Category"=>$this->getCategory()
		));
		
	}

	//RETRUN A ROOM IFNORMATION BY IT'S ID
	public function RommInfo(){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM room WHERE ID=:ID");
		$req->execute(array(
			"ID"=>$this->getID()
		));
		$data = $req->fetch();
		$this->setID($data["ID"]);
		$this->setImage($data["Image"]);
		$this->setTitle($data["Title"]);
		$this->setSubjects($data["Subjects"]);
		$this->setFollowers($data["Followers"]);
		$this->setCategory($data["Category"]);
		$this->setRDate($data["rDate"]);
	}

	//RETURN THE ID OF ROOM
	public function ReturnRoomId(){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM room WHERE Title=:Title AND rDate=:rDate AND User=:User");
		$req->execute(array(
			"Title"=>$this->getTitle(),
			"rDate"=>$this->getRDate(),
			"User"=>$this->getUser()
		));
		$data = $req->fetch();
		$this->setID($data["ID"]);
	}

	//UPDATE IMAGE
	public function UpdateImage(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE room SET Image=:Image WHERE ID=:ID");
		$req->execute(array(
			"Image"=>$this->getID().".jpg",
			"ID"=>$this->getID()
		));
		
	}

	//FUNCTION TO ACCEPT ROOM BY ADMIN
	public function AdminAcceptRoom(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE room SET State=:State WHERE ID=:ID");
		$req->execute(array(
			"State"=>"yes",
			"ID"=>$this->getID()
		));
		//
	}

	//FUNCTION TO DELETE ROOM BY ADMIN
	public function AdminDeleteRoom(){
		include 'connect.php';
		$req1 = $bdd->prepare("SELECT * FROM subject  WHERE Room=:Room");
		$req1->execute(array(	
			"Room"=>$this->getID()
		));
		while($data= $req1->fetch()){
			$subject = new Subject();
			$subject->setSID($data["SID"]);
			$subject->DeleteSubject();
		}

		$req = $bdd->prepare("DELETE FROM room  WHERE ID=:ID");
		$req->execute(array(	
			"ID"=>$this->getID()
		));

		$Category = new Category();
		$Category->RoomsNumber("minus");
	}

	//FUNCTION TO RETUN THE LIST OF ROOMS
	public function RoomList($first,$second){
		include 'connect.php';
			$json = "";
			$req = $bdd->prepare("SELECT * FROM category c JOIN room r ON r.Category=c.CATID WHERE state='yes' ORDER BY ID DESC LIMIT ".$first." , ".$second);
			$req->execute();
			if ($req->rowCount()==0) 
				return "empty";
			else{
				$json = array();
				$followers= new Followers();
				while ($data = $req->fetch()) {
					$followers->setRoom($data['ID']);
					array_push($json, 
								 array( "ID"=>$data['ID'],
								 		"Title"=>$data['Title'],
								 		"Subjects"=>$data['Subjects'],
								 		"rDate"=>$data['rDate'],
								 		"State"=>$data['State'],						 		
								 		"Image"=>$data['Image'],
								 		"Followers"=>$data['Followers'],
								 		"Name"=>$data['Name'],
								 		"Category"=>$data["Category"]
								  )
					);
				}
				return json_encode($json);
			}
	}


	//FUNCTION TO RETUN THE LIST OF ROOMS BY CATEGORY
	public function RoomListByCategory($fisrt,$second){
		include 'connect.php';
			$json = "";
			$req = $bdd->prepare("SELECT * FROM category c JOIN room r ON r.Category=c.CATID WHERE Category=:Category AND state='yes' ORDER BY ID DESC LIMIT ".$fisrt." , ".$second);
			$req->execute(array(
				"Category"=>$this->getCategory()
			));
			if ($req->rowCount()==0) 
				return "empty";
			else{
				$json = array();
				while ($data = $req->fetch()) {
					array_push($json, 
								 array( "ID"=>$data['ID'],
								 		"Title"=>$data['Title'],
								 		"Subjects"=>$data['Subjects'],
								 		"rDate"=>$data['rDate'],
								 		"State"=>$data['State'],						 		
								 		"Image"=>$data['Image'],
								 		"Followers"=>$data['Followers'],
								 		"Name"=>$data['Name'],
								 		"Category"=>$data["Category"]
								  )
					);
				}
				return json_encode($json);
			}
	}


	//FUNCTION TO ADD FOLLOWER NUMBER OR MINUS 
	public function RoomFollowersNumber($operation){
		include 'connect.php';
		$number = 0;
		
		if ($operation=="plus")
			$number = 1;
		else if ($operation=="minus") 
			$number = -1;	

		$req = $bdd->prepare("UPDATE room SET Followers=Followers+:cNumber WHERE ID=:ID");
		$req->execute(array(
			"cNumber"=>$number,
			"ID"=>$this->getID()
		));
		
	}


	//FUNCTION TO ADD FOLLOWER NUMBER OR MINUS 
	public function RoomSubjectsNumber($operation){
		include 'connect.php';
		$number = 0;
		
		if ($operation=="plus")
			$number = 1;
		else if ($operation=="minus") 
			$number = -1;	

		$req = $bdd->prepare("UPDATE room SET Subjects=Subjects+:cNumber WHERE ID=:ID");
		$req->execute(array(
			"cNumber"=>$number,
			"ID"=>$this->getID()
		));
		
	}




	//CHECK FOR CATEGORY
	public function CheckRoom(){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM room WHERE ID=:ID");
		$req->execute(array(
			'ID'=>$this->getID()
		));
		return $req->rowCount();
	}

	//A LIST OF ROOMS THAT A USER IS FOLLOWING
	public function RoomsListByUser($first,$second,$User){
		include 'connect.php';
		$json = "";
		$req = $bdd->prepare("SELECT * FROM category c JOIN room r JOIN followers f ON f.Room=r.ID AND r.Category=c.CATID WHERE f.User=:User ORDER BY FID DESC LIMIT ".$first." , ".$second);
		$req->execute(array(
			"User"=>$User
		));

		if($req->rowCount()==0)
			return "empty";
		else{
			$json = array();
				while ($data = $req->fetch()) {
					array_push($json, 
								 array( "ID"=>$data['ID'],
								 		"Title"=>$data['Title'],
								 		"Subjects"=>$data['Subjects'],
								 		"rDate"=>$data['rDate'],
								 		"State"=>$data['State'],						 		
								 		"Image"=>$data['Image'],
								 		"Followers"=>$data['Followers'],
								 		"Name"=>$data['Name'],
								 		"Category"=>$data["Category"]
								  )
					);
				}
				return json_encode($json);
		}
	}
}

?>