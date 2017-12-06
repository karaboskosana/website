<?php
/*
API PHP SCRIPT
This script is the script of all the website that will serve us as an API 
Many function are defined on this script. Most fo the Returned results are on JSON form and accepts post requests
All the n=manipulation between the end-user and our Database  server are here
By using this script we will be able to do the Following:
	
	A- 	USER API	
		1-		Create a new user
		2-		Connect a user to the website
		3-		Update the user info email and name
		4-		Return a user information by it's ID
		5-		Change the User Avatar


	B-	SUBJECT API 	
		1-		Create a new subject
		2-		Delete a subject
		3-		Like or Unlike a subject
		4-		Return a subject information by it's ID
		5-		Return A List of Subjects on 3 Diferrent spaces: Admin space - Guest Space - User Space
		6-		Search for a subject
		7-		Return a list of subjects by room
		8-		Return a list of a user's subject
		10-		Add a view to a subject
		11-		Add a comment to a subject
		12-		Open or Close a subject
		13-		Report a subject As Spam
		14-		Choose best comment for a subject
		15-		Return a list of reported subjects on the Admin Panel
		16-		Edit a subject
		17-		Share a subject on the social networks as (facebook-google+-twitter)


	C-  COMMENT API
		1-		Create a new comment
		2-		Delete a comment
		3-		Like or Unlike a comment
		4-		Choose a commnet as the best comment
		5-		Return a list of comments by subject on 3 Diferrent spaces: Admin space - Guest Space - User Space


	D-	FOLLOWERS API
		1-		Add a new follower to a room
		2-		Delete a follower from a room
		3-		Return a list of a room followers

	
	E- 	ROOM API  
		1-		Request adding a new room ==> any user can request a new room and the Admin will approve it or not
		2-		Approve room request by Admin
		3-		Decline room request by Admin
		4-		Delete room by Admin
		5-		Return random list of the last rooms
		6-		Retrun a list of room by category
		7-		Return a list of room that a user is following
		8-		Add new follower number for a room
		9-		Add new subject number for a room


	F-  CATEGORY API
		1-		Add a new category
		2-		Delete a Category
		3-		Return a list of categories on 3 Diferrent spaces: Admin space - Guest Space - User Space
		4-		Add new Room number for a category


	G-	NOTIFICATION API
		1-		Add a new notification for a user
		2-		Delete a notification
		3-		Return a list of notifications for a user
		4-		Return the number of notifications for a user
		5-		Mark a notification as read

	
	H-	ADMIN API
		1-		Create a new admin
		2-		Delete an admin
		3-		Return a list of admins
		4-		Update admin information and profile: Avatar Name Email Password
		5-		Change admin role : Master or Normal


	I-	REPORT API
		1-		Report a subject
		2-		Check if a user has already reported a subject
		3-		Delete a report	
*/


session_start();
include '../classes/classPack.php';
$user = new User();
$subject = new Subject();
$comment = new Comment();
$slike = new Slike();
$followers = new Followers();
$room = new Room();
$category = new Category();
$notification = new Notification();
$admin = new Admin();
$report = new Report();


//################################################################################################################
//################################################################################################################
//##########################################   U S E R	    A P  I    ############################################
//################################################################################################################
//################################################################################################################
//ADDING A NEW USER
if (isset($_POST["FullName"]) && $_POST["FullName"]!="" && isset($_POST["Email"]) && $_POST["Email"]!="" && isset($_POST["Password"]) 
	&& $_POST["Password"]!="" && isset($_POST["action"]) && $_POST["action"]=="signup") {
	$user->setEmail($_POST["Email"]);
	if ($user->CheckEmail() ==0 ) {
		$user->setFullName($_POST["FullName"]);		
		$user->setPassword(sha1($_POST["Password"]));
		$user->setAvatar("normal.png");
		$user->setFacebookId(0);
		$user->InsertUser();
	}else
		echo "exist";
}


//CONNECT A USER
if (isset($_POST["EmailCon"]) && $_POST["EmailCon"]!="" && isset($_POST["PasswordCon"]) && $_POST["PasswordCon"]!="" && isset($_POST["action"]) && $_POST["action"]=="login" ) {
	$user->setEmail($_POST["EmailCon"]);
	$user->setPassword(sha1($_POST["PasswordCon"]));
	$user->ConnectUser();
}


//USER INFORMATION BY IT'S ID
if (isset($_POST["UID"]) && $_POST["UID"]!="" && isset($_POST["action"]) && $_POST["action"]=="UserInfoById") {
	$user->setUID($_POST["UID"]);
	$user->UserInfoById();
}

