<?php
/*
clike class or Comment like
This class is for manipulating the likes of a comment
By using this class we will be able to do the following
1-	Add a new like
2-	Delete a like
4-	Check if the users already likes the comment
5-	Return a list of users who likes a comment	
*/


class Clike
{
		private $ID,$User,$Comment;
		public function getID(){ return $this->ID; }
		public function setID($ID){ $this->ID = $ID; }
		
		public function getUser(){ return $this->User; }
		public function setUser($User){ $this->User = $User; }
		
		public function getComment(){ return $this->Comment; }
		public function setComment($Comment){ $this->Comment = $Comment; }

		//FUNCTION TO ADD A LIKE
		public function InsertLike(){
			include 'connect.php';
			$req = $bdd->prepare("INSERT INTO clike(User,Comment) VALUES(:User,:Comment)");
			$req->execute(array(
				"User"=>$this->getUser(),
				"Comment"=>$this->getComment()
			));		
				
		}		

		//DELETE LIKE
		public function DeleteLike(){
			include 'connect.php';
			$req = $bdd->prepare("DELETE FROM clike WHERE User=:User AND Comment=:Comment");
			$req->execute(array(
				"User"=>$this->getUser(),
				"Comment"=>$this->getComment()
			));	
			
		}

		//RETURN PEOPLE WHO LIKED
		public function LikersList(){
			include 'connect.php';
			$json = "";
			$req = $bdd->prepare("SELECT * FROM clike l JOIN user u WHERE u.UID=l.User WHERE ID=:ID ORDER BY ID DESC LIMIT 0,10 ");
			$req->execute(array(
				"ID"=>$this->getID()
			));
			if ($req->rowCount()==0) 
				echo "empty";
			else{
				$json = array();
				while ($data = $req->fetch()) {
					array_push($json,
								 array( "ID"=>$data['ID'],
								 		"Comment"=>$data['Comment'],						 		
								 		"UID"=>$data['UID'],
								 		"FullName"=>$data['FullName'],
								 		"Image"=>$data['Image']
								  )
					);
				}
				echo $json;
			}
		}

		//CHECK IF A USER IS ALREADY IN LOVE WITH A COMMENT :D
		public function CheckLike(){
			include 'connect.php';
			$req = $bdd->prepare("SELECT *  FROM clike WHERE User=:User AND Comment=:Comment");
			$req->execute(array(
				"User"=>$this->getUser(),
				"Comment"=>$this->getComment()
			));
			return $req->rowCount();
		}


}

?>