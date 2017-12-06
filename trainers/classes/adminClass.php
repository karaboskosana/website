<?php 
/*
admin class is the class that will allow us to manipulating Website Admins
By using this class we will be ableto do the following
1-	Create a new admin
2-	Delete an admin
3-	Return a list of admins
4-	Update admin information and profile: Avatar Name Email Password
5-	Change admin role : Master or Normal
*/
class Admin
{
	private $AID,$aFullName,$aEmail,$aPassword,$aRole,$aAvatar;
	
	public function getAID(){ return $this->AID; }
	public function setAID($AID){ $this->AID = $AID; }
	
	public function getAFullName(){ return $this->aFullName; }
	public function setAFullName($aFullName){ $this->aFullName = $aFullName; }
	
	public function getAEmail(){ return $this->aEmail; }
	public function setAEmail($aEmail){ $this->aEmail = $aEmail; }

	public function getAPassword(){ return $this->aPassword; }
	public function setAPassword($aPassword){ $this->aPassword = $aPassword; }

	public function getARole(){ return $this->aRole; }
	public function setARole($aRole){ $this->aRole = $aRole; }
	
	public function getAAvatar(){ return $this->aAvatar; }
	public function setAAvatar($aAvatar){ $this->aAvatar = $aAvatar; }

	//functon to add a new Admin 
	public function InsertAdmin(){
		include 'connect.php';
		$req = $bdd->prepare("INSERT INTO admin(aFullName,aEmail,aPassword,aRole,aAvatar) VALUES(:aFullName,:aEmail,:aPassword,:aRole,:aAvatar)");
		$req->execute(array(
			"aFullName"=>$this->getAFullName(),
			"aEmail"=>$this->getAEmail(),
			"aPassword"=>$this->getAPassword(),
			"aRole"=>$this->getARole(),
			"aAvatar"=>"normal.png"
		));
	}

	//UPDATE ADMIN INFO : NAME EMAIL AND PASSWORD
	public function UpdateAdminInfo($oldPassword){
		include 'connect.php';
		//we wiil check if the user has changed the password or not
		if (trim($this->getAPassword())=="") {
			$this->setAPassword($_SESSION["aPassword"]);
		}else
			$this->setAPassword(sha1($this->getAPassword()));
		//We will check if the old password is matched to know that it's really the admins who wants to update the password and not some ****
		if (trim($oldPassword)!="" && sha1($oldPassword)!=$_SESSION["aPassword"]) {
			$this->setAPassword($_SESSION["aPassword"]);
			echo "nomatch";		
		}	

		$req = $bdd->prepare("UPDATE admin SET aFullName=:aFullName , aEmail=:aEmail , aPassword=:aPassword WHERE AID=:AID");
		$req ->execute(array(
			"aFullName"=>$this->getAFullName(),
			"aEmail"=>$this->getAEmail(),
			"aPassword"=>$this->getAPassword(),
			"AID"=>$this->getAID()
		));
		$_SESSION["aFullName"]=$this->getAFullName();
		$_SESSION["aEmail"]=$this->getAEmail();
		$_SESSION["aPassword"]=$this->getAPassword();
	}



	//Change Admin Role
	//There are two admin roles 
	//Normal : can manage only subjects rooms categories and users
	//Master : can manage everything even Admin he can delete and Add admins
	public function ChangeAdminRole(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE admin SET aRole=:aRole WHERE AID=:AID");
		$req ->execute(array(
			"aRole"=>$this->getARole(),
			"AID"=>$this->getAID()
		));
		$_SESSION["aRole"]=$this->getARole();
	}

	//DELETE an ADMIN By another Master Admin
	public function DeleteAdmin(){
		include 'connect.php';
		$req = $bdd->prepare("DELETE FROM admin WHERE AID=:AID");
		$req ->execute(array(
			"AID"=>$this->getAID()
		));
	}

	//This function will connect the admin to the Admin Panel depending on his Role
	public function ConnectAdmin(){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM admin WHERE aEmail=:aEmail AND aPassword=:aPassword");
		$req->execute(array(
			"aEmail"=>$this->getAEmail(),
			"aPassword"=>$this->getAPassword(),
		));
		if ($req->rowCount()==0) {
			echo "error";
		}else{
			$data = $req->fetch();		
			$_SESSION["AID"]=$data["AID"] ;
			$_SESSION["aFullName"]=$data["aFullName"] ;
			$_SESSION["aEmail"]=$data["aEmail"] ;
			$_SESSION["aPassword"]=$data["aPassword"] ;
			$_SESSION["aRole"]=$data["aRole"] ;
			$_SESSION["aAvatar"]=$data["aAvatar"] ;
			echo "good";
		}

	}

	//Change an Admin Avatar
	public function UpdateAdminAvatar(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE admin SET  aAvatar=:aAvatar WHERE AID=:AID");
		$req->execute(array(
			"aAvatar"=>$this->getAID().".jpg",
			"AID"=>$this->getAID()
		));	
	}	