//USER INFORMATION BY IT'S FACEBOOOKID
if (isset($_POST["FacebookId"]) && $_POST["FacebookId"]!="" && isset($_POST["action"]) && $_POST["action"]=="UserInfoByFacebookId") {
	$user->setUID($_POST["FacebookId"]);
	$user->UserInfoByFacebookId();
}

//DELETE A USER BY IT"S ID ONLY ADMINS WHO CAN MAKE THIS ACTIONS
if(isset($_POST['UID']) && trim($_POST['UID'])!="" &&  isset($_POST['actionAdmin']) && $_POST['actionAdmin']=="deleteUser" && Token::CheckToken($_POST["token"])){
		$user->setUID($_POST['UID']);
		$user->DeleteByUID();
}

//UPDATE A THE USER STATUS ENABLED OR DISABLED
if( isset($_POST['UID']) && trim($_POST['UID'])!="" && isset($_POST['Status']) && trim($_POST['Status'])!="" &&  isset($_POST['actionAdmin']) && $_POST['actionAdmin']=="updateUserStatus" && Token::CheckToken($_POST["token"])){
		$user->setUID($_POST['UID']);
		$user->setStatus($_POST['Status']);
		$user->UpdateStatus();
}



//UPDATE USER INFO FULL NAME AND PASSWORD
if (isset($_POST["UID"]) && $_POST["UID"]!="" && isset($_POST["FullName"]) && $_POST["FullName"]!="" && isset($_POST["Email"]) && $_POST["Email"]!="" && isset($_POST["action"])
 && $_POST["action"]=="UpdateUserInfo" && Token::CheckToken($_POST["token"]) ) {
	$user->setFullName($_POST["FullName"]);
	$user->setEmail($_POST["Email"]);
	$user->setUID($_POST["UID"]);
	$user->UpdateUserInfo();
}

//update User Password
if (isset($_POST["UID"]) && $_POST["UID"]!="" && isset($_POST["Password"]) && $_POST["Password"]!="" && isset($_POST["OldPassword"]) && $_POST["OldPassword"]!="" && isset($_POST["action"])
 && $_POST["action"]=="updatePassword" && Token::CheckToken($_POST["token"])) {
		$user->setPassword(sha1($_POST["Password"]));
		$user->setUID($_POST["UID"]);
		$user->UpdateUserPassword($_POST["OldPassword"]);
}

//Update The USer Password Token and sending a mail to the user
if( isset($_POST['Email']) && trim($_POST['Email'])!="" && isset($_POST['action']) && $_POST['action']=="askResetPassword" ){
		$user->setEmail($_POST['Email']);
		if ($user->CheckEmail() !=0 ) {
			$alphabet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			$PasswordToken = str_shuffle($alphabet);	
			$user->setPasswordToken($PasswordToken);	
			$user->UpdatePasswordToken();
			echo "good";
		}else
		echo "emailnoexist";
}
//reset the passwor forgotten
if( isset($_POST['UID']) && trim($_POST['UID'])!="" && isset($_POST['Password']) && trim($_POST['Password'])!="" &&  isset($_POST['action']) && $_POST['action']=="updateForgottenPassword"){
		if ( $_POST['PasswordToken'] == $user->ReturnUserToken($_POST["UID"])) {
			$user->setUID($_POST['UID']);
			$user->setPassword($_POST['Password']);
			$user->UpdateForgottenPassword();
			echo "good";
		}else
			echo "error";
		
}




//CHANGE USER AVATAR
if ( isset($_FILES['avatar']) && $_FILES['avatar']['error']==0 ){
		if($_FILES['avatar']['size']<= 1024*1024)
			{
				$info=pathinfo($_FILES['avatar']['name']);	
				$extension=$info['extension'];
				$autorise=array('png','jpg','jpeg','PNG','JPG','JPEG','gif','GIF');
				if(in_array($extension,$autorise))
				{				
					$user->setUID($_SESSION['UID']);
					$user->UpdateUserAvatar(); 
					SaveThumbImage("../avatars/perso/",$_SESSION['UID'].".jpg",200,200,$_FILES['avatar']['name'],$_FILES['avatar']['tmp_name']);
					
				}
				 echo "success";			
			}else{
				echo "error";
			}	
			$_SESSION["Avatar"] = $_SESSION['UID'].".jpg";						
}

