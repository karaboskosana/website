<?php
include '../includes/sessionGuest.php';
//this page is where a guest user will be taken to view the website things
//as guest user he can only view things no actions will be done by him he needs to be a memebre so he can create comment and like....
?>

<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../style/sharedStyle.css" />
	<link rel="stylesheet" type="text/css" href="../style/indexStyle.css" />
	<link rel="stylesheet" type="text/css" href="../style/style.css" />	
	<link rel="icon" type="image/png" href="../images/forumLogo.png" />	
	<link rel="favicon" type="image/png" href="../images/forumLogo.png" />	
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

	<div id="container" style="margin-top:100px;">
		<div id="left-container">
			<div class="left-container-section-title">
				Last categories
			</div>
			<div class="last-categories-list">
				<?php
					//display the last 6 categories
					$category = new Category();
					$Categories_list = $category->CategoryList(0,6);//Getting the Categories list
					if ($Categories_list!="empty") {	
						$Categories_list = json_decode($Categories_list);
						foreach ($Categories_list as $Cat) {//using a foreach loop to display the list of the last 6 categories
							?>
							<a href="rooms-list-guest.php?category=<?php echo $Cat->CATID; ?>" class="category-div">
								<div class="category-top" style="background-image:url(../avatars/category/<?php echo $Cat->Image; ?>)"></div>
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
				
				<div class="see-more-wide-div">
					<a href="categories-list-guest.php" class="see-more-home">See all &#10145;</a>	
				</div>
			</div>
			<!--Small List of top rooms-->
			<div class="left-container-section-title">
				Top Rooms
			</div>
			<div class="last-rooms-list"><!--the last six rooms-->
				<?php
					//display the last 6 rooms
					$room = new Room();
					$Rooms_list = $room->RoomList(0,6);//Getting the Rooms list
					if ($Rooms_list!="empty") {									
					$Rooms_list = json_decode($Rooms_list);
					foreach ($Rooms_list as $Ro) {//using a foreach loop to display the list of the last 6 rooms
						?>
						<a href="subjects-guest.php?room=<?php echo $Ro->ID; ?>" class="room-div">
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
				


				
				
				<div class="see-more-wide-div">
					<a href="rooms-guest.php" class="see-more-home">See all &#10145;</a>	
				</div>				
				

			</div>	

		</div>

		<div id="side_bar_right">
			<?php include '../includes/right_side_bar_guest.php'; ?>
		</div>
	</div>

</body>
</html>