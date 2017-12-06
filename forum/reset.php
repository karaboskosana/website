<!--
this is the reset page is for people who have forgotted their password
-->
<!DOCTYPE html>
<html>
<head>
	<title>Password Forgotten</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="style/style.css" />
	<link rel="stylesheet" type="text/css" href="style/sharedStyle.css" />
	<link rel="stylesheet" type="text/css" href="style/styleAdmin.css" />
	<link rel="icon" type="image/png" href="images/forumLogo.png" />	
	<link rel="favicon" type="image/png" href="images/forumLogo.png" />	
	<script type="text/javascript" src="script/jquery.js"></script>
	<script type="text/javascript" src="script/jquery-ui.js"></script>
	<script type="text/javascript">
		$(function() {
			//Script that will connect the admin to the Admin panel
			//Once the Admin is logged he can manage all the users subjects Rooms and Categories
			$("#email-reset-no-exist").hide();
			$("#email-sent").hide(); 
			$(".reset-success").hide();
			$(".reset-loading").hide(); 
			//script for asking for the password reset
			$("#ask-for-reset").click(function(){

				var email = $("#email-account-reset").val();
				if ($.trim(email)!="") {
					$(".reset-loading").fadeIn(); 
					$.ajax({
						type:"POST",
						url:"api/api.php",
						data:{Email:email,action:"askResetPassword"},
						success:function(data){			
							$(".reset-loading").fadeOut(); 
							if($.trim(data)=="emailnoexist"){
								$(".reset-loading").fadeOut();
								$("#email-reset-no-exist").fadeIn(100,function(){
									$("#email-reset-no-exist").fadeOut(3000);
								});
							}
							if($.trim(data)=="good"){								
								$(".reset-success").fadeIn();
								$("#email-sent").fadeIn(100,function(){
									$("#email-sent").fadeOut(3000);
									$(".reset-success").fadeOut(3000);
								});
							}
						}
					});
				}
			});

			$("#new-pasword-not-match").hide();
			$("#new-password-success-sent").hide(); 
			$("#wrong-key").hide();
			//Ask for generating the key
			$("#update-the-password").click(function(){
				var NewPass= $("#new-password").val();
				var ConfirmPass= $("#confirm-newpassword").val();
				var PasswordToken= $("#password-token").val();
				var UID = $("#UID").val();
				if ($.trim( NewPass) == $.trim(ConfirmPass)) {
					$.ajax({
						type:"POST",
						url:"api/api.php",
						data:{Password:NewPass,PasswordToken:PasswordToken,UID:UID,action:"updateForgottenPassword"},
						success:function(data){
							$(".reset-loading").fadeOut();
							if ($.trim(data)=="good") {								
								$(".reset-success").fadeIn();
								$("#new-password-success-sent").fadeIn(100,function(){
									$("#new-password-success-sent").fadeOut(3000);
									$(".reset-success").fadeOut(3000);
								});
							}else{
								$("#wrong-key").fadeIn(100,function(){
									$("#wrong-key").fadeOut(4000);
								});
							}
							
						}

					});
				}else{
					$("#new-pasword-not-match").fadeIn(100,function(){
						$("#email-sent").fadeOut(3000);
					});
						
				}

			});

		});

	</script>

</head>
<body style="background: #59a9e6;">
<?php if(isset($_GET["ask"])){ ?>
<!--Asking for reseting the password-->
<div id="admin-login-div" class="user-action send-email-div" style="height:230px;">
	<div class="user-action-header">
		Password Forgotten
	</div>
	<div class="user-action-devider">
		------------------------------------- <span>$$$</span> -------------------------------------
	</div>
	<div class="admin-user-inputs">	
		<input type="email"  id="email-account-reset"  placeholder="Email">	
		<span class="red-error" id="email-reset-no-exist">Email doesn't exist.</span>
		<span class="green" id="email-sent" style="color:green; margin-left:10px;"><strong>An email was sent to you inbox.</strong></span>
		<div class="user-action-button" id="ask-for-reset">
			RESET PASSWORD
			<div class="reset-success"></div>
			<div class="reset-loading loading"></div>
		</div>
	</div>
	<div class="user-action-footer">
		Please this time try not to forget your password.<br/>If remember it <a href="index.php">go back</a>
	</div>			
</div>	
<?php } ?>

<?php if(isset($_GET["UID"],$_GET["Email"],$_GET["Key"])){ 
	include 'classes/classPack.php';
	$user = new user();
	if($user->CheckUserGeneratedKey($_GET["UID"],$_GET["Email"],$_GET["Key"]) == 0){
		header("Locaction: index.php");
	}
?>
<!--Changing the password-->
<div id="admin-login-div" class="user-action send-email-div" >
	<div class="user-action-header">
		Update Password
	</div>

	<div class="admin-user-inputs">	
		<input type="password"  id="new-password"  placeholder="New password">	
		<input type="password"  id="confirm-newpassword"  placeholder="Confirm password">
		<input type="hidden" id="password-token" value="<?php echo $_GET['Key']; ?>">
		<input type="hidden" id="UID" value="<?php echo $_GET['UID']; ?>">
		<span class="red-error" id="new-pasword-not-match">The passwords doesn't match.</span>
		<span class="red-error" id="wrong-key">Something went wrong.</span>
		<span class="green" id="new-password-success-sent" style="color:green; margin-left:10px;"><strong>Password updated Successfully.</strong></span>
		<div class="user-action-button" id="update-the-password">
			SUBMIT
			<div class="reset-success"></div>
			<div class="reset-loading loading"></div>
		</div>
	</div>
	<div class="user-action-footer">
		Nothing to do here <a href="index.php">go back</a>
	</div>			
</div>
<?php } ?>

</body>
</html>