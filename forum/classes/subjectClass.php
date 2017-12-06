<?php
/*
subject class is the class that will allow us to handle the subjects of the rooms
By using this class we weill be ableto do the following
1-	Create a new subject
2-	Delete a subject
3-	Like or Unlike a subject
4-	Return a subject information by it's ID
5-	Return A List of Subjects on 3 Diferrent spaces: Admin space - Guest Space - User Space
6-	Search for a subject
7-	Return a list of subjects by room
8-	Return a list of a user's subject
10-	Add a view to a subject
11-	Add a comment to a subject
12-	Open or Close a subject
13-	Report a subject As Spam
14-	Choose best comment for a subject
15-	Return a list of reported subjects on the Admin Panel
16-	Edit a subject
17-	Share a subject on the social networks as (facebook-google+-twitter)


*/
class Subject
{
	private $SID,$User,$Room,$Title,$Text,$Likes,$Comments,$State,$Date,$View,$BestComment,$Report;
	public function getSID(){ return $this->SID; }
	public function setSID($SID){ $this->SID = $SID; }

	public function getUser(){ return $this->User; }
	public function setUser($User){ $this->User = $User; }
	
	public function getRoom(){ return $this->Room; }
	public function setRoom($Room){ $this->Room = $Room; }
	
	public function getTitle(){ return $this->Title; }
	public function setTitle($Title){ $this->Title = $Title; }
	
	public function getText(){ return $this->Text; }
	public function setText($Text){ $this->Text = $Text; }
	
	public function getLikes(){ return $this->Likes; }
	public function setLikes($Likes){ $this->Likes = $Likes; }
	
	public function getComments(){ return $this->Comments; }
	public function setComments($Comments){ $this->Comments = $Comments; }
	
	public function getState(){ return $this->State; }
	public function setState($State){ $this->State = $State; }
	
	public function getDate(){ return $this->Date; }
	public function setDate($Date){ $this->Date = $Date; }
	
	public function getView(){ return $this->View; }
	public function setView($View){ $this->View = $View; }


	public function getBestComment(){ return $this->BestComment; }
	public function setBestComment($BestComment){ $this->BestComment = $BestComment; }


	public function getReport(){ return $this->Report; }
	public function setReport($Report){ $this->Report = $Report; }


	//FUNTION TO INSERT NEW SUBJECT
	public function InsertSubject(){
		include 'connect.php';
		$req = $bdd->prepare("INSERT INTO subject(User,Room,Title,Text,Likes,Comments,State,Date,View,BestComment) VALUES(:User,:Room,:Title,:Text,:Likes,:Comments,:State,:Date,:View,:BestComment)");
		$req->execute(array(
			"User"=>$this->getUser(),
			"Room"=>$this->getRoom(),
			"Title"=>htmlspecialchars($this->getTitle()),
			"Text"=>htmlspecialchars($this->getText()),
			"Likes"=>0,
			"Comments"=>0,
			"State"=>"pending",
			"Date"=>$this->getDate(),
			"View"=>0,
			"BestComment"=>0
		));
	
	}

	//RETURN SUBJECT INFO
	public function SubjectInfo(){
		include 'connect.php';
		$json = array();
		$req = $bdd->prepare("SELECT * FROM subject s JOIN user u ON u.UID=s.User WHERE s.SID=:SID");
		$req->execute(array(
			"SID"=>$this->getSID()
		));
		if ($req->rowCount()==0) {
			$json = array("state"=>"no");
		}else{
			$data = $req->fetch();
			$json = array(
					"state"=>"yes",
					"SID"=>$data['SID'],
			 		"Room"=>$data['Room'],
			 		"Title"=>$data['Title'],
			 		"Text"=>smiley($data['Text']),
			 		"Likes"=>$data['Likes'],
			 		"Comments"=>$data['Comments'],
			 		"State"=>$data['State'],
			 		"Date"=>$data['Date'],
			 		"UID"=>$data['UID'],
			 		"FullName"=>$data['FullName'],
			 		"Avatar"=>$data['Avatar'],
			 		"View"=>$data["View"],
			 		"BestComment"=>$data["BestComment"]
				);
		}
		
		return json_encode($json);
	}