//################################################################################################################
//################################################################################################################
//########################################## S U B J E C  T 	A P I  ###########################################
//################################################################################################################
//################################################################################################################
//INSERT NEW SUBJECT
if ( isset($_POST["User"]) && $_POST["User"]!="" && isset($_POST["Room"]) && $_POST["Room"]!="" && 
	isset($_POST["Title"]) && $_POST["Title"]!=""  && 	isset($_POST["Text"]) && $_POST["Text"]!="" && isset($_POST["action"]) && $_POST["action"]=="addSubject" && Token::CheckToken($_POST["token"])) {

	$subject->setUser($_POST["User"]);
	$subject->setRoom($_POST["Room"]);
	$subject->setTitle($_POST["Title"]);
	$subject->setText($_POST["Text"]);
	$subject->setDate(date("d M Y H:i:s"));
	$subject->InsertSubject();
	$room->setID($_POST["Room"]);
	$room->RoomSubjectsNumber("plus");
}
//RETURN SUBJECT INFO
if (isset($_POST["SID"]) && $_POST["SID"]!="" && isset($_POST["action"]) && $_POST["action"]=="subjectInfo") {
	$subject->setSID($_POST["SID"]);
	$subject->SubjectInfo();
}


//DELETE A SUBJECT
if (isset($_POST["SID"]) && $_POST["SID"]!="" && isset($_POST["action"]) && $_POST["action"]=="deleteSubject"  && Token::CheckToken($_POST["token"])) {
	$subject->setSID($_POST["SID"]);
	$subject->DeleteSubject();

	$room->setID($_POST["Room"]);
	$room->RoomSubjectsNumber("minus");
}


//LIST OF SUBJECTS BY ROOM
if (isset($_POST["Room"]) && $_POST["Room"]!=""  && isset($_POST["action"]) && 
	$_POST["action"]=="SubjectListByRoom" && isset($_POST["first"]) && isset($_POST["second"]) && isset($_POST["filter"]) && $_POST["filter"]!="") {
	$subject->setRoom($_POST["Room"]);
	if ($subject->SubjectsByRoom($_POST["first"],$_POST["second"],$_POST["filter"])!="empty") {
		PrintSubject($subject->SubjectsByRoom($_POST["first"],$_POST["second"],$_POST["filter"]),$_SESSION["tokenClassPackApi"]) ; 	
	}else
		echo "empty";	
}

//LIST OF SUBJECTS BY ROOM ON THE GUETS PAGE
if (isset($_POST["Room"]) && $_POST["Room"]!=""  && isset($_POST["action"]) && 
	$_POST["action"]=="SubjectListByRoomGuest" && isset($_POST["first"]) && isset($_POST["second"]) && isset($_POST["filter"]) && $_POST["filter"]!="") {
	$subject->setRoom($_POST["Room"]);
	if ($subject->SubjectsByRoom($_POST["first"],$_POST["second"],$_POST["filter"])!="empty") {
		PrintSubjectGuest($subject->SubjectsByRoom($_POST["first"],$_POST["second"],$_POST["filter"])) ; 	
	}else
		echo "empty";	
}



//LIST OF SUBJECTS BY ROOM ON THE ADMIN PAGE
if (isset($_POST["Room"]) && $_POST["Room"]!=""  && isset($_POST["action"]) && 
	$_POST["action"]=="SubjectListByRoomAdmin" && isset($_POST["first"]) && isset($_POST["second"]) && isset($_POST["filter"]) && $_POST["filter"]!="") {
	$subject->setRoom($_POST["Room"]);
	if ($subject->SubjectsByRoom($_POST["first"],$_POST["second"],$_POST["filter"])!="empty") {
		PrintSubjectAdmin($subject->SubjectsByRoom($_POST["first"],$_POST["second"],$_POST["filter"]),$_SESSION["tokenClassPackApi"]) ; 	
	}else
		echo "empty";	
}

//LIST OF REPORTED SUBJECTS  THE ADMIN PAGE
if ( isset($_POST["action"]) &&	$_POST["action"]=="ReportedSubjectListAdmin" && isset($_POST["first"]) && isset($_POST["second"]) && isset($_POST["filter"]) && $_POST["filter"]!="") {
	if ($subject->ReportedSubjectsList($_POST["first"],$_POST["second"])!="empty") {
		PrintSubjectAdmin($subject->ReportedSubjectsList($_POST["first"],$_POST["second"]),$_SESSION["tokenClassPackApi"]) ; 	
	}else
		echo "empty";	
}


//LIST OF SUBJECT BY USER
//THIS WILL BE RETURNED TO A USER PROFILE PAGE
if (isset($_POST["action"]) && $_POST["action"]=="SubjectListByUser" && isset($_POST["first"]) && isset($_POST["second"]) && isset($_POST["User"]) && $_POST["User"]!="") {
	$subject->setUser($_POST["User"]);
	if ($subject->SubjectsByUser($_POST["first"],$_POST["second"])!="empty") {
		PrintSubject($subject->SubjectsByUser($_POST["first"],$_POST["second"]),$_SESSION["tokenClassPackApi"]) ; 	
	}else
		echo "empty";	
}



