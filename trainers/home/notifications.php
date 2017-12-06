<?php
include '../includes/session.php';
/*
Notification is the page where a user will be able to amnipulate his notifications and see who commented his subjects or liked it
He can also delete notifications or mark them as read
*/
?>
<!DOCTYPE html>
<html>
<head>
	<title>Last Notifications</title>
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
		//function to mark all my notification as read and delete them
		$("#mark-as-read").click(function(){
			var User = '<?php echo $_SESSION["UID"] ?>';
			$.ajax({
				type:"POST",
				url:"../api/api.php",
				data:{User:User,action:"markAsRead",token:'<?php echo $token; ?>'},
				success:function(data){
					$("#theList-Notif").hide();
				}
			});
		});

		//load more notifications
		//this script will load 9 by 9 notification
		$("#more-notifications-loading-gif").hide();
		var first = 0;
		$("#load-more-notification").click(function(){
			$("#more-notifications-loading-gif").show();
			first+=9;
			var second = 9;
			$.ajax({
				type:"POST",
				url:"../api/api.php",
				data:{User:"<?php echo $_SESSION['UID']; ?>",action:"loadNotification",first:first,second:second,token:'<?php echo $token; ?>'},
				success:function(data){
					$("#theList-Notif").append(data);
					$("#more-notifications-loading-gif").hide();
					if($.trim(data)=="" || $.trim(data)==null){
						$(".see-more-wide-div").hide();
					}
				}
			});
		});

	});

	function deletenotification(e,NotifId,User){
		$.ajax({
			type:"POST",
			url:"../api/api.php",
			data:{NotifId:NotifId,User:User,action:"deleteNotification",token:'<?php echo $token; ?>'},
			success:function(data){

			}
		});
		$("#Notification-"+NotifId).fadeOut(500);	
			
	}

	</script>

</head>
<body>
	<?php
		//importing the menu
		include '../includes/menu.php';
	?>
	<!--#######################################################################################################-->
	<!--#######################################################################################################-->
	<div id="notifications-top-div">
		<div id="notifications-top-div-insider">	
			<div id="title-header" style="margin-top:30px;"> Last notifications</div>	
			<div id="notification-what">
				<div id="mark-as-read" style="float:right;">Mark as read</div>
			</div>
		</div>	
	</div>


	<div id="container">
		<div id="left-container">	
			<div class="notification-page-list" style="margin-top:40px;">
			<div id="theList-Notif">
				<?php $notification = new Notification();
					//Priting the last 9 notifications
					$notification->GeneralNotificationList($_SESSION["UID"],0,9,$token);
				 ?>

			</div>
				<div class="see-more-wide-div">
					<a class="see-more-other-pages" id="load-more-notification">
						Load more <span>&#65516;</span>
						<div class="loading loadin-more" id="more-notifications-loading-gif"></div>	
					</a>

				</div>
			</div>		
		</div>

		<div id="side_bar_right">
			<?php include '../includes/right_side_bar.php'; ?>
		</div>
	</div>
<style type="text/css">
	.delete-notification{cursor: pointer;}
</style>
</body>
</html>