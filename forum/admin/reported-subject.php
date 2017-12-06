<?php
include '../includes/sessionAdmin.php';
//this page will display a list of reported subjects so the admin will take some action regarding that subjcet and see what's wrong with it
?>
<!DOCTYPE html>
<html>
<head>
	<title>Admin: Reported Subjects </title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../style/sharedStyle.css" />
	<link rel="stylesheet" type="text/css" href="../style/style.css" />
	<link rel="stylesheet" type="text/css" href="../style/styleAdmin.css" />
	<link rel="icon" type="image/png" href="../images/forumLogo.png" />	
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
	<div id="container" style="margin-top:200px;">
		<div id="left-container">
			<div id="subjects-list">
				<div id="theListOsSubjects">
					<?php
						$subject = new Subject();
						if ($subject->ReportedSubjectsList(0,9)!="empty") {
							$Subjects_List = $subject->ReportedSubjectsList(0,9);
							//this function will print us the subjects 
							PrintSubjectAdmin($Subjects_List,$token);
						}else{
					?>	
					<span id="No-Result"><strong>No reported subjects for the moment</strong></span>
					<?php } ?>
				</div>
				<div class="see-more-wide-div">
					<a class="see-more-other-pages" id="load-more-subjects">
						Load more <span>&#65516;</span>
						<div class="loading loadin-more"></div>
					</a>	
				</div>
			</div>
		</div>

		<div id="side_bar_right_admin">
			<?php include '../includes/right_side_bar_admin.php'; ?>
		</div>
	</div>


	
	<script type="text/javascript">
		$(function() {
			//Loading more subjects
				//We will sent an ajax request ro the server so we will upload and print more subjects that are under this actual room
				//it loads more subjects depending on the actual filter that you will find on the top right on the page
				$(".loadin-more").hide();
				var first=0;
				var filter = "date";
				$("#load-more-subjects").click(function(){
					first+=9;
					var second = 9;
					$(".loadin-more").show();
					$.ajax({
						type:"POST",
						url:"../api/api.php",
						data:{first:first,second:second,filter:filter,action:"ReportedSubjectListAdmin"},
						success:function(data){
							if ($.trim(data)!="empty") {
								$("#theListOsSubjects").append(data);
							}else
								$(".see-more-wide-div").hide();

							$(".loadin-more").hide();

						}
					});
				});

			});
							

	</script>

</body>
</html>