//CLOSE A SUBJECT
//when closing a subject no more users can comment it until you choose to re open it
//when you close a subject that meas that you have found the best comment or solution for your subject
//you can also close a subject just for a certain moment so users will not add comments to it
if (isset($_POST["SID"]) && $_POST["SID"]!="" && isset($_POST["CID"]) && $_POST["CID"]!="" && 
	isset($_POST["action"]) && $_POST["action"]=="closeSubject"  && Token::CheckToken($_POST["token"])) {
	$subject->setSID($_POST["SID"]);
	$subject->setBestComment($_POST["CID"]);
	$subject->CloseSubject();

	$comment->setCID($_POST["CID"]);
	$comment->CommentIsTheBest();
	
}

//OPEN A SUBJECT
//when opening a subject you will be able to post more comments on it
//IF this subject had a best comment that comment will be delete a s the best and you have to choose anothe rone

if (isset($_POST["SID"]) && $_POST["SID"]!="" && isset($_POST["CID"]) && $_POST["CID"]!="" && 
	isset($_POST["action"]) && $_POST["action"]=="openSubject" && Token::CheckToken($_POST["token"])) {
	$subject->setSID($_POST["SID"]);
	$subject->setBestComment($_POST["CID"]);
	$subject->OpenSubject();

	$comment->setCID($_POST["CID"]);
	$comment->CommentIsNotTheBest();
	
}


//ADD COMMENT TO A SUBJECT AND ALSO COMMENT NUMBER SUBJECT
if (isset($_POST["SID"]) && $_POST["SID"]!="" &&
	isset($_POST["action"]) && $_POST["action"]=="addComment"  && isset($_POST["Notified"]) && $_POST["Notified"]!=""
	&& isset($_POST["cText"]) && $_POST["cText"]!="" && isset($_POST["User"]) && $_POST["User"]!=""	 && Token::CheckToken($_POST["token"])) {

	$subject->setSID($_POST["SID"]);
	$subject->SubjectCommentsNumber("plus");
	
	$comment->setUser($_POST["User"]);
	$comment->setSubject($_POST["SID"]);
	$comment->setCText($_POST["cText"]);
	$comment->setCDate(date("d M Y H:i:s"));		
	$comment->InsertComment();
	//insertin a new notification so the user of the subject will know that some has commented on his subject
	//we check if the user who commented is the user who poted the subject if yes we will not insert a new notificatiosn 
	if ($_POST["Notified"]!=$_POST["User"]) {
		$notification->InsertNotification($_POST["Notified"],$_POST["User"],date("d M Y H:i:s"),"subject",$_POST["SID"],"comment","Has commented on your subject.");
	}
	
}	




//ADD LIKE NUMBER SUBJECT
if (isset($_POST["SID"]) && $_POST["SID"]!="" && isset($_POST["action"]) && $_POST["action"]=="likeSubject" 
	&& isset($_POST["operation"]) && $_POST["operation"]!="" && isset($_POST["User"]) && 
	$_POST["User"]!=""  && isset($_POST["Notified"]) && $_POST["Notified"]!="" && Token::CheckToken($_POST["token"])) {
	
	$subject->setSID($_POST["SID"]);
	$subject->SubjectLikesNumber($_POST["operation"],$_POST["User"]);
	//insertin a new notification so the user of the subject will know that some has commented on his subject
	//we check if the user who liked is the user who poted the subject if yes we will not insert a new notification
	//and checking that a user has like and not unliked 
	if ($_POST["Notified"]!=$_POST["User"] && $_POST["operation"]=="plus") {
		$notification->InsertNotification($_POST["Notified"],$_POST["User"],date("d M Y H:i:s"),"subject",$_POST["SID"],"like","Has liked your subject.");
	}
}

//EDIT SUBJECT
if (isset($_POST["SID"]) && $_POST["SID"]!="" && isset($_POST["action"]) && $_POST["action"]=="editSubject" && isset($_POST["Text"]) && $_POST["Text"]!="" && isset($_POST["Title"]) && $_POST["Title"]!="" && Token::CheckToken($_POST["token"])) {

	$subject->setSID($_POST["SID"]);
	$subject->setTitle($_POST["Title"]);
	$subject->setText($_POST["Text"]);

	$subject->EditSubject();
}


//ADD VIEW TO THE SUBJECT
if (isset($_POST["SID"]) && $_POST["SID"]!="" && isset($_POST["action"]) && $_POST["action"]=="addView" ) {
	$subject->setSID($_POST["SID"]);
	$subject->AddView();
	echo "ss";
}


