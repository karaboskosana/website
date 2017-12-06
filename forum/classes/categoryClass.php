<?php
//ONLY ADMIN WHO WILL HAVE HAND ON THIS
/*
By using this class the admin will be able to do the following
1-	Add a new category
2-	Delete a Category
3-	Return a list of categories on 3 Diferrent spaces: Admin space - Guest Space - User Space
4-	Add new Room number for a category
*/
class Category
{
	private $CATID,$Name,$Image,$Rooms;
	
	public function getCATID(){ return $this->CATID; }
	public function setCATID($CATID){ $this->CATID = $CATID; }
	
	public function getName(){ return $this->Name; }
	public function setName($Name){ $this->Name = $Name; }
	
	public function getImage(){ return $this->Image; }
	public function setImage($Image){ $this->Image = $Image; }
	
	public function getRooms(){ return $this->Rooms; }
	public function setRooms($Rooms){ $this->Rooms = $Rooms; }

	//ADD NEW CATEGORY
	public function InsertCategory(){
		include 'connect.php';
		$req = $bdd->prepare("INSERT INTO category(Name,Image,Rooms) VALUES(:Name,:Image,:Rooms)");
		$req->execute(array(
			"Name"=>$this->getName(),
			"Image"=>$this->getName().".jpg",
			"Rooms"=>0
			));
		
	}

	//DELETE CATEGORY
	public function DeleteCategory(){
		include 'connect.php';				
		//this request will delete all the rooms that are under the deleted category and it will delete all the subjects also
		$req1 = $bdd->prepare("SELECT *  FROM room WHERE Category=:Category");
		$req1->execute(array(
			"Category"=>$this->getCATID()
		));
		while ($data = $req1->fetch()) {
			$room = new Room();
			$room->setID($data["ID"]);
			$room->AdminDeleteRoom();
		}

		//this request will delete the category form the database
		$req = $bdd->prepare("DELETE FROM category WHERE CATID=:CATID");
		$req->execute(array(
			"CATID"=>$this->getCATID()
		));

	}

	//ADD NEW ROOM NUMBER TO THE CATEGORY
	public function RoomsNumber($operation){
		include 'connect.php';
		$number = 1;
		if ($operation=="plus")
			$number = 1;
		else if ($operation=="minus")
			$number = -1;		
				

		$req = $bdd->prepare("UPDATE category SET Rooms=Rooms+:cNumber WHERE CATID=:CATID");
		$req->execute(array(
			"cNumber"=>$number,
			"CATID"=>$this->getCATID()
		));		
	}


	//CHECK FOR CATEGORY
	public function CheckCategory(){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM category WHERE CATID=:CATID");
		$req->execute(array(
			'CATID'=>$this->getCATID()
		));
		return $req->rowCount();
	}

	//RETURN A LIST OF CATEGORIES 
	public function CategoryList($first,$second){
		include 'connect.php';
			$json = "";
			$req = $bdd->prepare("SELECT * FROM category ORDER BY CATID DESC LIMIT ".$first." , ".$second);
			$req->execute();
			if ($req->rowCount()==0) 
				return "empty";
			else{
				$json = array();
				$followers= new Followers();
				while ($data = $req->fetch()) {
					array_push($json, 
								 array( "CATID"=>$data['CATID'],
								 		"Image"=>$data['Image'],
								 		"Rooms"=>$data['Rooms'],
								 		"Name"=>$data['Name']
								  )
					);
				}
				return json_encode($json);
			}
	}

}

?>