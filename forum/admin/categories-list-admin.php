<?php
include '../includes/sessionAdmin.php';
//this page will give a list of the last categories
//The Admin will be able to delete some categories or add new ones
?>
<!DOCTYPE html>
<html>
<head>
	<title>Categories list admin</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../style/sharedStyle.css" />
	<link rel="stylesheet" type="text/css" href="../style/style.css" />
	<link rel="stylesheet" type="text/css" href="../style/styleAdmin.css" />
	<link rel="icon" type="image/png" href="../images/forumLogo.png" />	
	<script type="text/javascript" src="../script/jquery.js"></script>
	<script type="text/javascript" src="../script/jquery-ui.js"></script>

	<script type="text/javascript" src="../script/main.js"></script>

	<script type="text/javascript">
		$(function() {
			var first = 0;	
			var second = 9;
			$(".loadin-more").hide();
			$("#load-more-categories").click(function(){
				$(".loadin-more").show();
				first+=9;
				$.ajax({
					type:"POST",
					url:"../api/api.php",					
					data:{first:first,second:second,action:"CategoryList"},
					success:function(data){
						if($.trim(data)!="empty"){
							data = JSON.parse(data);
							data.forEach(function(cat){
								//DISPLAYING THE LOADED CATEGORY ON THE PAGE
								var categoryDisplay = '<a href="rooms-list-admin.php?category='+cat["CATID"]+'" id="category-div-'+cat["CATID"]+'" class="category-div"><div class="category-top" style="background-image:url(../avatars/category/'+cat["Image"]+')"><div class="category-home-action"><div class="RedGradient"  onclick="deleteCategory(event,'+cat["CATID"]+'>,\'<?php echo $token; ?>\');">Delete</div></div></div><div class="category-bottom"><span class="category-name">'+cat["Name"]+'</span><div class="category-room-information"><div class="small-top-bar-icones category-room-icon"></div><span class="category-room-number">'+cat["Rooms"]+' Room</span></div></div></a>';
								$("#theList").append(categoryDisplay);			
							});
						}else if(data=="empty")
							$(".see-more-wide-div").fadeOut(100);
						
						$(".loadin-more").hide();
					}		
				});
				
			});

		//ANIMATIONS TO SHOW AND HIDE THE CREATE NEW CATEGORY DIV
			$("#BIGBOSS").hide();
			$("#create-category-div").hide();
				//showing the sign up div to create a new account
				$("#create-new-category").click(function(){
				 	showCreateNewCategoryDiv();
				});

				//Hiding the create new subject div when you click escape
				$(document).keyup(function(e){
					if(e.keyCode==27)
						hideCreateNewCategoryDiv();
				});
				$(".cancel-create-category").click(function(){
					hideCreateNewCategoryDiv();
				});


		});

	
		//function that will show create new category div
		function showCreateNewCategoryDiv(){
			$("#BIGBOSS").fadeIn(200,function(){
				$("#create-category-div").slideDown(400);
			});
		}

		//function that will hide the create new category
		function hideCreateNewCategoryDiv(){				
			$("#BIGBOSS").slideUp(300,function(){
				$("#create-category-div").hide();
			});
		}

	</script>

</head>
<body>
	<?php
		//importing the menu
		include '../includes/menuAdmin.php';
	?>
	<!--#######################################################################################################-->
	<!--#######################################################################################################-->
	<div id="container"  style=" margin-top:200px;">
		<div id="left-container">	
			<div class="last-categories-list" >
				<div class="left-container-section-title">
					Last categories
				</div>
			<div id="theList">
				<?php
					//display the last 6 categories
					$category = new Category();
					$Categories_list = $category->CategoryList(0,9);//Getting the Categories list
					if ($Categories_list!="empty") {
						$Categories_list = json_decode($Categories_list);
						foreach ($Categories_list as $Cat) {//using a foreach loop to display the list of the last 9 categories
							?>
							<a href="rooms-list-admin.php?category=<?php echo $Cat->CATID; ?>" id="category-div-<?php echo $Cat->CATID; ?>" class="category-div">
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
					<a class="see-more-other-pages" id="load-more-categories">
						Load more <span>&#65516;</span>
						<div class="loading loadin-more"></div>
					</a>	
				</div>
			</div>		
		</div>
		
		<div id="side_bar_right_admin">
			<div id="create-new-category" class="create-new">Create new category</div>
			<?php include("../includes/right_side_bar_admin.php"); ?>	
		</div>
	</div>
	
	<div id="BIGBOSS">	
		<!--CREATE A NEW CATEGORY BY AN ADMIN-->
		<div id="create-category-div" >
			<div class="create-category-header">
				Create new category
			</div>
			<div class="create-category-devider">
				------------------------------------- <span>$$$</span> -------------------------------------
			</div>
			<div class="create-category-inputs">
				<form action="../api/api.php" method="post" enctype="multipart/form-data" class="create-category-inputs">
					<input type="text" name="Name" id="create-category-title"  placeholder="Name" required>
					<input type="file" name="avatarCategory" id="create-category-avatar"  placeholder="Category avatar" required>	
					<input type="hidden" name="action" id="create-category-action" value="addCategory" required>
					<input type="hidden" name="token" value="<?php echo $token; ?>" required>
					<input type="submit" class="create-category-button" id="create-category-button" value="CREATE" />
				</form>			
			</div>
			<div class="create-category-footer">
				P.S : Please only non-existing categories.</a><br/>
				<div class="cancel-create-category"  title="hide"></div>
			</div>			
		</div>	
	
	</div>

</body>
</html>