//################################################################################################################
//################################################################################################################
//########################################## C O M M E N T	   A P  I ############################################
//################################################################################################################
//################################################################################################################

//LIST OF COMMENTS BY SUBJECTS
if (isset($_POST["Subject"]) && $_POST["Subject"]!=""  && isset($_POST["action"]) && $_POST["action"]=="CommentsListBySubject" && 
	isset($_POST["first"]) && isset($_POST["second"])) {
	$comment->setSubject($_POST["Subject"]);
if ($comment->CommentsBySubject($_POST["first"],$_POST["second"])!="empty") {
		$subject = new Subject();
		$subject->setSID($_POST["Subject"]);	
		$subject = json_decode($subject->SubjectInfo());
		PrintComment($comment->CommentsBySubject($_POST["first"],$_POST["second"]),$subject,$_SESSION["tokenClassPackApi"]);	
}else
	echo "empty";

}

//LIST OF COMMENTS BY SUBJECTS ON THE GUEST PAGE
if (isset($_POST["Subject"]) && $_POST["Subject"]!=""  && isset($_POST["action"]) && $_POST["action"]=="CommentsListBySubjectGuest" && 
	isset($_POST["first"]) && isset($_POST["second"])) {
	$comment->setSubject($_POST["Subject"]);
if ($comment->CommentsBySubject($_POST["first"],$_POST["second"])!="empty") {
		$subject = new Subject();
		$subject->setSID($_POST["Subject"]);	
		$subject = json_decode($subject->SubjectInfo());
		PrintCommentGuest($comment->CommentsBySubject($_POST["first"],$_POST["second"]),$subject);	
}else
	echo "empty";

}



//LIST OF COMMENTS BY SUBJECTS ON THE ADMIN PAGE
if (isset($_POST["Subject"]) && $_POST["Subject"]!=""  && isset($_POST["action"]) && $_POST["action"]=="CommentsListBySubjectAdmin" && 
	isset($_POST["first"]) && isset($_POST["second"])) {
	$comment->setSubject($_POST["Subject"]);
if ($comment->CommentsBySubject($_POST["first"],$_POST["second"])!="empty") {
		$subject = new Subject();
		$subject->setSID($_POST["Subject"]);	
		$subject = json_decode($subject->SubjectInfo());
		PrintCommentAdmin($comment->CommentsBySubject($_POST["first"],$_POST["second"]),$subject,$_SESSION["tokenClassPackApi"]);	
}else
	echo "empty";

}



//ADD LIKE NUMBER COMMENT
if (isset($_POST["CID"]) && $_POST["CID"]!="" && isset($_POST["SID"]) && $_POST["SID"]!="" &&  isset($_POST["action"]) && $_POST["action"]=="unlineAndlikeComment" && 
	isset($_POST["operation"]) && $_POST["operation"]!="" && isset($_POST["User"]) && $_POST["User"]!="" && isset($_POST["Notified"]) && $_POST["Notified"]!=""  && Token::CheckToken($_POST["token"])) {
	
	$comment->setCID($_POST["CID"]);
	$comment->CommentLikesNumber($_POST["operation"],$_POST["User"]);
	if ($_POST["Notified"]!=$_POST["User"] && $_POST["operation"]=="plus") {
		$notification->InsertNotification($_POST["Notified"],$_POST["User"],date("d M Y H:i:s"),"subject",$_POST["SID"],"like","Has liked your comment on a subject.");
	}
}

//DELETE COMMENT 
if (isset($_POST["CID"]) && $_POST["CID"]!="" && isset($_POST["SID"]) && $_POST["SID"]!="" && isset($_POST["action"]) && $_POST["action"]=="deleteComment"  && Token::CheckToken($_POST["token"])) {
	$comment->setCID($_POST["CID"]);
	$comment->DeleteComment();
	$subject->setSID($_POST["SID"]);
	$subject->SubjectCommentsNumber("minus");
}

//################################################################################################################
//################################################################################################################
//###################################### F O L L O W E R S 	   A P  I ############################################
//################################################################################################################
//################################################################################################################

//ADD NEW FOLLOWING
if (isset($_POST["User"]) && $_POST["User"]!="" && isset($_POST["Room"]) && $_POST["Room"]!="" && isset($_POST["action"]) 
	&& $_POST["action"]=="addFollower") {
	$followers->setUser($_POST["User"]);
	$followers->setRoom($_POST["Room"]);
	if ($followers->CheckFollow()==0){
		$followers->InsertFollow();
		$room->setID($_POST["Room"]);
		$room->RoomFollowersNumber("plus");
	} 
		
		

}

