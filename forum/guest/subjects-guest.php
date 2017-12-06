<?php
include '../includes/sessionGuest.php';
//this page will give us a list of subject that are under a specific room
//this is for the a guest user he cant do anything just viewing the results
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
	<link rel="stylesheet" type="text/css" href="../style/indexStyle.css" />
	<link rel="stylesheet" type="text/css" href="../style/style.css" />	
	<link rel="icon" type="image/png" href="../images/forumLogo.png" />	
	<script type="text/javascript" src="../script/jquery.js"></script>
	<script type="text/javascript" src="../script/jquery-ui.js"></script>
	<script type="text/javascript" src="../script/main.js"></script>
</head>
<body>
	<?php
		//importing the menu
		include '../includes/menuGuest.php';
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
			<div id="room-some-followers">
				<?php
					//dsiplayin a smal list of users that follows this room
					$followers = new Followers();
					$followers->setRoom($_GET["room"]);
					$Followers_List = $followers->FollowersList(0,7);
					if ($Followers_List!="empty") {
						$Followers_List = json_decode($Followers_List);
						foreach ($Followers_List as $follower) {
							?>
								<img src="../avatars/perso/<?php echo $follower->Avatar; ?>" title="<?php echo $follower->FullName ?>">
							<?php
						}
					}
				?>
						
			</div>

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
		</div>
	</div>



	<div id="container">
		<div id="left-container">
			<div id="subjects-list">
				<div id="theListOsSubjects">
					<?php
						$subject = new Subject();
						$subject->setRoom($_GET["room"]);
						if($subject->SubjectsByRoom(0,9,'date')!="empty"){
							$Subjects_List = $subject->SubjectsByRoom(0,9,'date');
							//this function will print us the subjects 
							PrintSubjectGuest($Subjects_List);
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
			<?php include '../includes/right_side_bar_guest.php'; ?>
		</div>
	</div>


	<script type="text/javascript">

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
						data:{Room:Room,first:first,second:second,filter:filter,action:"SubjectListByRoomGuest"},
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
						data:{Room:Room,first:first,second:second,filter:filter,action:"SubjectListByRoomGuest"},
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