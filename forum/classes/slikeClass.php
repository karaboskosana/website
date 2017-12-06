<?php
/*
slike class or subject like
This class is for manipulating the likes of a subject
By using this class we will be able to do the following
1-	Add a new like
2-	Delete a like
4-	Check if the users already likes the subject
5-	Return a list of users who likes a subject
*/


class Slike
{
		private $ID,$User,$Subject;
		public function getID(){ return $this->ID; }
		public function setID($ID){ $this->ID = $ID; }
		
		public function getUser(){ return $this->User; }
		public function setUser($User){ $this->User = $User; }
		
		public function getSubject(){ return $this->Subject; }
		public function setSubject($Subject){ $this->Subject = $Subject; }

		//FUNCTION TO ADD A LIKE
		public function InsertLike(){
			include 'connect.php';
			$req = $bdd->prepare("INSERT INTO slike(User,Subject) VALUES(:User,:Subject)");
			$req->execute(array(
				"User"=>$this->getUser(),
				"Subject"=>$this->getSubject()
			));		
				
		}		

		//DELETE LIKE
		public function DeleteLike(){
			include 'connect.php';
			$req = $bdd->prepare("DELETE FROM slike WHERE User=:User AND Subject=:Subject");
			$req->execute(array(
				"User"=>$this->getUser(),
				"Subject"=>$this->getSubject()
			));	
			
		}

		//RETURN PEOPLE WHO LIKED
		public function LikersList(){
			include 'connect.php';
			$json = "";
			$req = $bdd->prepare("SELECT * FROM slike l JOIN user u WHERE u.UID=l.User WHERE ID=:ID ORDER BY ID DESC LIMIT 0,10 ");
			$req->execute(array(
				"ID"=>$this->getID()
			));
			if ($req->rowCount()==0) 
				return "empty";
			else{
				$json = array();
				while ($data = $req->fetch()) {
					array_push($json,
								 array( "ID"=>$data['ID'],
								 		"Subject"=>$data['Subject'],						 		
								 		"UID"=>$data['UID'],
								 		"FullName"=>$data['FullName'],
								 		"Avatar"=>$data['Avatar']
								  )
					);
				}
				return $json;
			}
		}

		//CHEKING IF A CERTAIN USER LIKES A CERTAIN SUBJECT
		public function CheckLike(){
			include 'connect.php';
			$req = $bdd->prepare("SELECT *  FROM slike WHERE User=:User AND Subject=:Subject");
			$req->execute(array(
				"User"=>$this->getUser(),
				"Subject"=>$this->getSubject()
			));
			return $req->rowCount();
		}


}

?>