//DELETE NEW FOLLOWING
if (isset($_POST["User"]) && $_POST["User"]!="" && isset($_POST["Room"]) && $_POST["Room"]!="" && isset($_POST["action"]) && $_POST["action"]=="deleteFollower") {
	$followers->setUser($_POST["User"]);
	$followers->setRoom($_POST["Room"]);
	$followers->DeleteFollow();
	$room->setID($_POST["Room"]);
	$room->RoomFollowersNumber("minus");
}
//FOLLOWERS LIST
if (isset($_POST["Room"]) && $_POST["Room"]!="" && isset($_POST["first"]) && $_POST["first"]!="" && isset($_POST["second"]) && $_POST["second"]!="" && isset($_POST["action"]) && $_POST["action"]=="followersList") {
	$followers->setUser($_POST["User"]);
	$followers->FollowersList($_POST["first"],$_POST["second"]);
}



//################################################################################################################
//################################################################################################################
//##########################################   R O O M 	   	 A P  I   ############################################
//################################################################################################################
//################################################################################################################
//REQUEST A ROOM
if (isset($_POST["User"]) && $_POST["User"]!="" && isset($_POST["Title"]) && $_POST["Title"]!="" 
	&& isset($_POST["Category"]) && $_POST["Category"]!="" && isset($_POST["action"]) && $_POST["action"]=="addRoom" && Token::CheckToken($_POST["token"])){
		$room->setUser($_POST["User"]);
		$room->setTitle($_POST["Title"]);
		$room->setRDate(date("d M Y H:i:s"));
		$room->setCategory($_POST["Category"]);
		$room->RequestRoom();

		if($_FILES['avatarRoom']['size']<= 1024*1024)
			{
				$info=pathinfo($_FILES['avatarRoom']['name']);	
				$extension=$info['extension'];
				$autorise=array('png','jpg','jpeg','PNG','JPG','JPEG','gif','GIF');
				if(in_array($extension,$autorise))
				{		
					$room->ReturnRoomId();		
					$room->UpdateImage();
					SaveThumbImage("../avatars/room/",$room->getID().".jpg",230,200,$_FILES['avatarRoom']['name'],$_FILES['avatarRoom']['tmp_name']);
				}
							
			}else{
				echo "error";
		}	

		header("Location: ".$_SERVER["HTTP_REFERER"]);						
}

//ACCEPT A ROOM BY ADMIN
if (isset($_POST["ID"]) && $_POST["ID"]!="" && isset($_POST["actionAdmin"]) && $_POST["actionAdmin"]=="acceptRoom" && isset($_POST["CATID"]) && $_POST["CATID"]!="" && Token::CheckToken($_POST["token"])) {
	$room->setID($_POST["ID"]);
	$room->AdminAcceptRoom();
	$category->setCATID($_POST["CATID"]);
	$category->RoomsNumber("plus");
}

//DO NOT ACCEPT A ROOM BY ADMIN
if (isset($_POST["ID"]) && $_POST["ID"] && isset($_POST["actionAdmin"]) && $_POST["actionAdmin"]=="deleteRoom" && Token::CheckToken($_POST["token"])) {
	$room->setID($_POST["ID"]);
	$room->AdminDeleteRoom();
}

//ROOM LIST RANDOM
if (isset($_POST["first"]) && $_POST["first"]!="" && isset($_POST["second"]) && $_POST["second"]!="" && 
 isset($_POST["action"]) && $_POST["action"]=="roomList") {
	echo $room->RoomList($_POST["first"],$_POST["second"]);
}

//ROOM LIST OF THE ADMIN "THE PENDING ROOMS" 
if (isset($_POST["first"]) && $_POST["first"]!="" && isset($_POST["second"]) && $_POST["second"]!="" && 
 isset($_POST["action"]) && $_POST["action"]=="roomListPending") {
	echo $admin->PendingRoomList($_POST["first"],$_POST["second"]);
}

//RETURN A LIST OF ROOMS BY CATEGORY
if (isset($_POST["first"]) && $_POST["first"]!="" && isset($_POST["second"]) && $_POST["second"]!="" && 
 isset($_POST["action"]) && $_POST["action"]=="roomListByCategory" && isset($_POST["Category"]) && $_POST["Category"]!="") {
	$room->setCategory($_POST["Category"]);
	echo $room->RoomListByCategory($_POST["first"],$_POST["second"]);
}