	//FUNCTION TO DELETE A SUBJECT BY IT'S ID
	public function DeleteSubject(){
		include 'connect.php';
		$req = $bdd->prepare("DELETE FROM subject WHERE SID=:SID");
		$req->execute(array(
			"SID"=>$this->getSID()
		));

		$req1 = $bdd->prepare("DELETE FROM slike WHERE Subject=:Subject");
		$req1->execute(array(
			"Subject"=>$this->getSID()
		));
		
		//DELETING ALL THE COMMENTS OF THE DELETED SUBJECT
		$comment = new Comment();
		$comment->setSubject($this->getSID());
		$comment->DeleteAllComment();

		//DELETING ALL THE REPORTS OF THE DELETED SUBJECT
		$report = new Report();
		$report->setSubjectId($this->getSID());
		$report->DeleteAllReport();
	}

	//function to report a subject
	public function ReportSubject($operation,$UserId){
		include 'connect.php';
		$number = 0;
		$report = new Report();
		$report->setSubjectId($this->getSID());
		$report->setUserId($UserId);
	
		if ($operation=="plus"){
			$number = 1;
			$report->InsertReport();	
		}
		else if ($operation=="minus") {
			$number = -1;	
			$report->DeleteReport();	
		}

		$req = $bdd->prepare("UPDATE subject SET Report=Report+:cNumber WHERE SID=:SID");
		$req->execute(array(
			"cNumber"=>$number,
			"SID"=>$this->getSID()
		));
		
	}

	//Return a list fo the reported subjects
	//the reported subject will be return if at least 10 users reports that same subjects 
	//So admins will try to figure out the problem with that subject
	public function ReportedSubjectsList($first,$second){
		include 'connect.php';
		$json ="";		
		$req = $bdd->prepare("SELECT * FROM subject s JOIN user u ON u.UID=s.User WHERE Report>=:Report ORDER BY s.SID DESC LIMIT ".$first." , ".$second);		
		$req->execute(array(
			"Report"=>10
		));

		if ($req->rowCount()==0) {			
			return "empty";
		}else{
			$json = array();
			while ($data = $req->fetch()) {
				array_push($json,
							 array( "SID"=>$data['SID'],
							 		"Room"=>$data['Room'],
							 		"Title"=>$data['Title'],
							 		"Text"=>smiley($data['Text']),
							 		"Likes"=>$data['Likes'],
							 		"Comments"=>$data['Comments'],
							 		"State"=>$data['State'],
							 		"Date"=>$data['Date'],
							 		"UID"=>$data['UID'],
							 		"FullName"=>$data['FullName'],
							 		"Avatar"=>$data['Avatar'],
							 		"View"=>$data["View"],
			 						"BestComment"=>$data["BestComment"]
							  )
				);
			}
			return json_encode($json);
		}
	}

	//SearchSubjects
	//FUNCTION TO RETURN ALL SUBJECTS BY KEYWORD
	//We will pass a parameter to this function it wll serve us as to search on database for matched subjects
	//The result of this serac will return any subject that matches in any room and category
	//if no match was found it will rettun empty
	public function SearchSubjects($first,$second,$keyword){
		include 'connect.php';
		$json="";
		$req = $bdd->prepare("SELECT * FROM subject s JOIN user u ON u.UID=s.User WHERE s.Title LIKE :Title ORDER BY s.SID DESC LIMIT ".$first." , ".$second);						
		$req->execute(array(
			"Title"=>'%'.$keyword.'%'
		));
		if ($req->rowCount()==0) {			
			return "empty";
		}else{
			$json = array();
			while ($data = $req->fetch()) {
				array_push($json,
							 array( "SID"=>$data['SID'],
							 		"Room"=>$data['Room'],
							 		"Title"=>$data['Title'],
							 		"Text"=>smiley($data['Text']),
							 		"Likes"=>$data['Likes'],
							 		"Comments"=>$data['Comments'],
							 		"State"=>$data['State'],
							 		"Date"=>$data['Date'],
							 		"UID"=>$data['UID'],
							 		"FullName"=>$data['FullName'],
							 		"Avatar"=>$data['Avatar'],
							 		"View"=>$data["View"],
			 						"BestComment"=>$data["BestComment"]
							  )
				);
			}
			return json_encode($json);
		}
	}


