//MAIN JS SCRIPIT !!
/*
This scriot will allow us to control most of the actions on the website
Many action can be done using this script every action is defined by a JavaScript function
	1-	Hide and Show the Top bar Menu 
	2-  Search for subjects
	3-	Notification Checker every some seconds
	4-	Function to edit an existin Subject
	5-	Function to comment a non-closed Subject
	6-	Function to like or dislike a Subject
	7-	Function to close or open my Subject
	8-	Function to delete my Subject
	9-	Function to Report a Subject as Spam
	10-	Function to like or dislike a comment of a subject
	11-	Function to Accept a room Request by an Admin
	12-	Function to Delete a Room By an Admin
	13-	Function to Delete a Category By an Admin
	14- Some other function for Design and showing and hiding some divs
*/



var menuShown=true;
$(function () {	

		


	//Showing and Hiding the responsive menu on the user pages
	$(".responsive-nav-user").hide();
	$("#user-menu-show").click(function(){
		$(".responsive-nav-user").toggle();
	}); 


	//Showing and Hiding the responsive menu on the guest pages
	$(".responsive-nav").hide();
	$("#index-menu-show").click(function(){
		$(".responsive-nav").toggle();
	}); 

	//Showing and Hiding the top bar menu with some animation	
	//$("#menu-header").hide();
	$("#connected-user-avatar").click(function(){
		if (menuShown==false) {
			$("#menu-header").show("slide",{direction:"right"},100).animate({width:"580px"},500);	
			menuShown=true;
		}
		else if (menuShown==true) {
			$("#menu-header").animate({width:"0px"},500).hide("slide",{direction:"right"},500);	
			menuShown=false;
		}
	});
	//$(".comment-added").hide();

	//search for subjects by title using the search input on the top menu bar
	$(".search-input").focus(function(e){
		if ( $(this).val()=="Search...") {
			$(this).val("");
		};
	});

	$(".search-input").focusout(function(e){
		if ( $.trim($(this).val())=="") {
			$(this).val("Search...");
		};
	});

	//When pressing enter on the search input the user will be redirected to the search result page to see the subjects that matches the givven keyword
	$(".search-input").keydown(function(e){
		var keyword = $(this).val(); 
		if (e.keyCode==13) {
			if ($.trim(keyword)!="") {
				window.location.href = "search.php?keyword="+keyword;
			}
		}

	});

	//script that will be executed every 10 seconds
	//To see if a new notifivation is inserted
	setInterval(function(){
		$.ajax({
			type:"POST",
			url:"../api/api.php",
			data:{action:"checkNotification"},
			success:function(data){
				if (data!=0) {
					$("#notification-number").html(data).fadeIn(400);
				}
			}
		});
	},10000);



	//hide the edit subject div
	$("#edit-subject-div").hide();
	$(".edit-subject-success").hide();
	$(".edit-subject-loading").hide();
	$("#error-edit-subject").hide();
	$(".cancel-edit-subject").click(function(){
		hideEditSubjectDiv();
	});
	//Submit the edit of the subject
				$("#edit-subject-button").click(function() {
					//taking the title and the text of the subject and making on the edit inputs
					$(".edit-subject-loading").show();	
					var Title = $("#edit-subject-name").val();
					var Text = $("#edit-subject-text-div").find(".nicEdit-main").html();
					var SID = $("#edit-subject-id").val();	
					$("#edit-subject-title-"+SID).html(Title);//replacing title by new one
					$("#edit-subject-text-"+SID).html(Text);//replacing text by new one
					var token = $("#edit-token").val();
					$("#edit-subject-name").val("");
 					$("#edit-subject-text-div").find(".nicEdit-main").html("");
					if ($.trim(Title)!="" && SID!="" && $.trim(Text)!="") {
						$.ajax({//Ajax reuqest to eding an existing Subject
							type:"POST",
							url:"../api/api.php",
							data:{Title:Title,Text:Text,SID:SID,action:"editSubject",token:token},
							success:function(data){
								$(".edit-subject-loading").hide();
								$(".edit-subject-success").fadeIn(100);
								$(".edit-subject-success").fadeOut(2000,function(){
									hideEditSubjectDiv();
								});
							}
						});
					}else{
						$(".edit-subject-loading").hide();
						$("#error-edit-subject").fadeIn(100,function(){
							$("#error-edit-subject").fadeOut(3000);
						});
					}

				});

});

	//Show Socil links share of the subject
		function showsocialshare(button){
			$(button).children(".share-show-social").toggle();
		}



	//ADD A NEW COMMENT TO THE SUBJECT	
	//You can only comment for Opened Subjects			
				function commentsubject(button,key,SID,Notified,User,token){
					if (key.keyCode==13) {
						var cText = $(button).val();
						var SID = SID;
						if ($.trim(cText)!="") {
							$.ajax({
								type:"POST",
								url:"../api/api.php",
								data:{cText:cText,User:User,SID:SID,Notified:Notified,action:"addComment",token:token},
								success:function(data){
									data = JSON.parse(data); 
									var comment ='<div class="comment"><div class="comment-user-avatar"><img src="../avatars/perso/'+data["Avatar"]+'"></div><div class="comment-user-info-date"><span class="comment-user-fullname">'+data["FullName"]+'</span><span class="comment-display-date">'+data["cDate"]+' -</span><span class="delete-comment" onclick="deletecomment(this,'+data["CID"]+','+data["Subject"]+')">x</span><span class="choose-best-comment" onclick="closesubject(this,'+data["Subject"]+','+data["CID"]+');">Best</span></div><div class="comment-display-text">'+data["cText"]+'</div><a class="comment-display-like" onclick="likecomment('+data["CID"]+');">like</a></div>';
									$("#subject-last-comment-"+data["Subject"]).append(comment);//Printing the new added comment
								}
							});
							 $(button).parent(".subject-add-quick-comment").children(".comment-added").fadeIn(100,function(){
								$(button).parent(".subject-add-quick-comment").children(".comment-added").fadeOut(3000);
							});
						}
						$(button).val("");
					}				
				}

				//LIKE A SUBJECT
				//this function will add a new like to the subject
				function likesubject(button,SID,Notified,User,token){
					var operation = "plus";
					$.ajax({
						type:"POST",
						url:"../api/api.php",
						data:{User:User,Notified:Notified,SID:SID,operation:operation,action:"likeSubject",token:token},
						success:function(data){
						}	
					});
					//changing the like button to the unlike button	
					$(button).attr({"onclick":"unlikesubject(this,"+SID+","+Notified+","+User+");","style":"background-position: -60px -27px"});
				}	

				//UnLIKE A SUBJECT
				//this function will delete like from subject
				function unlikesubject(button,SID,Notified,User,token){
					var operation = "minus";
					$.ajax({
						type:"POST",
						url:"../api/api.php",
						data:{User:User,Notified:Notified,SID:SID,operation:operation,action:"likeSubject",token:token},
						success:function(data){
						}	
					});
					//changing the unlike button to the like button
					$(button).attr({"onclick":"likesubject(this,"+SID+",'',"+User+");","style":"background-position: -85px -27px"});
				}	

				//DELETE MY SUBJECT
				//when deleting a subject all the comments and likes of that subject will be deleted as well
				function deletesubject(button,SID,Room,token){
					$.ajax({
						type:"POST",
						url:"../api/api.php",
						data:{SID:SID,Room:Room,action:"deleteSubject",token:token},
						success:function(data){
							$(button).parent(".subject-actions-left").parent(".subject-actions").parent(".subject").fadeOut(500);	
						}
					});				
				}

				//CHOOSE THE BEST COMMENT MY SUBJECT
				//when you choose a best comment you will close the subject no more comments will be allowed to be added thats mean that you have found the answer and we need you to choose the best answer
				//this function will also close subject even if you dont choose the best comment for it you can opem it later so other users will be able to comment on it
				function closesubject(button,SID,CID,token){
					$.ajax({
						type:"POST",
						url:"../api/api.php",
						data:{SID:SID,CID:CID,action:"closeSubject",token:token},
						success:function(data){											
						}
					});
					$("#close-subject-"+SID).attr({"class":"small-top-bar-icones subject-action-state" ,"id":"open-subject-"+SID+"", "onclick":"opensubject(this,"+SID+",0);", "title":"Open the subject" , "style":"background-position:-231px -26px;"});
					$("#comment-subject-area-"+SID).slideUp(500);
				}

				//OPEN A SUBJECT
				//when openning a subject the best comment will be deleted and users will be abale to re-post more comments				
				function opensubject(button,SID,CID,token){
					$.ajax({
						type:"POST",
						url:"../api/api.php",
						data:{SID:SID,CID:CID,action:"openSubject",token:token},
						success:function(data){										
						}
					});
					$("#open-subject-"+SID).attr({"class":"small-top-bar-icones subject-action-state" ,"id":"close-subject-"+SID+"", "onclick":"closesubject(this,"+SID+",0);" ,"title":"Close the subject" , "style":"background-position:-254px -26px;"});
					$("#comment-subject-area-"+SID).slideDown(500);
				}

				//REPORT A SUBJECT
				//This function will allow us to insert a report or delete	an exisiting one
				function reportsubject(button,SubjectId,UserId,operation,token){
					$.ajax({
						type:"POST",
						url:"../api/api.php",
						data:{SubjectId:SubjectId,UserId:UserId,operation:operation,action:"reportSubject",token:token},
						success:function(data){							
						}
					});
					if (operation=="plus") 
						$(button).attr({"style":"background-position:-312px -32px;" ,"onclick":"reportsubject(this,"+SubjectId+","+UserId+",'minus');" ,"title":"Not Spam"});
					else if(operation=="minus")
						$(button).attr({"style":"background-position:-312px -3px;" ,"onclick":"reportsubject(this,"+SubjectId+","+UserId+",'plus');" ,"title":"Report as Spam"});
					
								
				}



				//DELETE MY COMMENT
				//When deleting a comment all it's likes will be deleted as well
				function deletecomment(button,CID,SID,token){
					$.ajax({
						type:"POST",
						url:"../api/api.php",
						data:{CID:CID,SID:SID,action:"deleteComment",token:token},
						success:function(data){
							$(button).parent(".comment-user-info-date").parent(".comment").slideUp(500);
						}
					});					
				}


				//LIKE AND UNLIKE A COMMENT
				function comment_like_unlike(button,operation,CID,SID,Notified,User,token){
					var line_number = parseInt($("#likes-number-comment-"+CID).html());
					$.ajax({
						type:"POST",
						url:"../api/api.php",
						data:{operation:operation,CID:CID,Notified:Notified,SID:SID,User:User,action:"unlineAndlikeComment",token:token},
						success:function(data){
							if(operation=="plus"){
								$(button).attr({"onclick":"comment_like_unlike(this,'minus',"+CID+","+SID+","+Notified+","+User+")"});
								$(button).html("unlike");
								$("#likes-number-comment-"+CID).html(line_number+1);
							}
							if (operation=="minus") {
								$(button).attr({"onclick":"comment_like_unlike(this,'plus',"+CID+","+SID+","+Notified+","+User+")"});
								$(button).html("like");
								$("#likes-number-comment-"+CID).html(line_number-1);
							}
						}
					});
				}


				//FUNCTION TO EDIT ONE OF MY SUBJECTS
				//this script will sit a subject using it's id
				function editsubject (button,SID) {
					var Title = $("#edit-subject-title-"+SID);
					var Text = $("#edit-subject-text-"+SID);
					
					$("#edit-subject-name").val(Title.html());
					$("#edit-subject-text-div").find(".nicEdit-main").html(Text.html());
					
					$("#edit-subject-id").val(SID);
					showEditSubjectDiv();
				}



				//function that will show edit subject div
				function showEditSubjectDiv(){
					$("#BIGBOSS").fadeIn(200,function(){
						$("#edit-subject-div").slideDown(400);
					});
				}

				//function that will hide the edit  subject
				function hideEditSubjectDiv(){				
					$("#BIGBOSS").slideUp(300,function(){
						$("#edit-subject-div").hide();
					});
				}


	//##########################################################################################################################
	//#############################################  A D M I N        S C R I P T  #############################################
	//##########################################################################################################################	
	//this function will accept a pending room
	function acceptRoom(e,RoomId,CategoryId,token){
		$.ajax({//Ajax Request to the server
			type:"POST",
			url:"../api/api.php",
			data:{ID:RoomId,CATID:CategoryId,actionAdmin:"acceptRoom",token:token},//Data that will be sent to the server
			success:function(data){
				$("#room-div-"+RoomId).fadeOut(500);//Animation After Accepting A room
			}
		});
		e.preventDefault();	
	}

	//this function will decline a user adding room and it will delete it from the database
	function deleteRoom(e,RoomId,token){
		$.ajax({//Ajax Request to the server
			type:"POST",
			url:"../api/api.php",
			data:{ID:RoomId,actionAdmin:"deleteRoom",token:token},//Data that will be sent to the server
			success:function(data){
				$("#room-div-"+RoomId).fadeOut(500);//Animation After Deleting A room
			}
		});
		e.preventDefault();	
	}

	//this function will delete a category from the database
	//by deleting a category all it's rooms will be deleted 
	//All room's subjects will be deleted too
	//All the subject's comments will be deleted too
	//All the Likes and Followers will be deleted too
	function deleteCategory(e,CategoryId,token){
		$.ajax({
			type:"POST",
			url:"../api/api.php",
			data:{CATID:CategoryId,actionAdmin:"deleteCategory"},
			success:function(data){
				//alert(data);
				$("#category-div-"+CategoryId).fadeOut(500);
			}
		});
		e.preventDefault();	
	}





//function to make the first letter of a word in UpperCase
function ucFirst(string) {
	return string.substring(0, 1).toUpperCase() + string.substring(1).toLowerCase();
}