//RETURNS A LIST OF ROOMS THAT A USER IS FOLLOWING
if (isset($_POST["first"]) && $_POST["first"]!="" && isset($_POST["second"]) && $_POST["second"]!="" && 
 isset($_POST["action"]) && $_POST["action"]=="roomListByUser" && isset($_POST["User"]) && $_POST["User"]!="") {
	
	echo $room->RoomsListByUser($_POST["first"],$_POST["second"],$_POST["User"]);
}


//################################################################################################################
//################################################################################################################
//######################################  C A T E G O R Y  	 A P  I   ############################################
//################################################################################################################
//################################################################################################################
//INSERT NEW CATEGORY
if (isset($_POST["Name"]) && $_POST["Name"]!="" && isset($_POST["action"]) && $_POST["action"]=="addCategory"  && Token::CheckToken($_POST["token"])){
		$category->setName(ucfirst($_POST["Name"]));
		$category->InsertCategory();

		if($_FILES['avatarCategory']['size']<= 1024*1024)
			{
				$info=pathinfo($_FILES['avatarCategory']['name']);	
				$extension=$info['extension'];
				$autorise=array('png','jpg','jpeg','PNG','JPG','JPEG','gif','GIF');
				if(in_array($extension,$autorise))
				{		
					SaveThumbImage("../avatars/category/",$_POST["Name"].".jpg",230,200,$_FILES['avatarCategory']['name'],$_FILES['avatarCategory']['tmp_name']);
				}
							
			}else{
				echo "error";
		}		
		header("Location: ".$_SERVER["HTTP_REFERER"]);						
}

//RETTURN A LIST OF CATEGORIS 
if (isset($_POST["first"]) && $_POST["first"]!="" && isset($_POST["second"]) && $_POST["second"]!="" && 
 isset($_POST["action"]) && $_POST["action"]=="CategoryList" ) {
	echo $category->CategoryList($_POST["first"],$_POST["second"]);
}


//DELETE CATEGORY
if (isset($_POST["CATID"]) && $_POST["CATID"]!="" && isset($_POST["actionAdmin"]) && $_POST["actionAdmin"]=="deleteCategory" ){
		$category->setCATID($_POST["CATID"]);
		$category->DeleteCategory();						
}

//################################################################################################################
//################################################################################################################
//###############################   N O T I F I C A T I O N 	A P  I   #########################################
//################################################################################################################
//################################################################################################################

//Mark all a user notification as read and it will be deleted fro the database
if (isset($_POST["User"]) && $_POST["User"]!="" && isset($_POST["action"]) && $_POST["action"]=="markAsRead"  && Token::CheckToken($_POST["token"])) {
	$notification->MarkAsRead($_POST["User"]);
}

//Delete one single notification
if (isset($_POST["User"]) && $_POST["User"]!="" && isset($_POST["NotifId"]) && $_POST["NotifId"]!="" &&  isset($_POST["action"]) && $_POST["action"]=="deleteNotification"  && Token::CheckToken($_POST["token"])) {
	$notification->DeleteNotification($_POST["NotifId"],$_POST["User"]);
}

//load moore of a user notifications
if (isset($_POST["User"]) && $_POST["User"]!="" && isset($_POST["first"]) && $_POST["first"]!="" && isset($_POST["second"]) && $_POST["second"]!="" &&  
	isset($_POST["action"]) && $_POST["action"]=="loadNotification" && Token::CheckToken($_POST["token"])) {
	$notification->GeneralNotificationList($_POST["User"],$_POST["first"],$_POST["second"],$_POST["token"]);
}

//check if there new notifications ofr the connected user 
if (isset($_POST["action"]) && $_POST["action"]=="checkNotification") {
	$user->checkNotification($_SESSION["UID"]);
}




//################################################################################################################
//################################################################################################################
//########################################## A D M I N  	   A P  I ############################################
//################################################################################################################
//################################################################################################################
//ADMIN 
//We will create some script to Manage the Admin panel

// Add anew  Admin 
if ( isset($_POST["aFullName"]) && isset($_POST["aEmail"]) && isset($_POST["aPassword"]) && isset($_POST["aRole"]) &&  $_POST["aFullName"]!=""	&&
$_POST["aEmail"]!="" && $_POST["aPassword"]!="" && $_POST["aRole"]!="" && isset($_POST["actionAdmin"]) && $_POST["actionAdmin"]=="insertAdmin"  && Token::CheckToken($_POST["token"])) {
	$admin->setAFullName($_POST["aFullName"]);
	$admin->setAEmail($_POST["aEmail"]);
	$admin->setAPassword(sha1($_POST["aPassword"]));
	$admin->setARole($_POST["aRole"]);
	$admin->InsertAdmin();
}


//Connect an admin to the Admin Panel

