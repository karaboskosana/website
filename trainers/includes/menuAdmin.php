	<!--This is the meu that will be displayed on the Admin space-->
	<header class="admin-header ">
		<div class="inside-admin-header">
			<div class="admin-logo">
				<span id="site-name">Digititan Forum - </span>
				<span id="site-name-admin">Admin</span>
			</div>
			<div class="admin-user-right">
				<div class="admin-info-top">
					<span class="admin-name"><span>Welcome</span> <?php echo $_SESSION["aFullName"]; ?></span>
					<span class="admin-as"><span>logged as</span> <?php echo ucfirst($_SESSION["aRole"]); ?></span>
					<a href="logout.php" class="small-top-bar-icones" id="log-out-user" title="Log out"></a>
				</div>
				<div class="admin-avatar">
					<img src="../avatars/admin/<?php echo $_SESSION["aAvatar"]; ?>" />
				</div>
			</div>
			<div class="admin-menu menu-gradient">
				<a href="../admin/panel.php" class="admin-menu-item">
					<div class="admin-menu-icon" style="background-position:-7px -1px;"></div>
					<span class="admin-menu-item-name">Home</span>
				</a>
				<a href="rooms-admin.php" class="admin-menu-item">
					<div class="admin-menu-icon" style="background-position:-185px 1px;"></div>
					<span class="admin-menu-item-name">Rooms</span>
				</a>			
				<a href="categories-list-admin.php" class="admin-menu-item">
					<div class="admin-menu-icon" style="background-position:-221px 0px;"></div>
					<span class="admin-menu-item-name">Categories</span>
				</a>
				<a href="reported-subject.php" class="admin-menu-item">
					<div class="admin-menu-icon" style="background-position:-115px 0px;"></div>
					<span class="admin-menu-item-name">Reports</span>
				</a>
				<?php if ($_SESSION["aRole"]=="master") { ?>
					<a href="admins.php" class="admin-menu-item">
						<div class="admin-menu-icon" style="background-position:-115px 0px;"></div>
						<span class="admin-menu-item-name">Admins</span>
					</a>
					<a href="users.php" class="admin-menu-item">
						<div class="admin-menu-icon" style="background-position:-115px 0px;"></div>
						<span class="admin-menu-item-name">Users</span>
					</a>
				<?php } ?>
				
				<a id="admin-profile-update-link" class="admin-menu-item">
					<div class="admin-menu-icon" style="background-position:-151px 0px;"></div>
					<span class="admin-menu-item-name">Settings</span>
				</a>
			</div>
		</div>	
	</header>

	<div id="BIGBOSSADMIN">	
		<!--UPDATE ADMIN PROFILE-->
		<div id="admin-profile-div" >
			<div class="admin-profile-header">
				Update admin profile
			</div>
			<div class="admin-profile-devider">
				-------------------------------------------------------------------- <span>$$$</span> ---------------------------------------------------------------------
			</div>
			<div class="admin-profile-avatar-change">
				<div id="admin-profile_image_change">
					<div class="small-top-bar-icones"></div>
					<span>Update picture</span>
				</div>
				<img src="../avatars/admin/<?php echo $_SESSION['aAvatar']; ?>">
				<div class="loading" id="admin-change-avatar-loading"></div>
			</div>
			<div class="admin-profile-inputs">
					<input type="text" id="admin-profile-name" value="<?php echo $_SESSION['aFullName']; ?>">
					<input type="text" id="admin-profile-email" value="<?php echo $_SESSION['aEmail']; ?>">	
					<input type="password"  id="admin-profile-password-old"  placeholder="Old password">
					<input type="password"  id="admin-profile-password-new"  placeholder="New password">
					<input type="hidden" value="<?php echo $token; ?>" name="token">
					<span class="red-error" id="no-match">Bad Old Password</span>
					<div class="admin-profile-button" id="admin-profile-button">
						UPDATE
						<div class="admin-profile-success"></div>
						<div class="admin-profile-loading loading"></div>
					</div>
			</div>
			<div class="admin-profile-footer">
				P.S : Feel free to update your profile.</a><br/>
				<div class="cancel-admin-profile"  title="hide"></div>
			</div>			
		</div>	
	
	</div>	

