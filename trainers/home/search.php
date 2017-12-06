<?php
include '../includes/session.php';
//this page will display the resutl of a searched keyword
//it will display all the subkjects that matches the search desired
?>
<!DOCTYPE html>
<html>
<head>
	<title>Subjects</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../style/sharedStyle.css" />
	<link rel="stylesheet" type="text/css" href="../style/style.css" />
	<link rel="icon" type="image/png" href="../images/forumLogo.png" />	
	<script type="text/javascript" src="../script/jquery.js"></script>
	<script type="text/javascript" src="../script/jquery-ui.js"></script>
	<script type="text/javascript" src="../script/main.js"></script>
</head>
<body>
	<?php
		//importing the menu
		include '../includes/menu.php';
	?>
	<!--#######################################################################################################-->
	<div id="subject-room-top-div">
		<div id="subject-room-div-insider">
			<div id="room-name-display">Search result</div>			
		</div>
	</div>

	<div id="container">
		<div id="left-container">
			<div id="subjects-list">
				<div id="theListOsSubjects">
					<?php
						$subject = new Subject();//Function that will return subjects depending on the keyword 
						if ($subject->SearchSubjects(0,9,$_GET["keyword"])=="empty") {
					?>
					<div id="No-Result">No result were found for the <strong><?php echo $_GET["keyword"]; ?></strong> keyword.</div>
					<?php
						}else{
							$Subjects_List = $subject->SearchSubjects(0,9,$_GET["keyword"]);
							//this function will print us the subjects 
							PrintSubject($Subjects_List,$token);
						}					
						
					?>	
				</div>

				<!--<div class="see-more-wide-div">
					<a class="see-more-other-pages" id="load-more-subjects">
						Load more <span>&#65516;</span>
						<div class="loading loadin-more"></div>
					</a>	
				</div>-->
			</div>
		</div>

		<div id="side_bar_right">
			<?php include '../includes/right_side_bar.php'; ?>
		</div>
	</div>
</body>
</html>