	//FUNCTION TO RETURN ALL SUBJECTS BY ROOM
	//We will pass a parameter to this function it wll serve us as a filter for subjects
	//5 filters will be available By : Date -- Views -- Likes -- Closed -- Pending
	//this filters will allow the user to sort the Subjects displaying by one of them so he can find easily what he is looking for
	public function SubjectsByRoom($first,$second,$filter){
		include 'connect.php';
		$json ="";
		switch ($filter) {
			case 'date':
				$req = $bdd->prepare("SELECT * FROM subject s JOIN user u ON u.UID=s.User WHERE s.Room=:Room ORDER BY s.SID DESC LIMIT ".$first." , ".$second);				
				break;
			
			case 'views':
				$req = $bdd->prepare("SELECT * FROM subject s JOIN user u ON u.UID=s.User WHERE s.Room=:Room ORDER BY s.View DESC LIMIT ".$first." , ".$second);	
				break;

			case 'likes':
				$req = $bdd->prepare("SELECT * FROM subject s JOIN user u ON u.UID=s.User WHERE s.Room=:Room ORDER BY s.Likes DESC LIMIT ".$first." , ".$second);	
				break;

			case 'pending':
				$req = $bdd->prepare("SELECT * FROM subject s JOIN user u ON u.UID=s.User WHERE s.Room=:Room AND s.State='pending' ORDER BY s.SID DESC LIMIT ".$first." , ".$second);	
				break;	

			case 'closed':
				$req = $bdd->prepare("SELECT * FROM subject s JOIN user u ON u.UID=s.User WHERE s.Room=:Room AND s.State='closed' ORDER BY s.SID DESC LIMIT ".$first." , ".$second);	
				break;	

		}

		
		$req->execute(array(
			"Room"=>$this->getRoom()
		));



		if ($req->rowCount()==0) {			
			return "empty";
		}else{
			$json = array();
			while ($data = $req->fetch()) {
				array_push($json,
							 array( "SID"=>$data['SID'],
							 		"Room"=>$data['Room'],
							 		"Title"=>$data['Title'],
							 		"Text"=>smiley($data['Text']),
							 		"Likes"=>$data['Likes'],
							 		"Comments"=>$data['Comments'],
							 		"State"=>$data['State'],
							 		"Date"=>$data['Date'],
							 		"UID"=>$data['UID'],
							 		"FullName"=>$data['FullName'],
							 		"Avatar"=>$data['Avatar'],
							 		"View"=>$data["View"],
			 						"BestComment"=>$data["BestComment"]
							  )
				);
			}
			return json_encode($json);
		}
	}


	//FUNCTION TO RETURN ALL SUBJECTS BY USER
	//this will be displayed on the user profile page
	//so he can manage his subjects close or open them .. choose best comments or just delete them
	public function SubjectsByUser($first,$second){
		include 'connect.php';
		$json ="";		
		$req = $bdd->prepare("SELECT * FROM subject s JOIN user u ON u.UID=s.User WHERE s.User=:User ORDER BY s.SID DESC LIMIT ".$first." , ".$second);		
		$req->execute(array(
			"User"=>$this->getUser()
		));

		if ($req->rowCount()==0) {			
			return "empty";
		}else{
			$json = array();
			while ($data = $req->fetch()) {
				array_push($json,
							 array( "SID"=>$data['SID'],
							 		"Room"=>$data['Room'],
							 		"Title"=>$data['Title'],
							 		"Text"=>smiley($data['Text']),
							 		"Likes"=>$data['Likes'],
							 		"Comments"=>$data['Comments'],
							 		"State"=>$data['State'],
							 		"Date"=>$data['Date'],
							 		"UID"=>$data['UID'],
							 		"FullName"=>$data['FullName'],
							 		"Avatar"=>$data['Avatar'],
							 		"View"=>$data["View"],
			 						"BestComment"=>$data["BestComment"]
							  )
				);
			}
			return json_encode($json);
		}
	}


	//FUNCTION TO CLOSE A SUBJECT
	public function CloseSubject(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE subject SET State=:State , BestComment=:BestComment  WHERE SID=:SID");
		$req->execute(array(
			"State"=>"closed",
			"BestComment"=>$this->getBestComment(),
			"SID"=>$this->getSID()	
		));
		
	}

	//FUNCTION TO OPEN A SUBJECT
	public function OpenSubject(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE subject SET State=:State , BestComment=:BestComment  WHERE SID=:SID");
		$req->execute(array(
			"State"=>"pending",
			"BestComment"=>$this->getBestComment(),
			"SID"=>$this->getSID()	
		));
		
	}


