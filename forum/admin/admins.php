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
				Admins List
			</div>
			<div class="last-admins-list">
				<div id="TheAdminList">
					<?php
						$admin = new Admin();
						//Getting the list of admins
						$Admin_list = $admin->AdminsList(0,6);
						if ($Admin_list!="empty") {
							//Print Admin By Admin
							PrintAdmin($Admin_list,$token);
						}else{ ?>
							<span id="No-Result"><strong>No admins for the moment you are the only one!</strong></span>
						<?php } ?>
				</div>

				<div class="see-more-wide-div" id="pending-wide-div">
					<a class="see-more-other-pages" id="load-more-admin">
						Load more <span>&#65516;</span>
						<div class="loading loadin-more"></div>
					</a>	
				</div>
			</div>

		</div>
		
		<div id="side_bar_right_admin">
			<div id="create-new-admin" class="create-new">Add new admin</div>
			<?php include("../includes/right_side_bar_admin.php"); ?>	
		</div>	
	</div>



	
	<div id="BIGBOSS">	
		<!--CREATE NEW ADMIN-->		
		<div id="create-admin-div" >
			<div class="create-admin-header">
				Create new Admin
			</div>
			<div class="create-admin-devider">
				------------------------------------- <span>$$$</span> -------------------------------------
			</div>
			<div class="create-admin-inputs">
				<input type="text" name="aFullName" id="aFullName"  placeholder="Full Name" required>
				<input type="email" name="aEmail" id="aEmail"  placeholder="Email" required>	
				<input type="text" name="aPssword" id="aPssword" placeholder="Password" required>
				<select name="aRole" id="aRole" >
					<option for="" value="">Choose Admin Role ...</option>
					<option for="normal" value="normal">Normal</option>
					<option for="master" value="master">Master</option>
				</select>
				<span class="red-error" id="error-create-admin">All fields required </span>	
				<div class="create-admin-button" id="create-admin-button">
					CREATE
					<div class="create-admin-success"></div>
					<div class="create-admin-loading loading"></div>
				</div>				
			</div>
			<div class="create-admin-footer">
				P.S : Be sure to add trustful admins!</a><br/>
				<div class="cancel-create-admin"  title="hide"></div>
			</div>			
		</div>	
	
	</div>


	<script type="text/javascript">
	$(function() {
		//ANIMATIONS TO SHOW AND HIDE THE CREATE NEW ADMIN DIV
			$("#BIGBOSS").hide();
			$("#create-admin-div").hide();
				//showing the sign up div to create a new account
				$("#create-new-admin").click(function(){
				 	showCreateNewAdminDiv();
				});

				//Hiding the create new subject div when you click escape
				$(document).keyup(function(e){
					if(e.keyCode==27)
						hideCreateNewAdminDiv();
				});
				$(".cancel-create-admin").click(function(){
					hideCreateNewAdminDiv();
				});


		//SCRIPT TO CRATE A NEW ADMIN
		$(".create-admin-success").hide();
		$(".create-admin-loading").hide();
		$("#error-create-admin").hide();
		$("#create-admin-button").click(function(){
			$(".create-admin-loading").show();
			var aFullName = $("#aFullName").val();
			var aEmail = $("#aEmail").val();
			var aPassword = $("#aPssword").val();
			var aRole = $("#aRole option:selected").val();

			$("#aFullName").val("");
			$("#aEmail").val("");
			$("#aPssword").val("");
			if ($.trim(aFullName)!="" && $.trim(aEmail)!="" && $.trim(aPssword)!="" && $.trim(aRole)!="") {
				$.ajax({//SENDING THE AJAX REQUEST AND CHECKING FOR THE INPUTS
					type:"POST",
					url:"../api/api.php",
					data:{aFullName:aFullName,aEmail:aEmail,aPassword:aPassword,aRole:aRole,actionAdmin:"insertAdmin",token:'<?php echo $token; ?>'},
					success:function(data){
						$(".create-admin-loading").hide();
							$(".create-admin-success").fadeIn(100);
							$(".create-admin-success").fadeOut(2000,function(){
								hideCreateNewAdminDiv();
						});
					}
				});
			}else{
				$(".create-admin-loading").hide();
					$("#error-create-admin").fadeIn(100,function(){
						$("#error-create-admin").fadeOut(3000);
				});
			}
		});	

		//script to load more admins 6 by 6
		//LOADING MORE PENDIG ROOMS
			var first = 0;	
			var second = 6;
			$(".loadin-more").hide();
			$("#load-more-admin").click(function(){
				$(".loadin-more").show();
				first+=6;
				$.ajax({
					type:"POST",
					url:"../api/api.php",					
					data:{first:first,second:second,actionAdmin:"adminList"},
					success:function(data){
						if($.trim(data)!="empty"){
							$("#TheAdminList").append(data);				
						}else 
							$("#pending-wide-div").fadeOut(100);
						$(".loadin-more").hide();
					}		
				});
			});

	});

	
		//function that will show create new admin div
		function showCreateNewAdminDiv(){
			$("#BIGBOSS").fadeIn(200,function(){
				$("#create-admin-div").slideDown(400);
			});
		}

		//function that will hide the create new admin
		function hideCreateNewAdminDiv(){				
			$("#BIGBOSS").slideUp(300,function(){
				$("#create-admin-div").hide();				
			});
		}


		//function that will delete an Admin
		function deleteadmin(AdminId){
			$.ajax({
				type:"POST",
				url:"../api/api.php",
				data:{AID:AdminId,actionAdmin:"deleteAdmin"},
				success:function(data){
					$("#admin-div-"+AdminId).slideUp(500);
				}
			});		
		}


		//Chnage the admin Role
		function updateadminrole(AdminId){
			var Role = $("#adminChangeRole-"+AdminId+" option:selected").val();
			if (Role!="") {
				$.ajax({
					type:"POST",
					url:"../api/api.php",
					data:{AID:AdminId,aRole:Role,actionAdmin:"changeAdminRole",token:'<?php echo $token; ?>'},
					success:function(data){
						$("#amin-role-change-"+AdminId).html(ucFirst(Role));
					}
				});	
			}
				
		}

	</script>


</body>
</html>