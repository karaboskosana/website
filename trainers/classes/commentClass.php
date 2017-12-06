<?php
/*
comment class is the class that will allow us to manipulate the comments of a subject
By using this class you will be able to do the following
1-	Create a new comment
2-	Delete a comment
3-	Like or Unlike a comment
4-	Choose a commnet as the best comment
5-	Return a list of comments by subject on 3 Diferrent spaces: Admin space - Guest Space - User Space
*/
class Comment
{
	private $CID,$User,$Subject,$cText,$cDate,$Likes,$Best;
	
	public function getCID(){ return $this->CID; }
	public function setCID($CID){ $this->CID = $CID; }

	public function getUser(){ return $this->User; }
	public function setUser($User){ $this->User = $User; }
	
	public function getSubject(){ return $this->Subject; }
	public function setSubject($Subject){ $this->Subject = $Subject; }
	
	public function getCText(){ return $this->cText; }
	public function setCText($cText){ $this->cText = $cText; }
	
	public function getCDate(){ return $this->cDate; }
	public function setCDate($cDate){ $this->cDate = $cDate; }
	
	public function getLikes(){ return $this->Likes; }
	public function setLikes($Likes){ $this->Likes = $Likes; }
	
	public function getBest(){ return $this->Best; }
	public function setBest($Best){ $this->Best = $Best; }


	//FUNTION TO ADD NEW COMMENT	
	public function InsertComment(){
		include 'connect.php';
		$req = $bdd->prepare("INSERT INTO comment(User,Subject,cText,cDate,Likes,Best) VALUES(:User,:Subject,:cText,:cDate,:Likes,:Best)");
		$req->execute(array(
			'User'=>$this->getUser(),
			'Subject'=>$this->getSubject(),
			'cText'=>htmlspecialchars($this->getCText()),
			'cDate'=>$this->getCDate(),			
			'Likes'=>0,
			'Best'=>"no"
		));
		
		 $json = array( "CID"=>$bdd->lastInsertId(),
				"Subject"=>$this->getSubject(),
				"cText"=>$this->getCText(),
				"Likes"=>0,
				"Best"=>"no",
				"cDate"=>date("M d", strtotime($this->getCDate()))." at ".date("H:i",strtotime($this->getCDate()))."pm",
				"UID"=>$_SESSION['UID'],
				"FullName"=>$_SESSION['FullName'],
				"Avatar"=>$_SESSION['Avatar']
			 );
		echo  json_encode($json); 

	}

	//FUNCTION TO DELETE COMMENT
	public function DeleteComment(){
		include 'connect.php';
		$req = $bdd->prepare("DELETE FROM comment WHERE CID=:CID");
		$req->execute(array(
			"CID"=>$this->getCID()
		));

		$req1 = $bdd->prepare("DELETE FROM clike WHERE Comment=:Comment");
		$req1->execute(array(
			"Comment"=>$this->getCID()
		));
		
	}

	//DELETE ALL COMMENTS OF A DELETED SUBJECT
	public function DeleteAllComment(){
		include 'connect.php';
		$req = $bdd->prepare("DELETE FROM comment WHERE Subject=:Subject");
		$req->execute(array(
			"Subject"=>$this->getSubject()
		));
		
	}

	//FUNCTION TO ADD LIKE NUMBER OR MINUS 
	public function CommentLikesNumber($operation,$User){
		include 'connect.php';
		$number = 0;
		$clike = new Clike();
		$clike->setUser($User);
		$clike->setComment($this->getCID());	

		if ($operation=="plus"){
			$number = 1;
			$clike->InsertLike();
		}
		else if ($operation=="minus"){
			$number = -1;
			$clike->DeleteLike();
		} 
				

		$req = $bdd->prepare("UPDATE comment SET Likes=Likes+:cNumber WHERE CID=:CID");
		$req->execute(array(
			"cNumber"=>$number,
			"CID"=>$this->getCID()
		));
		
	}	


	//FUNCTION TO MAKE THE COMMENT IS THE BEST
	public function CommentIsTheBest(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE comment SET Best=:Best WHERE CID=:CID");
		$req->execute(array(
			'Best'=>'yes',
			"CID"=>$this->getCID()
		));
		
	}

	//FUNCTION TO MAKE THE COMMENT NOT THE BEST
	public function CommentIsNotTheBest(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE comment SET Best=:Best WHERE CID=:CID");
		$req->execute(array(
			'Best'=>'no',
			"CID"=>$this->getCID()
		));
		
	}



	//FUNCTION TO RETURN ALL SUBJECTS BY ROOM
	public function CommentsBySubject($first,$second){
		include 'connect.php';
		$json ="";
		$req = $bdd->prepare("SELECT * FROM comment c JOIN user u ON u.UID=c.User WHERE c.Subject=:Subject ORDER BY c.CID DESC LIMIT ".$first." , ".$second);
		$req->execute(array(
			"Subject"=>$this->getSubject()
		));

		if ($req->rowCount()==0) {			
			return "empty";
		}else{
			$json = array();
			while ($data = $req->fetch()) {
				array_push($json,
							 array( "CID"=>$data['CID'],
							 		"Subject"=>$data['Subject'],
							 		"cText"=>LinkMe(smiley($data['cText'])),
							 		"Likes"=>$data['Likes'],
							 		"Best"=>$data['Best'],
							 		"cDate"=>$data['cDate'],
							 		"UID"=>$data['UID'],
							 		"FullName"=>$data['FullName'],
							 		"Avatar"=>$data['Avatar']
							  )
				);
			}
			return json_encode($json);
		}
	}

}

