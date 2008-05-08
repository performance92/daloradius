<?php

// first we create a random session key
$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];						// get client ip address
srand((double)microtime()*1000000 );						// initialize random seed
$rand = rand(1,9);								// generate a random number between 1 to 9
$session_id = $rand.substr(md5($REMOTE_ADDR), 0, 11+$rand);			// append the random number to the beginning
										// of the session_id string followed by a
										// substring of the md5 ip address hash with
										// a dynamic length of anything between 11
										// to 16 digits (the max length of the md5
										// hash)
$session_id .= substr(md5(rand(1,1000000)), rand(1,32-$rand), 21-$rand);	// further add a dynamic length digits to 
										// to the session_id string composed of the
										// md5 hash for random number
session_id($session_id);							// apply the session_id that we created

// we must never forget to start the session
session_start();								// initiate the session

$errorMessage = '';
   include 'library/opendb.php';

   $operator_user = $_REQUEST['operator_user'];
   $operator_pass = $_REQUEST['operator_pass'];

   // check if the user id and password combination exist in database
   $sql = "SELECT username FROM ".$configValues['CONFIG_DB_TBL_DALOOPERATOR']." WHERE username = '".$dbSocket->escapeSimple($operator_user)."' 
AND password = '".$dbSocket->escapeSimple($operator_pass)."'";

   $res = $dbSocket->query($sql);

   if ($res->numRows() == 1) {

      // the user id and password match,
      // set the session
      $_SESSION['logged_in'] = true;
      $_SESSION['operator_user'] = $operator_user;

	// lets update the lastlogint time for this operator
        $date = date("Y-m-d H:i:s");
	$sql = "UPDATE operators SET lastlogin='$date' WHERE username='$operator_user'";
	$res = $dbSocket->query($sql);

      // after login we move to the main page
      header('Location: index.php');
      exit;
   } else {
      header('Location: login.php?error=an error occured');
      exit;
   }

   include 'library/closedb.php';

?>
