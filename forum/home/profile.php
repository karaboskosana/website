<?php
include '../includes/session.php';
/*
Profile page :
in this page the user will be able to see all hissubjects and rooms that he si following he can also configure his profile 
and change the name email and avatar
*/

?>
<!DOCTYPE html>
<html>
<head>
	<title>Profile</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../style/sharedStyle.css" />
	<link rel="stylesheet" type="text/css" href="../style/style.css" />
	<link rel="icon" type="image/png" href="../images/forumLogo.png" />	
	<script type="text/javascript" src="../script/jquery.js"></script>
	<script type="text/javascript" src="../script/jquery-ui.js"></script>
	<script type="text/javascript" src="../script/main.js"></script>
	<script type="text/javascript" src="../script/nicEdit.js"></script>
	<script type="text/javascript" src="../script/ajaxupload.js"></script>
</head>
<body>
	<?php
		//importing the menu
		include '../includes/menu.php';
	?>
	<div id="subject-room-top-div">
		<div id="subject-room-div-insider">	
			<div id="room-name-display" style="margin-top:33px;">Profile</div>	
			<div id="profile-show-what">
				<div id="profile-my-subjects" class="active profile-action">My subjects</div>
				<div id="profile-my-subscriptions" class="profile-action">My subscriptions</div>
			</div>
		</div>
	</div>
	<!--#######################################################################################################-->
	<div id="container" style=" margin-top:20px; padding-top:20px;">
		<div id="profile_image_info">
			<div id="profile_image_div">
				<div id="profile_image_change">
					<div class="small-top-bar-icones"></div>
					<span>Update picture</span>
				</div>
				<img src="../avatars/perso/<?php echo $_SESSION['Avatar'] ?>" id="profile_image" />	
				<div class="loading" id="change-avatar-loading"></div>		
			</div>				
			<span id="profile_name_display"><?php echo $_SESSION["FullName"]; ?></span>
			<span id="profile_email_display"><?php echo $_SESSION["Email"]; ?></span>
			<div id="profile_update_inputs">
				<input type="text" id="user-name-update" value="<?php echo $_SESSION["FullName"]; ?>">
				<input type="text" id="user-email-update" value="<?php echo $_SESSION["Email"]; ?>">
			</div>		
			<div id="edit_profile" class="GreyGradient">Edit profile</div>
			
			<div id="password_update_inputs">
				<input type="password" id="OldPassword" placeholder="Old Password">
				<input type="password" id="NewPassword" placeholder="New Password">
			</div>
			
			<div class="red_error_item" id="userno-match"  style="margin-left:10px; margin-top:0px; height:20px; width:150px; float:left">Wrong old password</div>			
			<div id="update_password" class="GreyGradient">Update password</div>
		</div>

		<!--########################### PROFILE SUBSCRIPTIONS AND SUBJECTS-->
		<div id="profile-right">
			<div id="profile-right-rooms">
				<div id="theList-of-my-rooms">
					<?php
						//display the last 9 rooms that a user is subscribed to	
						$room = new Room();		
						$Rooms_list = $room->RoomsListByUser(0,9,$_SESSION["UID"]);//Getting the Rooms list
						if ($Rooms_list!="empty") {
							$Rooms_list = json_decode($Rooms_list);
							foreach ($Rooms_list as $Ro) {//using a foreach loop to display the list of the last 9 rooms
								?>
								<a href="subjects.php?room=<?php echo $Ro->ID; ?>" class="room-div">
									<div class="room-top" style="background-image:url(../avatars/room/<?php echo $Ro->Image; ?>)">
										<div class="room-category-name"><?php echo $Ro->Name; ?></div>
									</div>
									<div class="room-bottom">
										<span class="room-name"><?php echo $Ro->Title; ?></span>
										<div class="room-information">
											<div class="room-indormation-insider-row">
												<div class="small-top-bar-icones room-subjects-icon"></div>
												<span class="category-room-number"><?php echo $Ro->Subjects; ?> Subject</span>
											</div>
											<div class="room-indormation-insider-row">
												<div class="small-top-bar-icones room-followers-icon"></div>
												<span class="category-room-number"><?php echo $Ro->Followers; ?> Follower</span>
											</div>							
										</div>
									</div>
								</a>
								<?php

							}
						}	
					?>			
				</div>

				<div class="see-more-wide-div" id="wide-load-my-rooms">
					<a class="see-more-other-pages" id="load-more-rooms-profile">
						Load more <span>&#65516;</span>
						<div class="loading loadin-more"></div>
					</a>	
				</div>
			</div>
				
			
			<div id="profile-right-subjects">
				<div id="theList-of-my-subjects" style="margin-top:20px; margin-left:20px;">
					<?php
						$subject = new Subject();
						$subject->setUser($_SESSION["UID"]);
						$Subjects_List = $subject->SubjectsByUser(0,9);
						//this function will print us the subjects 
						PrintSubject($Subjects_List,$token);
					?>	
				</div>
					<div class="see-more-wide-div" id="wide-load-my-subjects">
					<a class="see-more-other-pages" id="load-more-subjects-profile">
						Load more <span>&#65516;</span>
						<div class="loading loadin-more loading-subject-"></div>
					</a>	
				</div>
			</div>