//this function will be used to print comments of a subject
//it will make the work easy for us when we will load more comments using ajax
function PrintComment($comments,$subject,$token){
		if ($comments!="empty") {
			$comments = json_decode($comments);
				foreach ($comments as $com) {
						$cDate = date("M d", strtotime($com->cDate))." at ".date("H:i",strtotime($com->cDate))."pm";
						$clike = new Clike();
						$clike->setUser($_SESSION["UID"]);
						$clike->setComment($com->CID);
				?>
						<div class="comment">
							<div class="comment-user-avatar">
								<img src="../avatars/perso/<?php echo $com->Avatar; ?>">
							</div>
							<div class="comment-user-info-date">
								<span class="comment-user-fullname"><?php echo $com->FullName; ?></span>
								<span class="comment-display-date"><?php echo $cDate  ?> -</span>
								<?php if ($_SESSION["UID"]==$com->UID || $_SESSION["UID"]==$subject->UID) { ?>
								<span class="delete-comment" onclick="deletecomment(this,<?php echo $com->CID; ?>,<?php echo $subject->SID; ?>,'<?php echo $token; ?>')" title="Delete comment">x</span>					
								<?php } if ($subject->State!="closed" && $_SESSION["UID"]==$subject->UID) { ?>
								<span class="choose-best-comment" onclick="closesubject(this,<?php echo $subject->SID; ?>,<?php echo $com->CID; ?>,'<?php echo $token; ?>');">Best</span>											
								<?php } ?>
							</div>
							<div class="comment-display-text"><?php echo $com->cText; ?></div>
								<div class="likes-comment-num-text">
									<span class="comment-like-number" id="likes-number-comment-<?php echo $com->CID; ?>"><?php echo $com->Likes; ?></span>
									<?php  if($clike->CheckLike()==0){ ?>
									<a class="comment-display-like" onclick="comment_like_unlike(this,'plus',<?php echo $com->CID; ?>,<?php echo $subject->SID; ?>,<?php echo $com->UID; ?>,<?php echo $_SESSION['UID']; ?>,'<?php echo $token; ?>');">like</a>
									<?php }else{ ?>
									<a class="comment-display-like" onclick="comment_like_unlike(this,'minus',<?php echo $com->CID; ?>,<?php echo $subject->SID; ?>,<?php echo $com->UID; ?>,<?php echo $_SESSION['UID']; ?>,'<?php echo $token; ?>');">unlike</a>
									<?php } ?>
								</div>
						</div>
					<?php
							}
						}				
}


//this function will be used to print comments of a subject
//it will make the work easy for us when we will load more comments using ajax
function PrintCommentAdmin($comments,$subject,$token){
		if ($comments!="empty") {
			$comments = json_decode($comments);
				foreach ($comments as $com) {
						$cDate = date("M d", strtotime($com->cDate))." at ".date("H:i",strtotime($com->cDate))."pm";
						$clike = new Clike();	
						$clike->setComment($com->CID);
				?>
						<div class="comment">
							<div class="comment-user-avatar">
								<img src="../avatars/perso/<?php echo $com->Avatar; ?>">
							</div>
							<div class="comment-user-info-date">
								<span class="comment-user-fullname"><?php echo $com->FullName; ?></span>
								<span class="comment-display-date"><?php echo $cDate  ?> -</span>
								<span class="delete-comment" onclick="deletecomment(this,<?php echo $com->CID; ?>,<?php echo $subject->SID; ?>,'<?php echo $token; ?>')" title="Delete comment">x</span>					
								
							</div>
							<div class="comment-display-text"><?php echo $com->cText; ?></div>
							<div class="likes-comment-num-text">	
								<span class="comment-like-number" id="likes-number-comment-<?php echo $com->CID; ?>"><?php echo $com->Likes; ?> likes</span>	
							</div>	
						</div>
					<?php
							}
						}				
}

//this function will be used to print comments of a subject For a guest user no action will be done just print
//it will make the work easy for us when we will load more comments using ajax
function PrintCommentGuest($comments,$subject){
		if ($comments!="empty") {
			$comments = json_decode($comments);
				foreach ($comments as $com) {
						$cDate = date("M d", strtotime($com->cDate))." at ".date("H:i",strtotime($com->cDate))."pm";
						
				?>
						<div class="comment">
							<div class="comment-user-avatar">
								<img src="../avatars/perso/<?php echo $com->Avatar; ?>">
							</div>
							<div class="comment-user-info-date">
								<span class="comment-user-fullname"><?php echo $com->FullName; ?></span>
								<span class="comment-display-date"><?php echo $cDate  ?> -</span>
							</div>
							<div class="comment-display-text"><?php echo $com->cText; ?></div>
							<div class="likes-comment-num-text">	
								<span class="comment-like-number" id="likes-number-comment-<?php echo $com->CID; ?>"><?php echo $com->Likes; ?> likes</span>	
							</div>				
						</div>
					<?php
							}
						}				
}
?>