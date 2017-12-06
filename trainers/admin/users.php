<?php
include '../includes/sessionAdmin.php';
//this page will display a list of admins
//the master admins will be able to manipulate other admin==> Add new Admins change Adnis Role or Just delete Admins


//IF THE ADMIN IS NOT MASTER GO BACK TO THE PANEL DASHBOARD
if ($_SESSION["aRole"]!="master") 
		header("Location: ../admin/panel.php");
?>

<!DOCTYPE html>
<html>
<head>
	<title>Admin panel : Manage Users</title>
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
				Users Management tool 
				<div id="users-admin-filter-div">
					<select id="users-filter-select">
						<option value="all">All</option>
						<option value="enable">Enable</option>
						<option value="disable">Disable</option>
					</select>
				</div>
			</div>
			<div class="last-users-list">
				<div id="TheUsersList">
					<?php
						$admin = new Admin();
						//Getting the list of Users
						$User_list = $admin->UsersList(0,9,"all");
						if ($User_list!="empty") {
							//Print User by User
							PrintUser($User_list,$token);
						}else{ ?>
							<span id="No-Result"><strong>No users for the moment you are the only one!</strong></span>
						<?php } ?>
				</div>

				<div class="see-more-wide-div" id="pending-wide-div">
					<a class="see-more-other-pages" id="load-more-users">
						Load more <span>&#65516;</span>
						<div class="loading loadin-more"></div>
					</a>	
				</div>
			</div>

		</div>
		
		<div id="side_bar_right_admin">		
			<?php include("../includes/right_side_bar_admin.php"); ?>	
		</div>	
	</div>

	<script type="text/javascript">
	$(function() {
		//script to load more users 6 by 6
		//LOADING MORE PENDIG ROOMS
			var first = 0;	
			var second = 9;
			var filter = "all";
			$(".loadin-more").hide();
			$("#load-more-users").click(function(){
				$(".loadin-more").show();
				first+=9;
				$.ajax({
					type:"POST",
					url:"../api/api.php",					
					data:{first:first,second:second,filter:filter,actionAdmin:"usersList"},
					success:function(data){
						if($.trim(data)!="empty"){
							$("#TheUsersList").append(data);				
						}else 
							$("#pending-wide-div").fadeOut(100);
						$(".loadin-more").hide();
					}		
				});

			});

			//change the filter of shown users
			$("#users-filter-select").change(function(){
				filter = $("#users-filter-select option:selected").val();
				first=0;
				$.ajax({
					type:"POST",
					url:"../api/api.php",					
					data:{first:first,second:second,filter:filter,actionAdmin:"usersList"},
					success:function(data){		
						if($.trim(data)!="empty"){
							$("#TheUsersList").html(data);	
							$("#pending-wide-div").fadeIn(100);		
						}else 						
							$("#TheUsersList").html("<span style='color:red;'><strong>No user found</strong></span>");
					}		
				});
			});



	});

		//this function will detele a user from the database
	function deleteUser(UID,token){
		$.ajax({//Ajax Request to the server
			type:"POST",
			url:"../api/api.php",
			data:{UID:UID,actionAdmin:"deleteUser",token:token},//Data that will be sent to the server
			success:function(data){
				$("#user-div-"+UID).fadeOut(500);//Animation After Deleting A User
			}
		});
	}


	//this function will detele a user from the database
	function updateUserStatus(UID,token){
			var Status = $("#userChangeStatus-"+UID+" option:selected").val();
			if (Status!="") {//Ajax Request to the server
				$.ajax({
					type:"POST",
					url:"../api/api.php",
					data:{UID:UID,Status:Status,actionAdmin:"updateUserStatus",token:token},
					success:function(data){
						$("#user-status-change-"+UID).html('<span class="'+Status+'">'+ucFirst(Status)+'</span>');
					}
				});	
			}
				
		}	


	</script>


</body>
</html>