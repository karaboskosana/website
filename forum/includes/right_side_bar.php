<?php 
//This file be included on some pages to display 
//Some live notifications form the rooms that a user is subscribed too
//It will also display some of Ads that you can implement using your Google Adsence or others
//Note for the Ads that are displayed on the moment are just static 

$subscriptions = new Subject();
$subscriptions->setUser($_SESSION["UID"]);
$Subscriptions_Notifications = $subscriptions->Subscriptions();
if ($Subscriptions_Notifications!="empty") {
?>
<div id="right-container">
	<div class="side-title">	<div class="small-top-bar-icones notfication-what-icon" style="background-position:-257px -1px; margin-right:5px;"></div> My Subscriptions</div>
	<div id="right-notification-list">
		<?php $Subscriptions_Notifications = json_decode($Subscriptions_Notifications); 
			foreach ($Subscriptions_Notifications as $notif_subs) {
				$Date = date("M d", strtotime($notif_subs->Date))." at ".date("H:i",strtotime($notif_subs->Date))."pm"; 
		?>
		<a href="read-subject.php?subject=<?php echo $notif_subs->SID; ?>" class="notification-right">
			<div class="notification-right-user-avatar">
				<img src="../avatars/perso/<?php echo $notif_subs->Avatar ;?>">
			</div>
				<div class="notification-right-text">
					<span class="notification-right-user-name"><?php echo $notif_subs->FullName ;?><span><?php echo $Date ?></span></span>
					<span class="notification-right-what-done">Has added a new subject to the <?php echo $notif_subs->rTitle; ?> room.</span>
			</div>
		</a>


		<?php } ?>		
	</div>
</div>	
<?php } ?>	

<div id="publicity-right">
	<div class="side-title">	
		<div class="small-top-bar-icones notfication-what-icon" style="background-position:-287px 1px; margin-right:5px;"></div> 
		Sponsored By</div>
		<div id="advetising-div">
			<br/>
			280 x 250
			advertising space
		</div>
</div>