<script type="text/javascript" src="../script/ajaxupload.js"></script>
<script type="text/javascript">
	//SCRIPT THAT WILL UPDATE THE ADMIN PROFILE INFO
	//An admin can update his full name email password or Avatar
	$("#admin-change-avatar-loading").hide();
	ChangeAdminProfileAvatar();
	function ChangeAdminProfileAvatar(){
		var btnUpload=$("#admin-profile_image_change");
			new AjaxUpload(btnUpload, {
				action: '../api/api.php',
				name: 'avatarAdmin',
				onSubmit: function(file, ext){
				$("#admin-change-avatar-loading").show();
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
						return false;
						}
				},
				onComplete: function(file, response){	
					if(response=="success"){
						$("#admin-change-avatar-loading").hide();
						
					} 													
				}
				});
	}

	$("#no-match").hide();

	$(".admin-profile-success").hide();		
	$(".admin-profile-loading").hide();	

	//script to update an Admin profile info
	$("#admin-profile-button").click(function(){
		var aFullName = $("#admin-profile-name").val();
		var aEmail = $("#admin-profile-email").val();
		var aPasswordNew = $("#admin-profile-password-new").val();
		var aPasswordOld = $("#admin-profile-password-old").val();

		if ($.trim(aFullName)!="" && $.trim(aEmail)!="") {
			$(".admin-profile-loading").hide();	
			$.ajax({
				type:"POST",
				url:"../api/api.php",
				data:{aFullName:aFullName,aEmail:aEmail,aPasswordNew:aPasswordNew,aPasswordOld:aPasswordOld,actionAdmin:"updateAdminInfo"},
				success:function(data){
					if ($.trim(data)=="nomatch") {
						$("#no-match").show(function(){
							$("#no-match").fadeOut(3000);	
						});	
					}else{
						$(".admin-profile-success").fadeIn(100,function(){
							$(".admin-profile-success").fadeOut(2000,function(){
								hideAdminProfileUpdate();
							});	
						});
					}	
					
					$(".admin-profile-loading").hide();	
				}
			});
		}
	});

	//ANIMATIONS TO SHOW AND HIDE THE CREATE NEW SUBJECT DIVS
			$("#BIGBOSSADMIN").hide();
			$("#admin-profile-div").hide();
				//showing the sign up div to create a new account
				$("#admin-profile-update-link").click(function(){		
				 	showAdminProfileUpdate();
				});

				//Hiding the create new subject div when you click escape
				$(document).keyup(function(e){
					if(e.keyCode==27){
						hideAdminProfileUpdate();
					}
				});
				$(".cancel-admin-profile").click(function(){
					hideAdminProfileUpdate();
				});


				//function that will show create new subject div
				function showAdminProfileUpdate(){
					$("#BIGBOSSADMIN").fadeIn(200,function(){
						$("#admin-profile-div").slideDown(400);
					});
				}

				//function that will hide the create new subject
				function hideAdminProfileUpdate(){				
					$("#BIGBOSSADMIN").slideUp(300,function(){
						$("#admin-profile-div").hide();
					});
				}


				//Script to Load the website info
				setTimeout(function(){
					$.ajax({
						type:"POST",
						url:"../api/api.php",
						data:{actionAdmin:"webSiteInfo"},
						success:function(data){
							data = JSON.parse(data);
							$("#UsersNumber").html(data["Users"]);							
							$("#ConnectedUsersNumber").html(data["ConnectedUsers"]);
							$("#CategoriesNumber").html(data["Categories"]);
							$("#RoomsNumber").html(data["Rooms"]);
							$("#SubjectsNumber").html(data["Subjects"]);
							$("#CommentsNumber").html(data["Comments"]);

						}
					});
				},2000);
</script>