	//FUNCTION TO ADD COMMENT NUMBER OR MINUS 
	public function SubjectCommentsNumber($operation){
		include 'connect.php';
		$number = 0;
		
		if ($operation=="plus")
			$number = 1;
		else if ($operation=="minus") 
			$number = -1;	

		$req = $bdd->prepare("UPDATE subject SET Comments=Comments+:cNumber WHERE SID=:SID");
		$req->execute(array(
			"cNumber"=>$number,
			"SID"=>$this->getSID()
		));
		
	}	

	//FUNCTION TO ADD LIKE NUMBER OR MINUS 
	public function SubjectLikesNumber($operation,$User){
		include 'connect.php';
		$number = 0;
		$slike = new Slike();
		$slike->setUser($User);
		$slike->setSubject($this->getSID());		
		if ($operation=="plus"){
			$number = 1;
			$slike->InsertLike();
		}
		else if ($operation=="minus") {
			$number = -1;	
			$slike->DeleteLike();
		}

		$req = $bdd->prepare("UPDATE subject SET Likes=Likes+:cNumber WHERE SID=:SID");
		$req->execute(array(
			"cNumber"=>$number,
			"SID"=>$this->getSID()
		));
		
	}	


	//FUNCTION TO EDIT SUBJECT
	public function EditSubject(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE subject SET Title=:Title , Text:=:Text WHERE SID=:SID");
		$req->execute(array(
			'Title'=>$this->getTitle(),
			'Text'=>$this->getText(),
			'SID'=>$this->getSID(),
		));
	}

	//Add one view to a subject
	public function AddView(){
		include 'connect.php';
		$req = $bdd->prepare("UPDATE subject SET View=View+1 WHERE SID=:SID");
		$req->execute(array(
			'SID'=>$this->getSID()
		));
	}


	//Function that will load some of new subjects of the rooms that a user is subscribed too
	//This subject will be shown on the right of the page on the top of the Publicity div
	//Getting only subjects by other users on the room where I'm subscribed to
	public function Subscriptions(){
		include 'connect.php';
		$req = $bdd->prepare("SELECT * FROM	subject s JOIN user u JOIN room R ON u.UID=s.User AND r.ID=s.Room  WHERE s.User!=:User AND  (find_in_set(s.Room,'0,".$_SESSION['UserRoomSubscription']."') <> 0) ORDER BY s.SID DESC LIMIT 0,4 ");
		$req->execute(array(
			"User"=>$this->getUser()
		));

		if ($req->rowCount()==0) {			
			return "empty";
		}else{
			$json = array();
			while ($data = $req->fetch()) {			
				array_push($json,
							 array( "SID"=>$data['SID'],
							 		"Room"=>$data['Room'],
							 		"Title"=>$data['Title'],
							 		"Text"=>smiley($data['Text']),
							 		"Likes"=>$data['Likes'],
							 		"Comments"=>$data['Comments'],
							 		"State"=>$data['State'],
							 		"Date"=>$data['Date'],
							 		"UID"=>$data['UID'],
							 		"FullName"=>$data['FullName'],
							 		"Avatar"=>$data['Avatar'],
							 		"View"=>$data["View"],
							 		"rTitle"=>$data["Title"],
			 						"BestComment"=>$data["BestComment"]
							  )
				);
			}
			return json_encode($json);
		}
	}	


}

