<?php

    include ("library/checklogin.php");
    $operator = $_SESSION['operator_user'];
        
	include_once('library/config_read.php');
    $log = "visited page: ";
	
   if (isset($_REQUEST["submit"])) {
	$googleMapsCode = $_REQUEST['code'];
	writeGoogleMapsCode($googleMapsCode);
    }

    function writeGoogleMapsCode($googleMapsCode) {
	$myfile = "library/googlemaps.php";
	if ($fh = fopen($myfile, 'w') ) {
		$strCode = "<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=" . $googleMapsCode . "'
			type='text/javascript'></script>";
		fwrite($fh, $strCode);
	        fclose($fh);

		$actionStatus = "success";
		$actionMsg = "Successfully updated GoogleMaps API Registration code";
	} else {
		$actionStatus = "failure";
		$actionMsg = "error: could not open the file for writing: $myfile
		<br/> Check file permissions. The file should be writable by the webserver's user/group";
	}
    }

?>

<?php
	
	include ("menu-gis.php");
	
?>


		<div id="contentnorightbar">
		
		<h2 id="Intro"><a href="#" onclick="javascript:toggleShowDiv('helpPage')"><? echo $l['Intro']['gismain.php']; ?></a></h2>

                <div id="helpPage" style="display:none;visibility:visible" >
			<?php echo $l['helpPage']['gismain'] ?>		
		</div>
				
<?php
	include('include/config/logging.php');
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
