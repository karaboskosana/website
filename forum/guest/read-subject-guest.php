<?php
include '../includes/sessionGuest.php';
/*
read subject is the page where a user can view a specific subject 
he guest user he will be able just to view the subect he cant comment like or report it 
*/
$subject = new Subject();
if (isset($_GET["subject"]) && $_GET["subject"]!="") {//we check if the subject id is passed on the url or not

	$subject->setSID($_GET["subject"]);	
	$subject = json_decode($subject->SubjectInfo());
	if ($subject->state=="no") {//we check if the subjects is really exisit on the database if not the user will be taken to the home page
		header("Location: ../home/");
	}
}else
	header("Location: ../home/");
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $subject->Title; ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../style/sharedStyle.css" />
	<link rel="stylesheet" type="text/css" href="../style/indexStyle.css" />
	<link rel="stylesheet" type="text/css" href="../style/style.css" />
	<link rel="icon" type="image/png" href="../images/forumLogo.png" />	
	<script type="text/javascript" src="../script/jquery.js"></script>
	<script type="text/javascript" src="../script/jquery-ui.js"></script>
	<script type="text/javascript" src="../script/main.js"></script>	
</head>
<body>
	<?php
		//importing the menu
		include '../includes/menuGuest.php';
	?>
	<!--#######################################################################################################-->
	<div id="container"  style="margin-top:100px;">
		<div id="left-container">
			<div id="subjects-list">
			<div id="theListOsSubjects">
				<?php $Date = date("M d", strtotime($subject->Date))." at ".date("H:i",strtotime($subject->Date))."pm"; 
						//creating a class for the subject likes
						//Creating a class for the subject comments
						$comment = new Comment();

						$comment->setSubject($subject->SID);

				?>
				<div class="subject">
					<div class="subject-user-avatar">
						<img src="../avatars/perso/<?php echo $subject->Avatar; ?>">
					</div>	
					<div class="subject-user-info-date">
						<span class="subject-user-fullname"><?php echo $subject->FullName; ?></span>
							<!--Know if a subject is closer or not
								When it's closed you can re open so other users an comment (and the comment that you have chosen for the best comment will be deleted)
							you can also just close a subject for certain momnet so users can't comment
							-->
						<?php if ($subject->State=="closed") { ?>
						<div class="small-top-bar-icones subject-action-state" id="open-subject-<?php echo $subject->SID; ?>"   title="Open the subject" style="background-position:-231px -26px; "></div>
						<?php }else { ?>
						<div class="small-top-bar-icones subject-action-state" id="close-subject-<?php echo $subject->SID; ?>" title="Close the subject" style="background-position:-254px -26px"></div>
						<?php }?>
						<span class="subject-display-date"><?php echo $Date; ?></span>
					</div>				
					<div class="subject-title-and-text">
						<span class="subject-display-tilte" id="edit-subject-title-<?php echo $subject->SID; ?>"><?php echo $subject->Title; ?></span>
						<span id="edit-subject-text-<?php echo $subject->SID; ?>"><?php echo $subject->Text; ?></span>
					</div>
					<div class="subject-actions">
						<div class="subject-actions-left">
							<div class="small-top-bar-icones subject-actions-share" title="Share this subject" onclick="showsocialshare(this);">
								<div class="share-show-social WhiteHeader">
									<a href="http://www.facebook.com/sharer.php?u=http//www.nabil-lahssine.com/demo/shadyforum/home/read-subject.php?subject=<?php echo $subject->SID; ?>" title="Share on facebook" style="background-position:-1px 0px;"></a>
									<a href="http://twitter.com/share?url=http//www.nabil-lahssine.com/demo/shadyforum/home/read-subject.php?subject=<?php echo $subject->SID; ?>" title="Share on Twitter" style="background-position:-29px 0px;"></a>
									<a href="https://plus.google.com/share?url=http//www.nabil-lahssine.com/demo/shadyforum/home/read-subject.php?subject=<?php echo $subject->SID; ?>" title="Share on Google+" style="background-position:-61px 0px;"></a>
									<a href="http://www.linkedin.com/shareArticle?mini=true&url=http//www.nabil-lahssine.com/demo/shadyforum/home/read-subject.php?subject=<?php echo $subject->SID; ?>" title="Share on Linkedin" style="background-position:-93px 0px;"></a>
								</div>
							</div>
						</div>
						<div class="subject-actions-right">		
							<span class="how-many-subject-likes">
								<span><?php echo $subject->View; ?></span> views
							</span>					
							<span class="how-many-subject-likes" style="border-right:0px; padding-right:0px;">
								<span><?php echo $subject->Likes; ?> people</span> liked
							</span>	
						</div>
					</div>	
					<div class="subject-last-comments" id="subject-last-comment-<?php echo $subject->SID; ?>">
						<?php
							$Comments = $comment->CommentsBySubject(0,9);
							PrintCommentGuest($Comments,$subject);
						?>
									
								
					</div>
					
						
			</div>
			<div class="see-more-wide-div">
				<a class="see-more-other-pages" id="load-more-comments" >
					More comments <span>&#65516;</span>
					<div class="loading loadin-more" id="more-comments-loading-gif"></div>
				</a>	
			</div>
		</div>
		</div>
	</div>

		<div id="side_bar_right">
			<?php include '../includes/right_side_bar_guest.php'; ?>
		</div>
	</div>

	<style type="text/css">
		.comment:last-of-type{border-bottom: 0px;}
		.subject-last-comments{margin-top: 0px;}
		.subject-add-quick-comment{border-bottom: 1px solid #ccc;}
		#load-more-comments{width: 120px;}
		#more-comments-loading-gif{margin-left: 140px;}
	</style>

	<script type="text/javascript">		
		$(function(argument) {
			$("#BIGBOSS").hide();
			$(document).keyup(function(e){
					if(e.keyCode==27){						
						hideEditSubjectDiv();
					}
				});
			//script to load more comments for the subject that a user is viewing		
			$("#more-comments-loading-gif").hide();
			var first = 0;
			var second = 9;	
			$("#load-more-comments").click(function(){
				$("#more-comments-loading-gif").show();
				first+=9;	
				$.ajax({
					type:"POST",
					url:"../api/api.php",
					data:{Subject:'<?php echo $_GET["subject"]; ?>',first:first,second:second,action:"CommentsListBySubjectGuest"},
					success:function(data){
						if($.trim(data)=="empty")
							$(".see-more-wide-div").hide();
						else		
						$(".subject-last-comments").append(data);
					
						$("#more-comments-loading-gif").hide();
					}
				});
			});

			//After 15 seconds spent on the page a new view number will be added to the subject that a user is viewing
			setTimeout(function(){
				$.ajax({
					type:"POST",
					url:"../api/api.php",
					data:{SID:'<?php echo $_GET["subject"]; ?>',action:"addView"},
					success:function(data){
					}
				});
			},15000) 

		});
	</script>

</body>
</html>