//this function will be used to print the subject and it's last comment
//it will make the work easy for us when we will load more subjects using ajax
function PrintSubject($Subjects_List,$token){
if ($Subjects_List!="empty") {
						$Subjects_List = json_decode($Subjects_List);
						//creating a class for the subject likes
						$slike = new Slike();
						//Creating a class for the subject comments
						$comment = new Comment();

						$slike->setUser($_SESSION["UID"]);	
						foreach ($Subjects_List as $subj) {
							$Date = date("M d", strtotime($subj->Date))." at ".date("H:i",strtotime($subj->Date))."pm";
							$slike->setSubject($subj->SID);
							$comment->setSubject($subj->SID);
							$report = new Report();
							$report->setSubjectId($subj->SID);
							$report->setUserId($_SESSION["UID"]);
							?>
							<div class="subject">
								<div class="subject-user-avatar">
									<img src="../avatars/perso/<?php echo $subj->Avatar; ?>">
								</div>	
								<div class="subject-user-info-date">
									<span class="subject-user-fullname"><?php echo $subj->FullName; ?></span>
									<!--Know if a subject is closer or not
										When it's closed you can re open so other users an comment (and the comment that you have chosen for the best comment will be deleted)
										you can also just close a subject for certain momnet so users can't comment
									-->
									<?php if ($subj->State=="closed") { ?>
									<div class="small-top-bar-icones subject-action-state" id="open-subject-<?php echo $subj->SID; ?>"  <?php if ($_SESSION["UID"]==$subj->UID) { ?>	onclick="opensubject(this,<?php echo $subj->SID?>,0,'<?php echo $token; ?>');" <?php } ?>  title="Open the subject" style="background-position:-231px -26px; "></div>
									<?php }else { ?>
									<div class="small-top-bar-icones subject-action-state" id="close-subject-<?php echo $subj->SID; ?>"  <?php if ($_SESSION["UID"]==$subj->UID) { ?> onclick="closesubject(this,<?php echo $subj->SID?>,0,'<?php echo $token; ?>');" <?php } ?>  title="Close the subject" style="background-position:-254px -26px"></div>
									<?php }?>
									<span class="subject-display-date">
									<?php if ($report->CheckReport()==0 ) { ?>
										<div class="small-top-bar-icones report-icon" style="background-position:-312px -3px;" onclick="reportsubject(this,<?php echo $subj->SID; ?>,<?php echo $_SESSION["UID"]; ?>,'plus','<?php echo $token; ?>');" title="Report as Spam"></div>
									<?php }else{ ?>
										<div class="small-top-bar-icones report-icon" style="background-position:-312px -32px;" onclick="reportsubject(this,<?php echo $subj->SID; ?>,<?php echo $_SESSION["UID"]; ?>,'minus','<?php echo $token; ?>');"  title="Not Spam"></div>	
									<?php } ?>
										<?php echo $Date; ?>
									</span>
								</div>				
								<div class="subject-title-and-text">
									<span class="subject-display-tilte" id="edit-subject-title-<?php echo $subj->SID; ?>"><?php echo $subj->Title; ?></span>
									<span id="edit-subject-text-<?php echo $subj->SID; ?>"><?php echo $subj->Text; ?></span>
								</div>
								<div class="subject-actions">
									<div class="subject-actions-left">
										<!--checking if the actual user ikes this subject-->
										<?php	if($slike->CheckLike() == 0){ ?>
										<div class="small-top-bar-icones subject-actions-like"  onclick="likesubject(this,<?php echo $subj->SID; ?>,<?php echo $subj->UID; ?>,<?php echo $_SESSION['UID']; ?>,'<?php echo $token; ?>');" style="background-position: -85px -27px;"></div>
										<?php }else{ ?>
										<div class="small-top-bar-icones subject-actions-like" onclick="unlikesubject(this,<?php echo $subj->SID; ?>,<?php echo $subj->UID; ?>,<?php echo $_SESSION['UID']; ?>,'<?php echo $token; ?>');" style="background-position: -60px -27px;"></div>
										<?php } if ($_SESSION["UID"]==$subj->UID) { ?>								
											<div class="small-top-bar-icones subject-delete" onclick="deletesubject(this,<?php echo $subj->SID; ?>,<?php echo $subj->Room; ?>,'<?php echo $token; ?>');" title="Delete this subject"></div>
											<div class="small-top-bar-icones subject-edit" onclick="editsubject(this,<?php echo $subj->SID; ?>);" title="Edit this subject"></div>										
										<?php } ?> 
										<div class="small-top-bar-icones subject-actions-share" title="Share this subject" onclick="showsocialshare(this);">
											<div class="share-show-social WhiteHeader">
												<a href="http://www.facebook.com/sharer.php?u=http//www.nabil-lahssine.com/demo/shadyforum/home/read-subject.php?subject=<?php echo $subj->SID; ?>" title="Share on facebook" style="background-position:-1px 0px;"></a>
												<a href="http://twitter.com/share?url=http//www.nabil-lahssine.com/demo/shadyforum/home/read-subject.php?subject=<?php echo $subj->SID; ?>" title="Share on Twitter" style="background-position:-29px 0px;"></a>
												<a href="https://plus.google.com/share?url=http//www.nabil-lahssine.com/demo/shadyforum/home/read-subject.php?subject=<?php echo $subj->SID; ?>" title="Share on Google+" style="background-position:-61px 0px;"></a>
												<a href="http://www.linkedin.com/shareArticle?mini=true&url=http//www.nabil-lahssine.com/demo/shadyforum/home/read-subject.php?subject=<?php echo $subj->SID; ?>" title="Share on Linkedin" style="background-position:-93px 0px;"></a>
											</div>
										</div>
										
										<!--<div class="small-top-bar-icones subject-actions-comment"></div>-->
									</div>
									<div class="subject-actions-right">		
										<span class="how-many-subject-likes">
											<span><?php echo $subj->View; ?></span> views
										</span>					
										<span class="how-many-subject-likes">
											<span><?php echo $subj->Likes; ?> people</span> liked
										</span>	
										<span class="see-all-subject-comments">
											<a href="read-subject.php?subject=<?php echo $subj->SID; ?>">View all comments</a>
										</span>
									</div>
								</div>
								<div class="subject-last-comments" id="subject-last-comment-<?php echo $subj->SID; ?>">
									<?php
										$Last_Comment = $comment->CommentsBySubject(0,1);//printing the last comment of the subject
										if ($Last_Comment!="empty") {//checking if there is sme comments for the subject or not
											$Last_Comment = json_decode($Last_Comment);
											foreach ($Last_Comment as $com) {//looadp to print the comment
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
														<?php if ($_SESSION["UID"]==$com->UID || $_SESSION["UID"]==$subj->UID) { ?>
														<span class="delete-comment" onclick="deletecomment(this,<?php echo $com->CID; ?>,<?php echo $subj->SID; ?>,'<?php echo $token; ?>')"title="Delete comment">x</span>					
														<?php } if ($subj->State!="closed" && $_SESSION["UID"]==$subj->UID) { ?>
														<span class="choose-best-comment" onclick="closesubject(this,<?php echo $subj->SID; ?>,<?php echo $com->CID; ?>,'<?php echo $token; ?>');">Best</span>											
														<?php } ?>
													</div>
													<div class="comment-display-text"><?php echo $com->cText; ?></div>
													<div class="likes-comment-num-text">
														<span class="comment-like-number" id="likes-number-comment-<?php echo $com->CID; ?>"><?php echo $com->Likes; ?></span>
														<?php  if($clike->CheckLike()==0){ ?>
														<a class="comment-display-like" onclick="comment_like_unlike(this,'plus',<?php echo $com->CID; ?>,<?php echo $subj->SID; ?>,<?php echo $com->UID; ?>,<?php echo $_SESSION['UID']; ?>,'<?php echo $token; ?>');">like</a>
														<?php }else{ ?>
														<a class="comment-display-like" onclick="comment_like_unlike(this,'minus',<?php echo $com->CID; ?>,<?php echo $subj->SID; ?>,<?php echo $com->UID; ?>,<?php echo $_SESSION['UID']; ?>,'<?php echo $token; ?>');">unlike</a>
														<?php } ?>
													</div>
												</div>
												<?php
											}
										}
									?>
									
								
								</div>
								<?php if ($subj->State=="pending") { ?>
									<div class="subject-add-quick-comment" id="comment-subject-area-<?php echo $subj->SID; ?>">
										<textarea name="subject-quick-comment-textarea" onkeyup="commentsubject(this,event,<?php echo $subj->SID; ?>,<?php echo $subj->UID; ?>,<?php echo $_SESSION['UID']; ?>,'<?php echo $token; ?>');" class="subject-quick-comment-textarea" placeholder="Comment..."></textarea>
										<span class="comment-added" >Comment added successfully</span>
									</div>
								<?php } ?>
								
							</div>
							<?php
						}
					}
}


