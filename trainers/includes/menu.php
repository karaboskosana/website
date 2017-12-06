<!--Connected header with User Name & Avatar, Notifications,Logout Buttons AND a Hidden Menu
		When you click the User Avatar the menu will appear
	-->
	<!--HEADER BEGIN-->
	<header class="connected-header">		
		<div id="inside-connected-header">
			<a href="profile.php"><img id="connected-user-avatar-responsive" src="../avatars/perso/<?php echo $_SESSION["Avatar"]; ?>" /></a>	
			<div id="connected-logo">
				<a class="logo"><img src="../images/logo.png" height="50px"/></a>
			</div>			
			<div id="connected-user-header">		
					<img id="connected-user-avatar" src="../avatars/perso/<?php echo $_SESSION["Avatar"]; ?>" />
					<a href="profile.php" id="UserNameTopBar" title="My profile"><?php echo $_SESSION["FullName"]; ?></a>
					<div id="menu-header">
						<a href="../home">
							<div class="menu-icone small-top-bar-icones" style="background-position:-90px -1px;"></div>
							<span>Home</span>
						</a>
						<a href="../home/categories-list.php" >
							<div class="menu-icone small-top-bar-icones" style="background-position:-123px -2px;"></div>
							<span>Categories</span>
						</a>
						<a href="../home/rooms.php" style="margin-left:16px;">
							<div class="menu-icone small-top-bar-icones" style="background-position:-156px -1px;"></div>
							<span>Rooms</span>
						</a>
						<input type="text" value="Search..." id="search-top-bar">
					</div>
					<a href="notifications.php" class="small-top-bar-icones" id="notification-user" title="My Notifications">
						<span id="notification-number" class="RedGradient" <?php if($_SESSION["Notification"]==0) echo "style='display:none'"; ?> ><?php echo $_SESSION["Notification"]; ?></span>
					</a>	
					<a href="logout.php" class="small-top-bar-icones" id="log-out-user" title="Log out"></a>					
			</div>
			<div id="responsive-top-menu">
				<a href="notifications.php" class="small-top-bar-icones" id="notification-user" title="My Notifications">
					<span id="notification-number" class="RedGradient" <?php if($_SESSION["Notification"]==0) echo "style='display:none'"; ?> ><?php echo $_SESSION["Notification"]; ?></span>
				</a>	
				<a href="logout.php" class="small-top-bar-icones" id="log-out-user" title="Log out"></a>
				<div id="user-menu-show" title="Show menu"></div>	
			</div>	
			<nav class="responsive-nav-user WhiteHeader">
					<li><a href="../home">HOME</a></li>
					<li><a href="../home/categories-list.php">CATEGORIES</a></li>
					<li><a href="../home/rooms.php">ROOMS</a></li>
					<li><input type="text" value="Search..." id="search-top-bar-responsive" class="search-input"></li>
			</nav>
		</div>
	</header>
		<!--HEADER END-->