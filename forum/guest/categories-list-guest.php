<?php
include '../includes/sessionGuest.php';
//this page will give us the last categories on the website
//the user can load other categoris
?>
<!DOCTYPE html>
<html>
<head>
	<title>Guest : Categories list</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../style/sharedStyle.css" />
	<link rel="stylesheet" type="text/css" href="../style/indexStyle.css" />
	<link rel="stylesheet" type="text/css" href="../style/style.css" />
	<link rel="icon" type="image/png" href="../images/forumLogo.png" />	
	<script type="text/javascript" src="../script/jquery.js"></script>
	<script type="text/javascript" src="../script/jquery-ui.js"></script>
	<script type="text/javascript" src="../script/main.js"></script>
	<script type="text/javascript">
		$(function() {
			//script to load more categories
			//9 by 9 categories will be loaded
			var first = 0;	
			var second = 9;
			$(".loadin-more").hide();
			$("#load-more-categories").click(function(){
				$(".loadin-more").show();
				first+=9;
				$.ajax({
					type:"POST",
					url:"../api/api.php",					
					data:{first:first,second:second,action:"CategoryListGuest"},
					success:function(data){
						if($.trim(data)!="empty"){
							data = JSON.parse(data);
							data.forEach(function(cat){
								//DISPLAYING THE LOADED CATEGORY ON THE PAGE
								var categoryDisplay = '<a href="rooms-list-guest.php?category='+cat["CATID"]+'" class="category-div"><div class="category-top" style="background-image:url(../avatars/category/'+cat["Image"]+')"></div><div class="category-bottom"><span class="category-name">'+cat["Name"]+'</span><div class="category-room-information"><div class="small-top-bar-icones category-room-icon"></div><span class="category-room-number">'+cat["Rooms"]+' Room</span></div></div></a>';
								$("#theList").append(categoryDisplay);			
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
		include '../includes/menuGuest.php';
	?>
	<!--#######################################################################################################-->
	<!--#######################################################################################################-->
	<div id="subject-room-top-div"  style="background:none; border:none; ">
		<div id="subject-room-div-insider">	
			<div id="room-name-display" style="margin-top:30px;">Categories list</div>	
		</div>
	</div>


	<div id="container">
		<div id="left-container">	
			<div class="last-categories-list" style="margin-top:40px;">
			<div id="theList">
				<?php
					//display the last 6 categories
					$category = new Category();
					$Categories_list = $category->CategoryList(0,9);//Getting the Categories list
					$Categories_list = json_decode($Categories_list);
					foreach ($Categories_list as $Cat) {//using a foreach loop to display the list of the last 9 categories
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
				?>

			</div>	
				<div class="see-more-wide-div">
					<a class="see-more-other-pages" id="load-more-categories">
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

</body>
</html>