<?php
include '../includes/sessionAdmin.php';
//this is the page where an Admin will be taken when he logs in 
//the Admin will find a list of catgories that he can manage
//he will alson fing a list of Requested rooms so he can accept to add them or delete them
?>

<!DOCTYPE html>
<html>
<head>
	<title>Admin panel</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../style/sharedStyle.css" />
	<link rel="stylesheet" type="text/css" href="../style/style.css" />
	<link rel="stylesheet" type="text/css" href="../style/styleAdmin.css" />
	<link rel="icon" type="image/png" href="../images/forumLogo.png" />	
	<link rel="favicon" type="image/png" href="../images/forumLogo.png" />	
	<script type="text/javascript" src="../script/jquery.js"></script>
	<script type="text/javascript" src="../script/jquery-ui.js"></script>
	<script type="text/javascript" src="../script/main.js"></script>
</head>
<body>
	<?php
		//importing the menu
		include '../includes/menuAdmin.php';
	?>
	<!--#######################################################################################################-->

	<div id="container" style=" margin-top:200px;">
		<div id="left-container">						
			<div class="left-container-section-title">
				Last categories
			</div>
			<div class="last-categories-list">
				<div id="TheAdminListCategory">
					<?php
						//display the last 6 categories
						$category = new Category();
						$Categories_list = $category->CategoryList(0,6);//Getting the Categories list
						if ($Categories_list!="empty") {	
							$Categories_list = json_decode($Categories_list);
							foreach ($Categories_list as $Cat) {//using a foreach loop to display the list of the last 6 categories
								?>
								<a href="rooms-list-admin.php?category=<?php echo $Cat->CATID; ?>" class="category-div" id="category-div-<?php echo $Cat->CATID; ?>">
									<div class="category-top" style="background-image:url(../avatars/category/<?php echo $Cat->Image; ?>)">
									<div class="category-home-action">
											<div class="RedGradient"  onclick="deleteCategory(event,<?php echo $Cat->CATID; ?>,'<?php echo $token; ?>');">Delete</div>
									</div>
									</div>
									<div class="category-bottom">
										<span class="category-name"><?php echo $Cat->Name; ?></span>
										<div class="category-room-information">
											<div class="small-top-bar-icones category-room-icon"></div>
											<span class="category-room-number"><?php echo $Cat->Rooms; ?> Room</span>
										</div>
									</div>
								</a>
								<?php
							}
						}	
					?>
				</div>
				<div class="see-more-wide-div">
					<a href="categories-list-admin.php" class="see-more-home">See more &#10145;</a>	
				</div>
			</div>
		<!--Small List of top rooms-->
			<div class="left-container-section-title">
				Pending Rooms
			</div>
			<div class="last-rooms-list"><!--the last six pending rooms-->
				<div id="TheAdminListPendingRooms">
					<?php
						//display the last 6 pending rooms					
						$Rooms_list = $admin->PendingRoomList(0,6);//Getting the Pending Rooms list
						if ($Rooms_list!="empty") {									
						$Rooms_list = json_decode($Rooms_list);
						foreach ($Rooms_list as $Ro) {//using a foreach loop to display the list of the last 6 pending rooms
							?>
							<div class="room-div" id="room-div-<?php echo $Ro->ID; ?>">
								<div class="room-top" style="background-image:url(../avatars/room/<?php echo $Ro->Image; ?>)">
									<div class="room-category-name"><?php echo $Ro->Name; ?></div>
									<div class="room-pending-action">
										<div class="GreenGradient" onclick="acceptRoom(event,<?php echo $Ro->ID; ?>,<?php echo $Ro->Category; ?>,'<?php echo $token; ?>');">Accept</div>
										<div class="RedGradient"  onclick="deleteRoom(event,<?php echo $Ro->ID; ?>,'<?php echo $token; ?>');">Delete</div>
									</div>
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
							</div>
							<?php
							}
						}else{
					?>		
					<span id="No-Result" style="margin-left:5px;"><strong>No pending rooms for the moment.</strong></span>
					<?php } ?>		
				</div>	
				<?php if ($Rooms_list!="empty") {	 ?>			
				<div class="see-more-wide-div" id="pending-wide-div">
					<a class="see-more-other-pages" id="load-more-rooms-admin">
						Load more <span>&#65516;</span>
						<div class="loading loadin-more"></div>
					</a>	
				</div>						
				<?php } ?>
			</div>	

		</div>

		<div id="side_bar_right_admin">
			<?php include("../includes/right_side_bar_admin.php"); ?>	
		</div>	
	</div>

	<script type="text/javascript">
			//LOADING MORE PENDIG ROOMS
			var first = 0;	
			var second = 6;
			$(".loadin-more").hide();
			$("#load-more-rooms-admin").click(function(){
				$(".loadin-more").show();
				first+=6;
				$.ajax({
					type:"POST",
					url:"../api/api.php",					
					data:{first:first,second:second,action:"roomListPending"},
					success:function(data){
						if($.trim(data)!="empty"){
							data = JSON.parse(data);
							data.forEach(function(ro){
								//DISPLAYING THE LOADED ROOMS BY THE ACTUAL CATEGORY ON THE PAGE
								var roomDisplay = '<a href="subjects.php?room='+ro["ID"]+'"  id="room-div-'+ro["ID"]+'" class="room-div"><div class="room-top" style="background-image:url(../avatars/room/'+ro["Image"]+')"><div class="room-category-name">'+ro["Name"]+'</div><div class="room-pending-action"><div class="GreenGradient"  onclick="acceptRoom(event,'+ro["ID"]+','+ro["Category"]+',\'<?php echo $token; ?>\');">Accept</div><div class="RedGradient"  onclick="deleteRoom(event,'+ro["ID"]+',\'<?php echo $token; ?>\');">Delete</div></div></div><div class="room-bottom">	<span class="room-name">'+ro["Title"]+'</span><div class="room-information">	<div class="room-indormation-insider-row"><div class="small-top-bar-icones room-subjects-icon"></div><span class="category-room-number">'+ro["Subjects"]+' Subject</span></div><div class="room-indormation-insider-row"><div class="small-top-bar-icones room-followers-icon"></div><span class="category-room-number">'+ro["Followers"]+' Follower</span></div></div></div></a>';
								$("#TheAdminListPendingRooms").append(roomDisplay);			
							});
						}else if(data=="empty")
							$("#pending-wide-div").fadeOut(100);
						
						$(".loadin-more").hide();
					}		
				});
				
			});
	</script>

</body>
</html>