//THIS IS FOR THE ADMIN SUBJECT DISPLAY HE WILL BE ABALE TO DO EVERYTHING AND ANYTHING WITH THE SUBJECT NO NEED TO BE IT"S OWNER
//this function will be used to print the subject and it's last comment
//it will make the work easy for us when we will load more subjects using ajax
function PrintSubjectAdmin($Subjects_List,$token){
if ($Subjects_List!="empty") {
						$Subjects_List = json_decode($Subjects_List);
						//creating a class for the subject likes
						$slike = new Slike();
						//Creating a class for the subject comments
						$comment = new Comment();
						foreach ($Subjects_List as $subj) {
							$Date = date("M d", strtotime($subj->Date))." at ".date("H:i",strtotime($subj->Date))."pm";
							$slike->setSubject($subj->SID);
							$comment->setSubject($subj->SID);
							?>
							<div class="subject">
								<div class="subject-user-avatar">
									<img src="../avatars/perso/<?php echo $subj->Avatar; ?>">
								</div>	
								<div class="subject-user-info-date">
									<span class="subject-user-fullname"><?php echo $subj->FullName; ?></span>
									<!--Know if a subject is closer or not
										When it's closed you can re open so other users an comment (and the comment that you have chosen for the best comment will be deleted)
										you can also just close a subject for certain momnet so users can't comment
									-->
									<?php if ($subj->State=="closed") { ?>
									<div class="small-top-bar-icones subject-action-state" id="open-subject-<?php echo $subj->SID; ?>"  onclick="opensubject(this,<?php echo $subj->SID?>,0,'<?php echo $token; ?>');" title="Open the subject" style="background-position:-231px -26px; "></div>
									<?php }else { ?>
									<div class="small-top-bar-icones subject-action-state" id="close-subject-<?php echo $subj->SID; ?>"   onclick="closesubject(this,<?php echo $subj->SID?>,0,'<?php echo $token; ?>');"  title="Close the subject" style="background-position:-254px -26px"></div>
									<?php }?>
									<span class="subject-display-date"><?php echo $Date; ?></span>
								</div>				
								<div class="subject-title-and-text">
									<span class="subject-display-tilte" id="edit-subject-title-<?php echo $subj->SID; ?>"><?php echo $subj->Title; ?></span>
									<span id="edit-subject-text-<?php echo $subj->SID; ?>"><?php echo $subj->Text; ?></span>
								</div>
								<div class="subject-actions">
									<div class="subject-actions-left">
										<!--checking if the actual user ikes this subject-->
											<div class="small-top-bar-icones subject-delete" onclick="deletesubject(this,<?php echo $subj->SID; ?>,<?php echo $subj->Room; ?>,'<?php echo $token; ?>');" title="Delete this subject"></div>																				
										<!--<div class="small-top-bar-icones subject-actions-comment"></div>-->
									</div>
									<div class="subject-actions-right">		
										<span class="how-many-subject-likes">
											<span><?php echo $subj->View; ?></span> views
										</span>					
										<span class="how-many-subject-likes">
											<span><?php echo $subj->Likes; ?> people</span> liked
										</span>	
										<span class="see-all-subject-comments">
											<a href="read-subject-admin.php?subject=<?php echo $subj->SID; ?>">View all comments</a>
										</span>
									</div>
								</div>
								<div class="subject-last-comments" id="subject-last-comment-<?php echo $subj->SID; ?>">
									<?php
										$Last_Comment = $comment->CommentsBySubject(0,1);
										if ($Last_Comment!="empty") {
											$Last_Comment = json_decode($Last_Comment);
											foreach ($Last_Comment as $com) {
												$cDate = date("M d", strtotime($com->cDate))." at ".date("H:i",strtotime($com->cDate))."pm";
												?>
												<div class="comment">
													<div class="comment-user-avatar">
														<img src="../avatars/perso/<?php echo $com->Avatar; ?>">
													</div>
													<div class="comment-user-info-date">
														<span class="comment-user-fullname"><?php echo $com->FullName; ?></span>
														<span class="comment-display-date"><?php echo $cDate  ?> -</span>
														<span class="delete-comment" onclick="deletecomment(this,<?php echo $com->CID; ?>,<?php echo $subj->SID; ?>,'<?php echo $token; ?>')"title="Delete comment">x</span>					
													</div>
													<div class="comment-display-text"><?php echo $com->cText; ?></div>
													<!--<a class="comment-display-like" onclick="likecomment(idComment);">like</a>-->
												</div>
												<?php
											}
										}
									?>
									
								
								</div>								
							</div>
							<?php
						}
					}
}




