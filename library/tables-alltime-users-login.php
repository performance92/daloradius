<?php
/*
 *********************************************************************************************************
 * daloRADIUS - RADIUS Web Platform
 * Copyright (C) 2007 - Liran Tal <liran@enginx.com> All Rights Reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 *********************************************************************************************************
 * Description:    this graph extension procduces a query of the alltime logins
 *                 made by all users on a daily, monthly and yearly basis.
 *
 * Authors:	       Liran Tal <liran@enginx.com>
 *                 Filippo Lauria <filippo.lauria@iit.cnr.it>
 *
 *********************************************************************************************************
 */

// prevent this file to be directly accessed
$extension_file = '/library/tables-alltime-users-login.php';
if (strpos($_SERVER['PHP_SELF'], $extension_file) !== false) {
    header("Location: ../index.php");
    exit;
}

// used for presentation purpose
$label_param = array();
$label_param['day'] = "Day of month";
$label_param['month'] = "Month of year";
$label_param['year'] = "Year";

$orderType = (array_key_exists('orderType', $_GET) && isset($_GET['orderType']) &&
              in_array(strtolower($_GET['orderType']), array( "desc", "asc" )))
           ? strtolower($_GET['orderType']) : "asc";

$type = (array_key_exists('type', $_GET) && isset($_GET['type']) &&
         in_array(strtolower($_GET['type']), array( "daily", "monthly", "yearly" )))
      ? strtolower($_GET['type']) : "daily";

include('opendb.php');
include('include/management/pages_common.php');
include('include/management/pages_numbering.php');    // must be included after opendb because it needs to read
                                                      // the CONFIG_IFACE_TABLES_LISTING variable from the config file

switch ($type) {
    case "yearly":
        $selected_param = "year";
        $orderBy = (array_key_exists('orderBy', $_GET) && isset($_GET['orderBy']) &&
                    in_array(strtolower($_GET['orderBy']), array( "numberoflogins", "year" )))
                 ? strtolower($_GET['orderBy']) : "year";
        $sql = "SELECT YEAR(AcctStartTime) AS year, COUNT(username) AS numberoflogins
                 FROM %s GROUP BY year ORDER BY %s %s";
        break;
    
    case "monthly":
        $selected_param = "month";
        $orderBy = (array_key_exists('orderBy', $_GET) && isset($_GET['orderBy']) &&
                    in_array(strtolower($_GET['orderBy']), array( "numberoflogins", "month" )))
                 ? strtolower($_GET['orderBy']) : "month";
        $sql = "SELECT CONCAT(MONTHNAME(AcctStartTime), ' (', YEAR(AcctStartTime), ')'),
                       COUNT(username) AS numberoflogins,
                       CAST(CONCAT(YEAR(AcctStartTime), '-', MONTH(AcctStartTime), '-01') AS DATE) AS month
                  FROM %s GROUP BY month ORDER BY %s %s";
        break;
    
    default:
    case "daily":
        $selected_param = "day";
        $orderBy = (array_key_exists('orderBy', $_GET) && isset($_GET['orderBy']) &&
                    in_array(strtolower($_GET['orderBy']), array( "numberoflogins", "day" )))
                 ? strtolower($_GET['orderBy']) : "day";
        $sql = "SELECT DATE(AcctStartTime) AS day, COUNT(username) AS numberoflogins
                FROM %s GROUP BY day ORDER BY %s %s";
        break;
}

$sql = sprintf($sql, $configValues['CONFIG_DB_TBL_RADACCT'], $orderBy, $orderType);
$res = $dbSocket->query($sql);

$numrows = $res->numRows();

if ($numrows > 0) {
    
    $total_logins = 0;
    while ($row = $res->fetchRow()) {
        $total_logins += intval($row[1]);
    }
    
    $sql .= sprintf(" LIMIT %s, %s", $offset, $rowsPerPage);
    $res = $dbSocket->query($sql);
    
    /* START - Related to pages_numbering.php */
    $maxPage = ceil($numrows/$rowsPerPage);
    /* END */
    
    $partial_query_string = "&type=$selected_param";
    
    $cols = array( 
                   $selected_param => $label_param[$selected_param],
                   "numberoflogins" => "Number of logins/hits"
                 );
    $colspan = count($cols);
    $half_colspan = intdiv($colspan, 2);
    
?>
<div style="text-align: center; margin-top: 50px">
    <h4>All-time Logins/Hits statistics</h4>
    <br>
    <table border="0" class="table1">
        <thead>
<?php
    if ($maxPage > 1) {
        printf('<th colspan="%s">', $colspan);
        setupNumbering($numrows, $rowsPerPage, $pageNum, $orderBy, $orderType, $partial_query_string);
        echo "</th>";
    }
?>
            <tr>
<?php
    foreach ($cols as $label => $caption) {
        
        if (is_int($label)) {
            $ordering_controls = "";
        } else {
            $href_format = "?orderBy=%s&orderType=%s" . $partial_query_string;
            $href_asc = sprintf($href_format, $label, "asc");
            $href_desc = sprintf($href_format, $label, "desc");
            
            $title_format = "order by %s, sort %s";
            $title_asc = sprintf($title_format, strip_tags($caption), "ascending");
            $title_desc = sprintf($title_format, strip_tags($caption), "descending");
            
            $a_format = '<a title="%s" class="novisit" href="%s"><img src="%s" alt="%s"></a>';
            
            $ordering_controls = sprintf($a_format, $title_asc, $href_asc, 'images/icons/arrow_up.png', '^')
                               . sprintf($a_format, $title_desc, $href_desc, 'images/icons/arrow_down.png', 'v');

        }
        
        echo "<th>" . $caption . $ordering_controls . "</th>";
    }
?>
            </tr>
        </thead>

        <tbody>
<?php
    
    $per_page_logins = 0;
    while ($row = $res->fetchRow()) {
        $logins = intval($row[1]);
        
        echo "<tr>"
           . "<td>" . htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8') . "</td>"
           . "<td>" . $logins . "</td>"
           . "</tr>";
        $per_page_logins += $logins;
    }
?>
        </tbody>
        
        <tfoot>
            <tr>
<?php
    if ($maxPage > 1) {
?>
                <th scope="col" colspan="<?= ($colspan % 2 === 0) ? $half_colspan : $half_colspan + 1 ?>">
                    <?= setupLinks($pageNum, $maxPage, $orderBy, $orderType, $partial_query_string) ?>
                </th>
<?php
    }
?>
                <th scope="col" colspan="<?= ($maxPage > 1) ? $half_colspan : $colspan ?>">
                    <?= "<strong>$per_page_logins</strong> out of <strong>$total_logins</strong>" ?>
                </th>
            </tr>
        </tfoot>

    </table>
</div>

<?php
} else {
    // $numrows <= 0
    $failureMsg = "No login(s) found";
    include_once("include/management/actionMessages.php");
}

include('closedb.php');

?>
