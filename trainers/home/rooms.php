<?php
/*
This page will show a list of the last rooms 
*/
include '../includes/session.php';
	$category = new Category();
	$room = new Room();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Rooms list</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../style/sharedStyle.css" />
	<link rel="stylesheet" type="text/css" href="../style/style.css" />
	<link rel="icon" type="image/png" href="../images/forumLogo.png" />	
	<script type="text/javascript" src="../script/jquery.js"></script>
	<script type="text/javascript" src="../script/jquery-ui.js"></script>
	<script type="text/javascript" src="../script/main.js"></script>
	<script type="text/javascript">
		$(function() {
			//Loading more random rooms 			
			var first = 0;	
			var second = 9;
			$(".loadin-more").hide();
			$("#load-more-rooms").click(function(){
				$(".loadin-more").show();
				first+=9;
				$.ajax({
					type:"POST",
					url:"../api/api.php",					
					data:{first:first,second:second,action:"roomList"},
					success:function(data){
						if($.trim(data)!="empty"){
							data = JSON.parse(data);
							data.forEach(function(ro){
								//DISPLAYING THE LOADED ROOMS BY THE ACTUAL CATEGORY ON THE PAGE
								var roomDisplay = '<a href="subjects.php?room='+ro["ID"]+'" class="room-div"><div class="room-top" style="background-image:url(../avatars/room/'+ro["Image"]+')"><div class="room-category-name">'+ro["Name"]+'</div></div><div class="room-bottom">	<span class="room-name">'+ro["Title"]+'</span><div class="room-information">	<div class="room-indormation-insider-row"><div class="small-top-bar-icones room-subjects-icon"></div><span class="category-room-number">'+ro["Subjects"]+' Subject</span></div><div class="room-indormation-insider-row"><div class="small-top-bar-icones room-followers-icon"></div><span class="category-room-number">'+ro["Followers"]+' Follower</span></div></div></div></a>';
								$("#theList").append(roomDisplay);			
							});
						}else if(data=="empty")
							$(".see-more-wide-div").fadeOut(100);
						
						$(".loadin-more").hide();
					}		
				});
				
			});


		});


	</script>

</head>
<body>
	<?php
		//importing the menu
		include '../includes/menu.php';
	?>
	<!--#######################################################################################################-->
	<!--#######################################################################################################-->
	<div id="subject-room-top-div">
		<div id="subject-room-div-insider">	
			<div id="room-name-display" style="margin-top:30px;">Rooms list</div>	
			<div id="profile-show-what">			
			</div>
		</div>
		
	</div>


	<div id="container">
		<div id="left-container">	
			<div class="last-categories-list" style="margin-top:40px;">
				<div id="theList">
					<?php
						//display the last 9 rooms			
						$Rooms_list = $room->RoomList(0,9);//Getting the Rooms list
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
				<div class="see-more-wide-div">
					<a class="see-more-other-pages" id="load-more-rooms">
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

</body>
</html>