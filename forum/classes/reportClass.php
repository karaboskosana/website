<?php
/*
report class is the calss tha will allow us to report a subject so the admins will be alerted
By using this class we will be able to do the following
1-	Report a subject
2-	Check if a user has already reported a subject
3-	Delete a report	



*/


class Report
{
	private $ReportId,$SubjectId,$UserId;
	public function getReportId(){ return $this->ReportId; }
	public function setReportId($ReportId){ $this->ReportId = $ReportId; }

	public function getSubjectId(){ return $this->SubjectId; }
	public function setSubjectId($SubjectId){ $this->SubjectId = $SubjectId; }
	
	public function getUserId(){ return $this->UserId; }
	public function setUserId($UserId){ $this->UserId = $UserId; }

	//INSERT A NEW REPORT ON THE DATABSE TABLE
	public function InsertReport(){
		include 'connect.php';
		$req = $bdd->prepare("INSERT INTO report(SubjectId,UserId) VALUES(:SubjectId,:UserId)");
		$req->execute(array(
			"SubjectId"=>$this->getSubjectId(),
			"UserId"=>$this->getUserId()
		));
	}	

	//DELETE THE REPORT OF A SUBJECT BY A USER
	public function DeleteReport(){
		include 'connect.php';
		$req = $bdd->prepare("DELETE FROM report WHERE SubjectId=:SubjectId AND UserId=:UserId");
		$req->execute(array(
			"SubjectId"=>$this->getSubjectId(),
			"UserId"=>$this->getUserId()
		));
	}

	//DELETE THE ALL REPORT OF A SUBJECT WHEN DELETING THE SUBJECT ITSELF
	public function DeleteAllReport(){
		include 'connect.php';
		$req = $bdd->prepare("DELETE FROM report WHERE SubjectId=:SubjectId");
		$req->execute(array(
			"SubjectId"=>$this->getSubjectId()
		));
	}



	//CHECK IF A USER HAS ALREADY REPORTED ON A SUBJECT OR NOT
	public function CheckReport(){
		include 'connect.php'; 
		$req = $bdd->prepare("SELECT * FROM report WHERE SubjectId=:SubjectId AND UserId=:UserId");
		$req->execute(array(
			"SubjectId"=>$this->getSubjectId(),
			"UserId"=>$this->getUserId()
		));
		return $req->rowCount();
	}


}
?>