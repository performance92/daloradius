<?php

    include ("library/checklogin.php");
    $operator = $_SESSION['operator_user'];

        $username = $_POST['username'];
        $type = $_POST['type'];


?>

<?php
	
	include ("menu-graphs.php");
	
?>
		
		
		<div id="contentnorightbar">
		
		<h2 id="Intro"><a href="#"><? echo $l[Intro][graphsoveralllogins.php]; ?></a></h2>

<?php
	echo "<img src=\"library/exten-alltime_user_logins.php?type=$type&user=$username\" />";

	include 'library/graph-user_login_overall.php';
?>
		

		</div>
		
		<div id="footer">
		
								<?php
        include 'page-footer.php';
?>

		
		</div>
		
</div>
</div>


</body>
</html>
