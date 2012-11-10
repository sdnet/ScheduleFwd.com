<? require("php_includes/isLoggedIn.php"); ?>
<? require("php_includes/protectedPage.php"); ?>
<html>
	<head>
		<title>Schedule Forward :: Medical scheduling software made easy</title>
        <style>
			.highlightLink {
				font-weight: bold;
			}
		</style>
		<?php 
			include("html_includes/adminMeta.php"); 
			if ($role == "Admin") {
				include("html_includes/admin-home-twocolumn.php");	
			} elseif ($role == "Scribe") {
				include("html_includes/scribe-home.php");	
			} else {
				include("html_includes/user-home.php");	
			}
		?>
		<!-- Copyright -->
			<div id="copyright">
				(c) 2012 Forward Intelligence Systems, LLC. All rights reserved.
			</div>

	</body>
</html>