if (isset($_POST["aEmail"]) && isset($_POST["aPassword"]) && $_POST["aEmail"]!="" && $_POST["aPassword"]!="" && isset($_POST["action"]) && $_POST["action"]=="adminLogin") {
	$admin->setAEmail($_POST["aEmail"]);
	$admin->setAPassword(sha1($_POST["aPassword"]));
	$admin->ConnectAdmin();	
}


//Change a Role of an Admin
if (isset($_POST["aRole"]) && isset($_POST["AID"]) && $_POST["aRole"]!="" && $_POST["AID"]!="" && isset($_POST["actionAdmin"]) && $_POST["actionAdmin"]=="changeAdminRole"  && Token::CheckToken($_POST["token"])) {
	$admin->setAID($_POST["AID"]);
	$admin->setARole($_POST["aRole"]);
	$admin->ChangeAdminRole();	
}


//Change Admin info
if (isset($_POST["aFullName"]) && $_POST["aFullName"]!="" && isset($_POST["aEmail"]) &&  $_POST["aEmail"]!="" && 
	isset($_POST["actionAdmin"]) && $_POST["actionAdmin"]=="updateAdminInfo"  && Token::CheckToken($_POST["token"])) {
	$admin->setAID($_SESSION["AID"]);
	$admin->setAFullName($_POST["aFullName"]);
	$admin->setAEmail($_POST["aEmail"]);
	$admin->setAPassword($_POST["aPasswordNew"]);
	$admin->UpdateAdminInfo($_POST["aPasswordOld"]);
}




//Delete An Admin 
if (isset($_POST["AID"]) && $_POST["AID"]!="" && isset($_POST["actionAdmin"]) && $_POST["actionAdmin"]=="deleteAdmin"  && Token::CheckToken($_POST["token"])) {
	$admin->setAID($_POST["AID"]);
	$admin->DeleteAdmin();	
}


//Return The List of Admins
if (isset($_POST["first"]) && $_POST["first"]!="" && isset($_POST["second"]) && $_POST["second"]!="" && isset($_POST["actionAdmin"]) && $_POST["actionAdmin"]=="adminList") {
	$Admin_list = $admin->AdminsList($_POST["first"], $_POST["second"]);
	if ($Admin_list!="empty") {
		PrintAdmin($Admin_list,$_SESSION["tokenClassPackApi"]);
	
	}else
		echo "empty";
		
}

//Return The List of Admins
if (isset($_POST["first"]) && $_POST["first"]!="" && isset($_POST["second"]) && $_POST["second"]!="" && isset($_POST["filter"]) && $_POST["filter"]!=""  && isset($_POST["actionAdmin"]) && $_POST["actionAdmin"]=="usersList") {
	$User_list = $admin->UsersList($_POST["first"], $_POST["second"],$_POST["filter"]);
	if ($User_list!="empty") {
		PrintUser($User_list,$_SESSION["tokenClassPackApi"]);
	
	}else
		echo "empty";
		
}



//Change an Admin Avatar
if ( isset($_FILES['avatarAdmin']) && $_FILES['avatarAdmin']['error']==0 ){
		if($_FILES['avatarAdmin']['size']<= 1024*1024)
			{
				$info=pathinfo($_FILES['avatarAdmin']['name']);	
				$extension=$info['extension'];
				$autorise=array('png','jpg','jpeg','PNG','JPG','JPEG','gif','GIF');
				if(in_array($extension,$autorise))
				{				
					$admin->setAID($_SESSION['AID']);
					$admin->UpdateAdminAvatar(); 
					SaveThumbImage("../avatars/admin/",$_SESSION['AID'].".jpg",200,200,$_FILES['avatarAdmin']['name'],$_FILES['avatarAdmin']['tmp_name']);
					
				}
				 echo "success";			
			}else{
				echo "error";
			}	
			$_SESSION["aAvatar"] = $_SESSION['AID'].".jpg";						
}

//Return Website info to the admin panel
if (isset($_POST["actionAdmin"]) && $_POST["actionAdmin"]=="webSiteInfo") {
	echo $admin->WebSiteInfo();
}


//################################################################################################################
//################################################################################################################
//########################################## R E P O R T 	   A P  I ############################################
//################################################################################################################
//################################################################################################################

//Add or delete a report from a subject
if (isset($_POST["SubjectId"]) && $_POST["SubjectId"]!="" && isset($_POST["UserId"]) && $_POST["UserId"]!="" && isset($_POST["operation"]) && $_POST["operation"]!="" && isset($_POST["action"]) && $_POST["action"]=="reportSubject") {	
	$subject->setSID($_POST["SubjectId"]);
	$subject->ReportSubject($_POST["operation"],$_POST["UserId"]);	
}






?>