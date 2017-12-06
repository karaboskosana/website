<?php
/*
NOTIFICATION CLASS
this class is used to insert notification and inform users about it 
by using this class you will be able to do the following:
1-	Insert a new Notification When someone:
		a-	likes my post,forum,subject
		b-	Comments my post,forum,subject
		c- 	Friendship request
		d-	Starts following me
2- 	Delete a notifications
3- 	Mark notifications as read
4-	Return the list of notifications

*/

class Notification
{
	private $NotifId,$Notified,$Notifier,$NotifDate,$NotifTable,$NotifWhatId,$NotifAction;

	function setNotifId($NotifId) { $this->NotifId = $NotifId; }
	function getNotifId() { return $this->NotifId; }
	function setNotified($Notified) { $this->Notified = $Notified; }
	function getNotified() { return $this->Notified; }
	function setNotifier($Notifier) { $this->Notifier = $Notifier; }
	function getNotifier() { return $this->Notifier; }
	function setNotifDate($NotifDate) { $this->NotifDate = $NotifDate; }
	function getNotifDate() { return $this->NotifDate; }
	function setNotifTable($NotifTable) { $this->NotifTable = $NotifTable; }
	function getNotifTable() { return $this->NotifTable; }
	function setNotifWhatId($NotifWhatId) { $this->NotifWhatId = $NotifWhatId; }
	function getNotifWhatId() { return $this->NotifWhatId; }
	function setNotifAction($NotifAction) { $this->NotifAction = $NotifAction; }
	function getNotifAction() { return $this->NotifAction; }


//INSERT NOTFICATION
public function InsertNotification($Notified,$Notifier,$NotifDate,$NotifTable,$NotifWhatId,$NotifAction,$NotifSentence){
	include 'connect.php';
	if($Notified!=$Notifier){
		$req = $bdd->prepare("INSERT INTO notification(Notified,Notifier,NotifDate,NotifTable,NotifWhatId,NotifAction,NotifSentence) VALUES(:Notified,:Notifier,:NotifDate,:NotifTable,:NotifWhatId,:NotifAction,:NotifSentence)");
		$req->execute(array(
			"Notified"=>$Notified,
			"Notifier"=>$Notifier,
			"NotifDate"=>$NotifDate,
			"NotifTable"=>$NotifTable,
			"NotifWhatId"=>$NotifWhatId,
			"NotifAction"=>$NotifAction,
			"NotifSentence"=>$NotifSentence
		));	
		$user = new User();
		$user->setUID($Notified);
		$user->NotificationAdd();
	}
}

//DELETE NOTIFICATION
public function DeleteNotification($NotifId,$Notified){
	include 'connect.php';
	$req = $bdd->prepare("DELETE FROM notification WHERE NotifId=:NotifId AND Notified=:Notified");
	$req->execute(array(
		'NotifId'=>$NotifId,
		'Notified'=>$Notified
	));
	$user = new User();
	$user->setUID($Notified);
	$user->NotificationRead();
	$_SESSION["Notification"] =0;
}

//MARK AS READ NOTIFICATION
public function MarkAsRead($UserId){
	include 'connect.php';
	$req = $bdd->prepare("DELETE FROM notification WHERE Notified=:Notified");
	$req->execute(array(
		'Notified'=>$UserId
	));
	$user = new User();
		$user->setUID($UserId);
		$user->NotificationRead();
		$_SESSION["Notification"] =0;
}


//NOTIFICATION LIST
public function GeneralNotificationList($UserId,$First,$Second,$token){
	include 'connect.php';
	$req = $bdd->prepare("SELECT * FROM notification n JOIN user u ON u.UID=n.Notifier WHERE Notified=:Notified ORDER BY NotifId DESC LIMIT ".$First.",".$Second);
	$req->execute(array(
		'Notified'=>$UserId
	));
	while ($data = $req->fetch()) {
		$position = "";
		/* for comments background-position: -9px -30px;*/ /* for likes background-position: -36px -29px;*/
		if ($data["NotifAction"]=="comment") 
			$position = "background-position: -9px -30px;";
		
		if ($data["NotifAction"]=="like") 
			$position = "background-position: -36px -29px;";
		
	?>
		<div class="notification" id="Notification-<?php echo $data["NotifId"]; ?>">
			<img src="../avatars/perso/<?php echo $data["Avatar"]; ?>" >
			<div class="notification-info-div">
				<div class="notification-info-top">
					<span class="notification-user-name"><?php echo $data["FullName"]; ?></span>
					<span class="delete-notification" onclick="deletenotification(event,<?php echo $data["NotifId"]; ?>,<?php echo $data["Notified"]; ?>,'<?php echo $token; ?>');" title="Delete">x</span>
				</div>
				<a href="../home/read-subject.php?subject=<?php echo $data["NotifWhatId"]; ?>" class="notification-text-page">
					<div class="small-top-bar-icones notfication-what-icon notifi-icon" style="<?php echo $position ?>"></div>
					<span class="notification-span-text"><?php echo $data["NotifSentence"]; ?></span>
					<span class="notification-date"><?php echo date("M d", strtotime($data["NotifDate"]))." at ".date("H:i",strtotime($data["NotifDate"]))."pm";?></span>	
				</a>	
			</div>
		</div>
		<?php
	}
}


}



?>