	//FUNCTION TO RETUN THE LIST OF ROOMS
	public function PendingRoomList($first,$second){
		include 'connect.php';
			$json = "";
			$req = $bdd->prepare("SELECT * FROM category c JOIN room r ON r.Category=c.CATID WHERE state='no' ORDER BY ID DESC LIMIT ".$first." , ".$second);
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


	//function that will return a list of admins
	public function AdminsList($first,$second){
		include 'connect.php';
		$json = "";
		$req = $bdd->prepare("SELECT * FROM admin  ORDER BY AID DESC LIMIT ".$first." , ".$second);
		$req->execute();
		if ($req->rowCount()==0) 
			return "empty";
		else{
			$json = array();
			while ($data = $req->fetch()) {				
				array_push($json, 
							 array( "AID"=>$data["AID"] ,
									"aFullName"=>$data["aFullName"] ,
									"aEmail"=>$data["aEmail"] ,
									"aPassword"=>$data["aPassword"] ,
									"aRole"=>$data["aRole"] ,
									"aAvatar"=>$data["aAvatar"] 
							  )
				);
			}
			return json_encode($json);
		}
	}


//function that will return a list of users
	public function UsersList($first,$second,$filter){
		include 'connect.php';
		$json = "";
		$req="";
		if($filter=="all"){
			$req = $bdd->prepare("SELECT * FROM user  ORDER BY UID DESC LIMIT ".$first." , ".$second);
			$req->execute();
		}
		if($filter!="all"){
			$req = $bdd->prepare("SELECT * FROM user  WHERE Status=:Status ORDER BY UID DESC LIMIT ".$first." , ".$second);
			$req->execute(array(
				'Status'=>$filter
			));
		}		
		
		if ($req->rowCount()==0) 
			return "empty";
		else{
			$json = array();
			while ($data = $req->fetch()) {				
				array_push($json, 
							 array( "UID"=>$data["UID"] ,
									"FullName"=>$data["FullName"] ,
									"Email"=>$data["Email"] ,
									"Password"=>$data["Password"] ,
									"Status"=>$data["Status"] ,
									"Avatar"=>$data["Avatar"] 
							  )
				);
			}
			return json_encode($json);
		}
	}



	//this list of funtions will return the stats of the website
	//Users number
	//Connected users
	//Categoris number
	//Rooms number
	//Subjects number
	//Comments number
	public function WebSiteInfo(){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM user");
		$req->execute();
		$Users = $req->rowCount();

		$req1 = $bdd->prepare("SELECT * FROM user WHERE Connect=1");
		$req1->execute();
		$ConnectedUsers = $req1->rowCount();

		$req2 = $bdd->prepare("SELECT * FROM category");
		$req2->execute();
		$Categories = $req2->rowCount();

		$req3 = $bdd->prepare("SELECT * FROM room");
		$req3->execute();
		$Rooms = $req3->rowCount();

		$req4 = $bdd->prepare("SELECT * FROM subject");
		$req4->execute();
		$Subjects = $req4->rowCount();

		$req5 = $bdd->prepare("SELECT * FROM comment");
		$req5->execute();
		$Comments = $req5->rowCount();

		$json = array(
			"Users"=>$Users,
			"ConnectedUsers"=>$ConnectedUsers,
			"Categories"=>$Categories,
			"Rooms"=>$Rooms,
			"Subjects"=>$Subjects,
			"Comments"=>$Comments
			);
		return json_encode($json);
	}

}






//function to print a list of admins
function PrintAdmin($Admin_list,$token){
	if ($Admin_list!="empty") {
		$Admin_list = json_decode($Admin_list);
		foreach ($Admin_list as $ad) {
			?>
				<div class="admin-div" id="admin-div-<?php echo $ad->AID; ?>">
					<div class="admin-top">
						<div class="admin-role-top" ><span id="amin-role-change-<?php echo $ad->AID; ?>">
							<?php echo ucfirst($ad->aRole); ?></span> admin
						</div>
						<img src="../avatars/admin/<?php echo $ad->aAvatar; ?>" />
					</div>
					<div class="admin-bottom">
						<div class="admin-name-display"><?php echo $ad->aFullName; ?></div>
						<div class="admin-action">						
							<div class="role-admin-change-select">
								<select name="aRole" id="adminChangeRole-<?php echo $ad->AID; ?>" onchange="updateadminrole(<?php echo $ad->AID; ?>,'<?php echo $token; ?>');">
									<option for="" value="">Choose Admin Role ...</option>
									<option for="normal" value="normal">Normal</option>
									<option for="master" value="master">Master</option>
								</select>
							</div>
							<div class="delete-admin RedGradient" onclick="deleteadmin(<?php echo $ad->AID; ?>,'<?php echo $token; ?>');">Delete</div>
						</div>
					</div>
				</div>		
			<?php
		}
	}
}


//function to print a list of users
function PrintUser($User_list,$token){
	if ($User_list!="empty") {
		$User_list = json_decode($User_list);
		foreach ($User_list as $us) {
			?>
				<div class="user-div" id="user-div-<?php echo $us->UID; ?>">
					<div class="user-top">
						<div class="user-status-top" ><span id="user-status-change-<?php echo $us->UID; ?>" class="<?php echo $us->Status; ?>">
						<?php echo ucfirst($us->Status); ?></span>
						</div>
						<img src="../avatars/perso/<?php echo $us->Avatar; ?>" />
					</div>
					<div class="user-bottom">
						<div class="user-name-display"><?php echo $us->FullName; ?></div>
						<div class="user-action-admin">						
							<div class="status-user-change-select">
								<select name="Status" id="userChangeStatus-<?php echo $us->UID; ?>" onchange="updateUserStatus(<?php echo $us->UID; ?>,'<?php echo $token; ?>');">
									<option for="" value="">Choose User Status ...</option>
									<option for="normal" value="enable">Enable</option>
									<option for="master" value="disable">Disable</option>
								</select>
							</div>
							<div class="delete-user RedGradient" onclick="deleteUser(<?php echo $us->UID; ?>,'<?php echo $token; ?>');">Delete</div>
						</div>
					</div>
				</div>		
			<?php
		}
	}
}


?>