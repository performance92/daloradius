<?php

    include ("library/checklogin.php");
    $operator = $_SESSION['operator_user'];

	include('library/check_operator_perm.php');


        //setting values for the order by and order type variables
        isset($_REQUEST['orderBy']) ? $orderBy = $_REQUEST['orderBy'] : $orderBy = "id";
        isset($_REQUEST['orderType']) ? $orderType = $_REQUEST['orderType'] : $orderType = "desc";



	include_once('library/config_read.php');
    $log = "visited page: ";
    $logQuery = "performed query on page: ";


?>

<?php

    include ("menu-reports.php");
        	
?>		
		
		
		<div id="contentnorightbar">
		
		<h2 id="Intro"><a href="#"><? echo $l[Intro][indexlastconnect.php]; ?></a></h2>

<?php

        include 'library/opendb.php';
        include 'include/management/pages_numbering.php';               // must be included after opendb because it needs to read the CONFIG_IFACE_TABLES_LISTING variable from the config file

        $sql = "SELECT user, pass, reply, date from radpostauth";
        $res = $dbSocket->query($sql);
	$numrows = $res->numRows();

        $sql = "SELECT user, pass, reply, date from radpostauth ORDER BY $orderBy $orderType LIMIT $offset, $rowsPerPage";
        $res = $dbSocket->query($sql);
        $logDebugSQL = "";
        $logDebugSQL .= $sql . "\n";

        /* START - Related to pages_numbering.php */
        $maxPage = ceil($numrows/$rowsPerPage);
        setupLinks($pageNum, $maxPage, $orderBy, $orderType);

        if ($configValues['CONFIG_IFACE_TABLES_LISTING_NUM'] == "yes")
                setupNumbering($numrows, $rowsPerPage, $pageNum, $orderBy, $orderType);
        /* END */


        $array_users = array();
        $array_pass = array();
        $array_starttime = array();
        $array_reply = array();
        $count = 0;

        while($row = $res->fetchRow()) {

                // The table that is being procuded is in the format of:
                // +-------------+-------------+---------------+---------------------+
                // | user        | pass        | reply         | date                |
                // +-------------+-------------+---------------+---------------------+


                $user = $row[0];
                $pass = $row[1];
                $starttime = $row[3];
                $reply = $row[2];

                array_push($array_users, "$user");
                array_push($array_pass, "$pass");
                array_push($array_starttime, "$starttime");
                array_push($array_reply, "$reply");

                $count++;

        }
        // creating the table:
        echo "<br/>";
        echo "<table border='2' class='table1'>\n";
        echo "
                        <thead>
                                <tr>
                                <th colspan='10'>Last 50 connection attempts</th>
                                </tr>
                        </thead>
                ";
        echo "<thread> <tr>
                        <th scope='col'> Username</th>
                        <th scope='col'> Password </th>
                        <th scope='col'> Logged-In Time </th>
                        <th scope='col'> RADIUS Reply Packet </th>
                </tr> </thread>";

        $i = 0;
        while ($i != $count) {
                echo "<tr>
                        <td> $array_users[$i] </td>
                        <td> $array_pass[$i] </td>
                        <td> $array_starttime[$i] </td>
                        <td> $array_reply[$i] </td>
                </tr>";
                $i++;
        }

        echo "</table>";

        include 'library/closedb.php';

?>


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
