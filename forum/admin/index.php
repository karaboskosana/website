<!--
this is the index page here an Admin will find a form so he can logs in
-->
<!DOCTYPE html>
<html>
<head>
	<title>Admin Panel</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../style/sharedStyle.css" />
	<link rel="stylesheet" type="text/css" href="../style/styleAdmin.css" />
	<link rel="icon" type="image/png" href="../images/forumLogo.png" />	
	<link rel="favicon" type="image/png" href="../images/forumLogo.png" />	
	<script type="text/javascript" src="../script/jquery.js"></script>
	<script type="text/javascript" src="../script/jquery-ui.js"></script>
	<script type="text/javascript" src="../script/main.js"></script>
	<script type="text/javascript">
		$(function() {
			//Script that will connect the admin to the Admin panel
			//Once the Admin is logged he can manage all the users subjects Rooms and Categories
			$(".admin-login-success").hide();
			$(".admin-login-loading").hide(); 
			$("#admin-error-login").hide();
			$("#admin-fields-login").hide();
			$("#admin-login-button").click(function() {
				LoginAdmin();
			});
			$("#admin-password-login").keyup(function(e){
				if (e.keyCode==13) {
					LoginAdmin();
				};
			});
		});

		//function to log the Admin in
		function LoginAdmin(){
			var admin_email = $("#admin-email-login").val();
				var admin_password = $("#admin-password-login").val();
				if($.trim(admin_email)!="" && $.trim(admin_password)!=""){
					$(".admin-login-loading").show();
					$.ajax({
						type:"POST",
						url:"../api/api.php",
						data:{aEmail:admin_email,aPassword:admin_password,action:"adminLogin"},
						success:function(data){
							if ($.trim(data)=="good") {//if the given login and password are good the admin will be taken to the panel page so he can manage the website
								$(".admin-login-loading").hide();
								$(".admin-login-success").fadeIn(500,function(){
										window.location.href ="panel.php"
								});
							}else{
								$("#admin-error-login").show(function(){
										$("#admin-error-login").fadeOut(2500);
								});
								$(".admin-login-loading").hide(); 
							}
						}
					});
				}else{
					$("#admin-fields-login").fadeIn(400,function(){
						$("#admin-fields-login").fadeOut(3000);
					});
				}
		}
	</script>

</head>
<body style="background: #59a9e6;">
<!--Admin login  Form-->
<div id="admin-login-div" class="user-action">
	<div class="user-action-header">
		Admin Panel
	</div>
	<div class="user-action-devider">
		------------------------------------- <span>$$$</span> -------------------------------------
	</div>
	<div class="admin-user-inputs">	
		<input type="email" name="admin-email-login" id="admin-email-login"  placeholder="Email">
		<input type="password" name="admin-password-login" id="admin-password-login" placeholder="Password">
		<span class="red-error" id="admin-error-login">Error Login</span>
		<span class="red-error" id="admin-fields-login">Please fill in all the fields</span>
		<div class="user-action-button" id="admin-login-button">
			LOG IN
			<div class="admin-login-success"></div>
			<div class="admin-login-loading loading"></div>
		</div>
	</div>
	<div class="user-action-footer">
		This is only for admin.<br/>If you are not admin <a href="../">go back</a>
	</div>			
</div>	

</body>
</html>