<div id="BIGBOSS">	
		<!--EDIT A USER SUBJECT -->
		<div id="edit-subject-div" >
			<div class="edit-subject-header">
				Edit my subject
			</div>
			<div class="edit-subject-devider">
				------------------------------------- <span>$$$</span> -------------------------------------
			</div>
			<div class="edit-subject-inputs">
				<input type="text" name="edit-subject-name" id="edit-subject-name"  placeholder="Title">
				<input type="hidden" name="edit-subject-id" id="edit-subject-id">
				<div id="edit-subject-text-div">
					<textarea name="edit-subject-text" id="edit-subject-text"  placeholder="Subject"></textarea>
				</div>
				<span class="red-error" id="error-edit-subject">All fields required </span>	
				<div class="edit-subject-button" id="edit-subject-button">
					EDIT
					<div class="edit-subject-success"></div>
					<div class="edit-subject-loading loading"></div>
				</div>
			</div>
			<div class="edit-subject-footer">
				P.S : Try to make it more clear.</a><br/>
				<div class="cancel-edit-subject"  title="hide"></div>
			</div>			
		</div>	



	</div>
		<script type="text/javascript">
			$(function() {
					//this function will update the User Password
					var update_password_shown = false;
					$("#password_update_inputs").hide();
					$("#userno-match").hide();
					$("#update_password").click(function(){
						if (update_is_shown==false) {
							$("#password_update_inputs").slideDown(400);
							update_password_shown = true;	
						} 
						if(update_password_shown==true){
							var Password = $("#NewPassword").val();
							var OldPassword = $("#OldPassword").val();
							if ($.trim(Password)!="" && $.trim(OldPassword)!="") {

							$.ajax({//Ajax Request to the server
									type:"POST",
									url:"../api/api.php",
									data:{UID:'<?php echo $_SESSION["UID"]; ?>',Password:Password,OldPassword:OldPassword,action:"updatePassword",token:'<?php echo $token; ?>'},//Data that will be sent to the server
									success:function(data){
										if ($.trim(data)=="nomatch") {		
											$("#userno-match").fadeIn(100,function(){
												$("#userno-match").fadeOut(5000);	
											});
										}else{
											$("#password_update_inputs").slideUp(400);
											update_password_shown = false;
										}			
									}
								});
								
							}
							
						}
						
					});
			//USING THE NICEDITOR A WYSIWYG 
			new nicEditor({iconsPath : '../images/nicEditorIcons.gif',
				buttonList : ['save','bold','italic','underline','left','center','right','ol','ul','indent','outdent','upload','link','unlink','forecolor']
			}).panelInstance('edit-subject-text');

			//ANIMATIONS TO SHOW AND HIDE THE CREATE NEW SUBJECT DIVS
			$("#BIGBOSS").hide();
		
			//Hiding the create new subject div when you click escape
				$(document).keyup(function(e){
					if(e.keyCode==27){
						hideEditSubjectDiv();
					}
				});
				$(".cancel-create-subject").click(function(){
					hideCreateNewSubjectDiv();
				});



				//show and hide update input divs
				var update_is_shown = false;
				$("#profile_update_inputs").hide();	
				$("#edit_profile").click(function(){			
					if (update_is_shown==false) {
						$("#profile_update_inputs").slideDown(400);
						update_is_shown = true;	
					}
					else if (update_is_shown==true) {
						$("#profile_update_inputs").slideUp(400);
						update_is_shown = false;	
						//checking if the user full name and email were changed
						//if yes we will send an Ajax request to the server to update the name and email
						var FullName =  $("#user-name-update").val();
						var Email = $("#user-email-update").val();
						if (FullName!=$("#profile_name_display").html() || Email !=$("#profile_email_display").html() ) {
							$.ajax({
								type:"POST",
								url:"../api/api.php",
								data:{Email:Email,FullName:FullName,action:"UpdateUserInfo",UID:'<?php echo $_SESSION["UID"] ?>',token:'<?php echo $token; ?>'},
								success:function(data){	
									$("#profile_name_display").html(FullName);
									$("#UserNameTopBar").html(FullName);
									$("#profile_email_display").html(Email);
								}
								
							});
						}

					}
				});

				//change what is shown on the profile page
				$("#profile-my-subscriptions").click(function(){
					$("#profile-show-what").children("div").attr("class","profile-action");
					$(this).attr("class","active profile-action");
					$("#profile-right-subjects").fadeOut(100,function(){
						$("#profile-right-rooms").slideDown(700);	
					});
				});

				$("#profile-my-subjects").click(function(){
					$("#profile-show-what").children("div").attr("class","profile-action");
					$(this).attr("class","active profile-action");
					$("#profile-right-rooms").fadeOut(100,function(){
						$("#profile-right-subjects").slideDown(700);	
					});					
				});

				//SCRIPT TO CHANGE THE USER AVATAR USING AJAX
				//THIS SCRIPT WILL CHANGE THE USER AVATAR WITHOUT REFRESHING THE PAGE
				$("#change-avatar-loading").hide();
				ChangeProfileAvatar();
				function ChangeProfileAvatar(){
					var btnUpload=$("#profile_image_change");
					new AjaxUpload(btnUpload, {
						action: '../api/api.php',
						name: 'avatar',
						onSubmit: function(file, ext){
							$("#change-avatar-loading").show();
							 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
								return false;
							}
						},
						onComplete: function(file, response){	
							if(response=="success"){
								$("#change-avatar-loading").hide();
								$('#profile_image').remove();
								$('#profile_image_div').append('<img src="../avatars/perso/<?php echo $_SESSION["Avatar"]; ?>"  id="profile_image"  />');
							} 													
						}
					});
				}
		});
		

		//LOADING WHAT IS SHOWN ON THE PROFILE PAGE
		//YOU CAN MANAGE BOTH YOUR ROOM SUBSCRIPTION AND THE SUBJECTS THAT YOU HAVE POSTED
		$('#profile-right-rooms').hide();
					//Script to load more rooms that the user is subscribed to
					var firstRoom = 0;					
					$(".loadin-more").hide();
					$("#load-more-rooms-profile").click(function(){
						$(".loadin-more").show();
						firstRoom+=9;
						var secondRoom = 9;
						$.ajax({
							type:"POST",
							url:"../api/api.php",					
							data:{first:firstRoom,second:secondRoom,action:"roomListByUser",User:'<?php echo $_SESSION["UID"]; ?>'},
							success:function(data){
								if($.trim(data)!="empty"){
									data = JSON.parse(data);
									data.forEach(function(ro){
										//DISPLAYING THE LOADED ROOMS OF THE ACTUAL USER PROFILE
										var roomDisplay = '<a href="subjects.php?room='+ro["ID"]+'" class="room-div"><div class="room-top" style="background-image:url(../avatars/room/'+ro["Image"]+')"><div class="room-category-name">'+ro["Name"]+'</div></div><div class="room-bottom">	<span class="room-name">'+ro["Title"]+'</span><div class="room-information">	<div class="room-indormation-insider-row"><div class="small-top-bar-icones room-subjects-icon"></div><span class="category-room-number">'+ro["Subjects"]+' Subject</span></div><div class="room-indormation-insider-row"><div class="small-top-bar-icones room-followers-icon"></div><span class="category-room-number">'+ro["Followers"]+' Follower</span></div></div></div></a>';
										$("#theList-of-my-rooms").append(roomDisplay);			
									});
								}else if(data=="empty")
									$("#wide-load-my-rooms").fadeOut(100);
								
								$(".loadin-more").hide();
							}		
						});
						
					});

					//script to load more  of my subjects
					var firstSubject=0;
					$(".loading-subject-").hide();
					$("#load-more-subjects-profile").click(function(){
						firstSubject+=9;
						var secondSubject = 9;					
						$(".loading-subject-").show();
						$.ajax({
							type:"POST",
							url:"../api/api.php",
							data:{first:firstSubject,second:secondSubject,action:"SubjectListByUser",User:'<?php echo $_SESSION["UID"]; ?>'},
							success:function(data){
								if ($.trim(data)!="empty") {
									$("#theList-of-my-subjects").append(data);
								}else
									$("#wide-load-my-subjects").hide();

								$(".loading-subject-").hide();

							}
						});
					});
	

				</script>

		</div>

	</div>

</body>
</html>