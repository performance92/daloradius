<?php
/*******************************************************************
* Extension name: hotspot compare time                             *
*                                                                  *
* Description:    this extension creates a pie chart of the        *
* comparison of hotspots per unique users                          *
*                                                                  *
* Author: Liran Tal <liran@enginx.com>                             *
*                                                                  *
*******************************************************************/

	include 'opendb.php';
	include 'libchart/libchart.php';

        header("Content-type: image/png");

        $chart = new PieChart(620,320);

	// getting total downloads of days in a month
	$sql = "select ".$configValues['CONFIG_DB_TBL_DALOHOTSPOTS'].".name, count(distinct(UserName)), count(radacctid), avg(AcctSessionTime), sum(AcctSessionTime) from ".$configValues['CONFIG_DB_TBL_RADACCT']." join ".$configValues['CONFIG_DB_TBL_DALOHOTSPOTS']." on (".$configValues['CONFIG_DB_TBL_RADACCT'].".calledstationid like ".$configValues['CONFIG_DB_TBL_DALOHOTSPOTS'].".mac) group by ".$configValues['CONFIG_DB_TBL_DALOHOTSPOTS'].".name;";
	$res = $dbSocket->query($sql);

	while($row = $res->fetchRow()) {
                $chart->addPoint(new Point("$row[0] ($row[3] seconds)", "$row[3]"));
        }

        $chart->setTitle("Distribution of Time usage per Hotspot");
        $chart->render();

	include 'closedb.php';




?>

