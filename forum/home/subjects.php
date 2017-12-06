<?php
include '../includes/session.php';
//this page will give us a list of subject that are under a specific room
$room = new Room();
if (isset($_GET["room"]) && $_GET["room"]!="") {
	$room->setID($_GET["room"]);
	if($room->CheckRoom()==0)//we check if the rook is really exisiting on the database
		header("Location: ../home/");
	else{
		$room->RommInfo();
		
	}
}else
	header("Location: ../home/");

?>
<!DOCTYPE html>
<html>
<head>
	<title>Subjects</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../style/sharedStyle.css" />
	<link rel="stylesheet" type="text/css" href="../style/style.css" />
	<link rel="icon" type="image/png" href="../images/forumLogo.png" />	
	<script type="text/javascript" src="../script/jquery.js"></script>
	<script type="text/javascript" src="../script/jquery-ui.js"></script>
	<script type="text/javascript" src="../script/nicEdit.js"></script>
	<script type="text/javascript" src="../script/main.js"></script>
</head>
<body>
	<?php
		//importing the menu
		include '../includes/menu.php';
	?>
	<!--#######################################################################################################-->
	<div id="subject-room-top-div">
		<div id="subject-room-div-insider">
			<div id="room-image-display" style="background-image:url(../avatars/room/<?php echo $room->getImage(); ?>)"></div>
			<div id="room-name-display"><?php echo $room->getTitle(); ?></div>

			<div id="room-numbers-info">
				<div id="room-subject-number-display"><?php echo $room->getSubjects(); ?> Subjects</div>
				<div id="room-subject-number-display"><span id="Followers-number"><?php echo $room->getFollowers(); ?></span> Follower</div>
			</div>
			
			<?php
				//dsiplayin a small list of users that follows this room
				$followers = new Followers();
				$followers->setRoom($_GET["room"]);
				$Followers_List = $followers->FollowersList(0,7);
				if ($Followers_List!="empty") {	?>
				<div id="room-some-followers">
						<?php
						$Followers_List = json_decode($Followers_List);
							foreach ($Followers_List as $follower) {
								?>
									<img src="../avatars/perso/<?php echo $follower->Avatar; ?>" title="<?php echo $follower->FullName ?>">
								<?php
							}	?>
				</div>
				<?php } ?>			
				

			<div id="room-subject-sort-by">
				<div class="loading loadin-filter"></div>
				<span>Sort by :</span>
				<select id="Filter-Subjects">
					<option for="date" value="date">Date</option>
					<option for="views" value="views">Views</option>
					<option for="likes" value="likes">Likes</option>
					<option for="closed" value="closed">Closed</option>
					<option for="pending" value="pending">Pending</option>
				</select>
			</div>

			<div id="subject-and-room-responsive-action">
				<div id="create-new-subject">Create new subject</div>
					<?php 
						//Code section to see if the connected users is following this room
						  $followers = new Followers();
						  $followers->setUser($_SESSION["UID"]);
						  $followers->setRoom($_GET["room"]);
						  if($followers->CheckFollow()==0){
					?>
						<div id="Follow-Room" onclick="FollowRoom(this,<?php echo $_GET['room'] ?>);">Follow</div>
					<?php
						  }else{
					?>
						<div id="Follow-Room" onclick="unFollowRoom(this,<?php echo $_GET['room'] ?>);">unFollow</div>
					<?php } ?>
					
				</div>
			</div>
			
	</div>



	<div id="container">
		<div id="left-container">
			<div id="subjects-list">
				<div id="theListOsSubjects">
					<?php
						$subject = new Subject();
						$subject->setRoom($_GET["room"]);
						if($subject->SubjectsByRoom(0,1,'date')!="empty"){
							$Subjects_List = $subject->SubjectsByRoom(0,9,'date');
							//this function will print us the subjects 
							PrintSubject($Subjects_List,$token);
							}else{
							?>	
						<span id="No-Result"><strong>No subjects on this room for the moment</strong></span>
						<?php } ?>
				</div>
				<div class="see-more-wide-div">
					<a class="see-more-other-pages" id="load-more-subjects">
						Load more <span>&#65516;</span>
						<div class="loading loadin-more"></div>
					</a>	
				</div>
			</div>
		</div>

		<div id="side_bar_right">
			<?php include '../includes/right_side_bar.php'; ?>
		</div>
	</div>


	<div id="BIGBOSS">	
		<!--CREATE NEW SUBJECT TO THE ACTUAL VIEWED ROOM-->
		<div id="create-subject-div" >
			<div class="create-subject-header">
				Start new subject
			</div>
			<div class="create-subject-devider">
				------------------------------------- <span>$$$</span> -------------------------------------
			</div>
			<div class="create-subject-inputs">
				<input type="text" name="create-subject-name" id="create-subject-name"  placeholder="Title">
				<div id="create-subject-text-div">
					<textarea name="create-subject-text" id="create-subject-text"  placeholder="Subject"></textarea>
				</div>
				<span class="red-error" id="error-create-subject">All fields required </span>	
				<div class="create-subject-button" id="create-subject-button">
					CREATE
					<div class="create-subject-success"></div>
					<div class="create-subject-loading loading"></div>
				</div>
			</div>
			<div class="create-subject-footer">
				P.S : DO NOT re-post exsiting subjects.</a><br/>
				<div class="cancel-create-subject"  title="hide"></div>
			</div>			
		</div>	


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
				<div id="edit-subject-text-div">
					<textarea name="edit-subject-text" id="edit-subject-text"  placeholder="Subject"></textarea>
				</div>
				<input type="hidden" name="edit-subject-id" id="edit-subject-id">
				<input type="hidden" name="edit-token" id="edit-token" value="<?php echo $token; ?>">
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
			//USING THE NICEDITOR A WYSIWYG 
	new nicEditor({iconsPath : '../images/nicEditorIcons.gif',
		buttonList : ['save','bold','italic','underline','left','center','right','ol','ul','indent','outdent','upload','link','unlink','forecolor']
	}).panelInstance('create-subject-text');
	
	//USING THE NICEDITOR A WYSIWYG 
	new nicEditor({iconsPath : '../images/nicEditorIcons.gif',
		buttonList : ['save','bold','italic','underline','left','center','right','ol','ul','indent','outdent','upload','link','unlink','forecolor']
	}).panelInstance('edit-subject-text');


			//ANIMATIONS TO SHOW AND HIDE THE CREATE NEW SUBJECT DIVS
			$("#BIGBOSS").hide();
			$("#create-subject-div").hide();
				//showing the sign up div to create a new account
				$("#create-new-subject").click(function(){
				 	showCreateNewSubjectDiv();
				});

				//Hiding the create new subject div when you click escape
				$(document).keyup(function(e){
					if(e.keyCode==27){
						hideCreateNewSubjectDiv();
						hideEditSubjectDiv();
					}
				});
				$(".cancel-create-subject").click(function(){
					hideCreateNewSubjectDiv();
				});

				//script to create the new subject to the actual room
					$(".create-subject-success").hide();
					$(".create-subject-loading").hide();
					$("#error-create-subject").hide();
					$("#create-subject-button").click(function(){
						$(".create-subject-loading").show();
						//GETTING THE ATTRIBUTES THAT WILL ALLOW US TO INSERT NEW USBJECT ON THIS ROOM
						var Room ='<?php echo $_GET["room"] ?>';
						var User ='<?php echo $_SESSION["UID"] ?>';
						var Title = $("#create-subject-name").val();
						var Text = $("#create-subject-text-div").find(".nicEdit-main").html();
						 $("#create-subject-name").val("");
						 $("#create-subject-text-div").find(".nicEdit-main").html("");
						if ($.trim(Title)!="" && $.trim(Text)!="") {
							$.ajax({
								type:"POST",
								url:"../api/api.php",
								data:{Room:Room,User:User,Title:Title,Text:Text,action:"addSubject",token:'<?php echo $token ?>'},
								success:function(data){
									$(".create-subject-loading").hide();
									$(".create-subject-success").fadeIn(100);
									$(".create-subject-success").fadeOut(2000,function(){
										hideCreateNewSubjectDiv();
									});
								}
							});
						}else{
							$(".create-subject-loading").hide();
							$("#error-create-subject").fadeIn(100,function(){
								$("#error-create-subject").fadeOut(3000);
							});
						}


					});

			});
	
		//function that will show create new subject div
		function showCreateNewSubjectDiv(){
			$("#BIGBOSS").fadeIn(200,function(){
				$("#create-subject-div").slideDown(400);
			});
		}

		//function that will hide the create new subject
		function hideCreateNewSubjectDiv(){				
			$("#BIGBOSS").slideUp(300,function(){
				$("#create-subject-div").hide();
			});
		}

			//Start Following a room when you start following a room you will get some updates and notifications regarding this Room
				function FollowRoom(button,Room){
					var User = '<?php echo $_SESSION["UID"] ?>';
					$.ajax({
						type:"POST",
						url:"../api/api.php",
						data:{User:User,Room:Room,action:"addFollower"},
						success:function(data){
							$(button).attr({"onclick":"unFollowRoom(this,"+Room+");"});
							$(button).html("unFollow");
							var fNumber = parseInt($("#Followers-number").html());
							$("#Followers-number").html(fNumber+1);
						}
					});
				}

				//Stop followin a room
				function unFollowRoom(button,Room){
					var User = '<?php echo $_SESSION["UID"] ?>';
					$.ajax({
						type:"POST",
						url:"../api/api.php",
						data:{User:User,Room:Room,action:"deleteFollower"},
						success:function(data){
							$(button).attr({"onclick":"FollowRoom(this,"+Room+");"});
							$(button).html("Follow");
							var fNumber = parseInt($("#Followers-number").html());
							$("#Followers-number").html(fNumber-1);
						}
					});
				}



				//Loading more subjects
				//We will sent an ajax request ro the server so we will upload and print more subjects that are under this actual room
				//it loads more subjects depending on the actual filter that you will find on the top right on the page
				$(".loadin-more").hide();
				var first=0;
				var filter = "date";
				$("#load-more-subjects").click(function(){
					first+=9;
					var second = 9;
					var Room = '<?php echo $_GET["room"] ?>';
					$(".loadin-more").show();
					$.ajax({
						type:"POST",
						url:"../api/api.php",
						data:{Room:Room,first:first,second:second,filter:filter,action:"SubjectListByRoom"},
						success:function(data){
							if ($.trim(data)!="empty") {
								$("#theListOsSubjects").append(data);
							}else
								$(".see-more-wide-div").hide();

							$(".loadin-more").hide();

						}
					});
				});

				//Filter Subjects by date -- like -- views -- closed or pending
				$(".loadin-filter").hide();
				$("#Filter-Subjects").change(function(){
					$(".loadin-filter").show();
					filter = $( "#Filter-Subjects option:selected").attr("value");
					first = 0;
					var second = 9;
					var Room = '<?php echo $_GET["room"] ?>';
					$.ajax({
						type:"POST",
						url:"../api/api.php",
						data:{Room:Room,first:first,second:second,filter:filter,action:"SubjectListByRoom"},
						success:function(data){
							if ($.trim(data)!="empty") {
								$("#theListOsSubjects").html(data);
							}

							$(".loadin-filter").hide();

						}
					});
				});




				


	</script>

</body>
</html>