//this function will be used to print the subject and it's last comment for a guest user he will be able just to see no action will be done
//it will make the work easy for us when we will load more subjects using ajax
function PrintSubjectGuest($Subjects_List){
if ($Subjects_List!="empty") {
						$Subjects_List = json_decode($Subjects_List);
						//creating a class for the subject likes
						//Creating a class for the subject comments
						$comment = new Comment();
						foreach ($Subjects_List as $subj) {
							$Date = date("M d", strtotime($subj->Date))." at ".date("H:i",strtotime($subj->Date))."pm";
							$comment->setSubject($subj->SID);
						
							?>
							<div class="subject">
								<div class="subject-user-avatar">
									<img src="../avatars/perso/<?php echo $subj->Avatar; ?>">
								</div>	
								<div class="subject-user-info-date">
									<span class="subject-user-fullname"><?php echo $subj->FullName; ?></span>
									<!--Know if a subject is closer or not
										When it's closed you can re open so other users an comment (and the comment that you have chosen for the best comment will be deleted)
										you can also just close a subject for certain momnet so users can't comment
									-->
									<?php if ($subj->State=="closed") { ?>
									<div class="small-top-bar-icones subject-action-state" id="open-subject-<?php echo $subj->SID; ?>"    title="Open the subject" style="background-position:-231px -26px; "></div>
									<?php }else { ?>
									<div class="small-top-bar-icones subject-action-state" id="close-subject-<?php echo $subj->SID; ?>"   title="Close the subject" style="background-position:-254px -26px"></div>
									<?php }?>
									<span class="subject-display-date">							
										<?php echo $Date; ?>
									</span>
								</div>				
								<div class="subject-title-and-text">
									<span class="subject-display-tilte" id="edit-subject-title-<?php echo $subj->SID; ?>"><?php echo $subj->Title; ?></span>
									<span id="edit-subject-text-<?php echo $subj->SID; ?>"><?php echo $subj->Text; ?></span>
								</div>
								<div class="subject-actions">
									<div class="subject-actions-left">
										<!--checking if the actual user ikes this subject-->
										<div class="small-top-bar-icones subject-actions-share" title="Share this subject" onclick="showsocialshare(this);">
											<div class="share-show-social WhiteHeader">
												<a href="http://www.facebook.com/sharer.php?u=http//www.nabil-lahssine.com/demo/shadyformhome/read-subject.php?subject=<?php echo $subj->SID; ?>" title="Share on facebook" style="background-position:-1px 0px;"></a>
												<a href="http://twitter.com/share?url=http//www.nabil-lahssine.com/demo/shadyformhome/read-subject.php?subject=<?php echo $subj->SID; ?>" title="Share on Twitter" style="background-position:-29px 0px;"></a>
												<a href="https://plus.google.com/share?url=http//www.nabil-lahssine.com/demo/shadyformhome/read-subject.php?subject=<?php echo $subj->SID; ?>" title="Share on Google+" style="background-position:-61px 0px;"></a>
												<a href="http://www.linkedin.com/shareArticle?mini=true&url=http//www.nabil-lahssine.com/demo/shadyformhome/read-subject.php?subject=<?php echo $subj->SID; ?>" title="Share on Linkedin" style="background-position:-93px 0px;"></a>
											</div>
										</div>
										
										<!--<div class="small-top-bar-icones subject-actions-comment"></div>-->
									</div>
									<div class="subject-actions-right">		
										<span class="how-many-subject-likes">
											<span><?php echo $subj->View; ?></span> views
										</span>					
										<span class="how-many-subject-likes">
											<span><?php echo $subj->Likes; ?> people</span> liked
										</span>	
										<span class="see-all-subject-comments">
											<a href="read-subject-guest.php?subject=<?php echo $subj->SID; ?>">View all comments</a>
										</span>
									</div>
								</div>
								<div class="subject-last-comments" id="subject-last-comment-<?php echo $subj->SID; ?>">
									<?php
										$Last_Comment = $comment->CommentsBySubject(0,1);
										if ($Last_Comment!="empty") {
											$Last_Comment = json_decode($Last_Comment);
											foreach ($Last_Comment as $com) {
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
													<!--<a class="comment-display-like" onclick="likecomment(idComment);">like</a>-->
												</div>
												<?php
											}
										}
									?>
									
								
								</div>
								
								
							</div>
							<?php
						}
					}
}
?>