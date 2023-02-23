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
 *
 * Authors:        Liran Tal <liran@enginx.com>
 *                 Filippo Lauria <filippo.lauria@iit.cnr.it>
 *
 *********************************************************************************************************
 */

    include("library/checklogin.php");
    $operator = $_SESSION['operator_user'];

    include('library/check_operator_perm.php');
    include_once('library/config_read.php');

    include_once("lang/main.php");
    include("library/validation.php");
    include("library/layout.php");

    // validate this parameter before including menu
    $username = (array_key_exists('username', $_GET) && !empty(str_replace("%", "", trim($_GET['username']))))
              ? str_replace("%", "", trim($_GET['username'])) : "";
    $username_enc = (!empty($username)) ? htmlspecialchars($username, ENT_QUOTES, 'UTF-8') : "";

    $startdate = (array_key_exists('startdate', $_GET) && !empty(trim($_GET['startdate'])) &&
                  preg_match(DATE_REGEX, trim($_GET['startdate']), $m) !== false &&
                  checkdate($m[2], $m[3], $m[1]))
               ? trim($_GET['startdate']) : "";

    $enddate = (array_key_exists('enddate', $_GET) && !empty(trim($_GET['enddate'])) &&
                preg_match(DATE_REGEX, trim($_GET['enddate']), $m) !== false &&
                checkdate($m[2], $m[3], $m[1]))
             ? trim($_GET['enddate']) : "";

    $cols = array(
                    "radacctid" => t('all','ID'),
                    "hotspot" => t('all','HotSpot'),
                    "nasipaddress" => t('all','NASIPAddress'),
                    "username" => t('all','Username'),
                    "framedipaddress" => t('all','IPAddress'),
                    "acctstarttime" => t('all','StartTime'),
                    "acctstoptime" => t('all','StopTime'),
                    "acctsessiontime" => t('all','TotalTime'),
                    "acctinputoctets" => t('all','Upload'),
                    "acctoutputoctets" => t('all','Download'),
                    "acctterminatecause" => t('all','Termination'),
                 );
    $colspan = count($cols);
    $half_colspan = intval($colspan / 2);

    $orderBy = (array_key_exists('orderBy', $_GET) && !empty($_GET['orderBy']) &&
                in_array($_GET['orderBy'], array_keys($cols)))
             ? $_GET['orderBy'] : array_keys($cols)[0];

    $orderType = (array_key_exists('orderType', $_GET) && !empty($_GET['orderType']) &&
                  preg_match(ORDER_TYPE_REGEX, $_GET['orderType']) !== false)
               ? strtolower($_GET['orderType']) : "asc";

    // init logging variables
    $log = "visited page: ";
    $logQuery = "performed query for user [$username] and start date [$startdate] and end date [$enddate] on page: ";
    $logDebugSQL = "";


    // print HTML prologue
    $extra_js = array(
        "static/js/ajax.js",
        "static/js/ajaxGeneric.js",
        "static/js/pages_common.js",
    );

    $title = t('Intro','acctdate.php');
    $help = t('helpPage','acctdate');

    print_html_prologue($title, $langCode, array(), $extra_js);

    print_title_and_help($title, $help);

    // we can only use the $dbSocket after we have included 'library/opendb.php' which initialzes the connection and the $dbSocket object
    include('library/opendb.php');
    include('include/management/pages_common.php');

    $sql_WHERE = array();
    $partial_query_params = array();

    if (!empty($startdate)) {
        $sql_WHERE[] = sprintf("AcctStartTime > '%s'", $dbSocket->escapeSimple($startdate));
        $partial_query_params[] = sprintf("startdate=%s", $startdate);
    }

    if (!empty($enddate)) {
        $sql_WHERE[] = sprintf("AcctStartTime < '%s'", $dbSocket->escapeSimple($enddate));
        $partial_query_params[] = sprintf("enddate=%s", $enddate);
    }

    if (!empty($username)) {
        $sql_WHERE[] = sprintf("username='%s'", $dbSocket->escapeSimple($username));
        $partial_query_params[] = sprintf("username=%s", urlencode($username_enc));

        // setup php session variables for exporting
        $_SESSION['reportTable'] = $configValues['CONFIG_DB_TBL_RADACCT'];
        $_SESSION['reportQuery'] = (count($sql_WHERE) > 0) ? " WHERE " . implode(" AND ", $sql_WHERE) : "";
        $_SESSION['reportType'] = "accountingGeneric";


        $sql = sprintf("SELECT COUNT(radacctid) FROM %s", $configValues['CONFIG_DB_TBL_RADACCT']);
        if (count($sql_WHERE) > 0) {
            $sql .= " WHERE " . implode(" AND ", $sql_WHERE);
        }
        $res = $dbSocket->query($sql);
        $logDebugSQL .= "$sql;\n";

        $numrows = $res->fetchrow()[0];

        if ($numrows > 0) {
            /* START - Related to pages_numbering.php */

            // when $numrows is set, $maxPage is calculated inside this include file
            include('include/management/pages_numbering.php');    // must be included after opendb because it needs to read
                                                                  // the CONFIG_IFACE_TABLES_LISTING variable from the config file

            // here we decide if page numbers should be shown
            $drawNumberLinks = strtolower($configValues['CONFIG_IFACE_TABLES_LISTING_NUM']) == "yes" && $maxPage > 1;

            $sql = sprintf("SELECT ra.RadAcctId, dhs.name AS hotspot, ra.username, ra.FramedIPAddress, ra.AcctStartTime,
                                   ra.AcctStopTime, ra.AcctSessionTime, ra.AcctInputOctets, ra.AcctOutputOctets,
                                   ra.AcctTerminateCause, ra.NASIPAddress
                              FROM %s AS ra LEFT JOIN %s AS dhs ON ra.calledstationid=dhs.mac",
                           $configValues['CONFIG_DB_TBL_RADACCT'], $configValues['CONFIG_DB_TBL_DALOHOTSPOTS']);
            if (count($sql_WHERE) > 0) {
                $sql .= " WHERE " . implode(" AND ", $sql_WHERE);
            }

            $sql .= sprintf(" ORDER BY %s %s LIMIT %s, %s", $orderBy, $orderType, $offset, $rowsPerPage);
            $res = $dbSocket->query($sql);
            $logDebugSQL .= "$sql;\n";

            $per_page_numrows = $res->numRows();

            $partial_query_string = (count($partial_query_params) > 0)
                                  ? ("&" . implode("&", $partial_query_params)) : "";

            $descriptors = array();

            $params = array(
                                'num_rows' => $numrows,
                                'rows_per_page' => $rowsPerPage,
                                'page_num' => $pageNum,
                                'order_by' => $orderBy,
                                'order_type' => $orderType,
                                'partial_query_string' => $partial_query_string,
                            );
            $descriptors['center'] = array( 'draw' => $drawNumberLinks, 'params' => $params );


            $descriptors['end'] = array();
            $descriptors['end'][] = array(
                                            'onclick' => "location.href='include/management/fileExport.php?reportFormat=csv'",
                                            'label' => 'CSV Export',
                                            'class' => 'btn-light',
                                         );
            print_table_prologue($descriptors);

            // print table top
            print_table_top();

            // second line of table header
            printTableHead($cols, $orderBy, $orderType, $partial_query_string);

            // closes table header, opens table body
            print_table_middle();

            // table content
            $count = 0;
            while ($row = $res->fetchRow()) {
                $rowlen = count($row);

                // escape row elements
                for ($i = 0; $i < $rowlen; $i++) {
                    $row[$i] = htmlspecialchars($row[$i], ENT_QUOTES, 'UTF-8');
                }

                list($radAcctId, $hotspot, $username, $framedIPAddress, $acctStartTime, $acctStopTime,
                     $acctSessionTime, $acctInputOctets, $acctOutputOctets, $acctTerminateCause, $nasIPAddress) = $row;

                $acctSessionTime = intval($acctSessionTime);
                if ($acctSessionTime === 0) {
                    $acctSessionTime = "(n/d)";
                } else {
                    $acctSessionTime = time2str($acctSessionTime);
                }

                $acctInputOctets = toxbyte($acctInputOctets);
                $acctOutputOctets = toxbyte($acctOutputOctets);

                if (!empty($hotspot)) {
                    $ajax_id = "divContainerHotspotInfo_" . $count;
                    $param = sprintf('hotspot=%s', urlencode($hotspot));
                    $onclick = "ajaxGeneric('library/ajax/hotspot_info.php','retHotspotGeneralStat','$ajax_id','$param')";
                    $tooltip1 = array(
                                        'subject' => $hotspot,
                                        'onclick' => $onclick,
                                        'ajax_id' => $ajax_id,
                                        'actions' => array(),
                                     );
                    $tooltip1['actions'][] = array( 'href' => sprintf('mng-hs-edit.php?name=%s', urlencode($hotspot), ), 'label' => t('Tooltip','HotspotEdit'), );
                    $tooltip1['actions'][] = array( 'href' => 'acct-hotspot-compare.php', 'label' => t('all','Compare'), );

                    $tooltip1 = get_tooltip_list_str($tooltip1);
                } else {
                    $tooltip1 = "(n/a)";
                }

                $ajax_id = "divContainerUserInfo_" . $count;
                $param = sprintf('username=%s', urlencode($username));
                $onclick = "ajaxGeneric('library/ajax/user_info.php','retBandwidthInfo','$ajax_id','$param')";
                $tooltip2 = array(
                                    'subject' => $username,
                                    'onclick' => $onclick,
                                    'ajax_id' => $ajax_id,
                                    'actions' => array(),
                                 );
                $tooltip2['actions'][] = array( 'href' => sprintf('mng-edit.php?username=%s', urlencode($username), ), 'label' => t('Tooltip','UserEdit'), );

                $tooltip2 = get_tooltip_list_str($tooltip2);

                // define table row
                $table_row = array( $radAcctId, $tooltip1, $nasIPAddress, $tooltip2, $framedIPAddress, $acctStartTime,
                                    $acctStopTime, $acctSessionTime, $acctInputOctets, $acctOutputOctets, $acctTerminateCause);

                // print table row
                print_table_row($table_row);

                $count++;
            }

            // close tbody,
            // print tfoot
            // and close table + form (if any)
            $table_foot = array(
                                    'num_rows' => $numrows,
                                    'rows_per_page' => $per_page_numrows,
                                    'colspan' => $colspan,
                                    'multiple_pages' => $drawNumberLinks
                               );
            $descriptor = array( 'table_foot' => $table_foot );

            print_table_bottom($descriptor);

            // get and print "links"
            $links = setupLinks_str($pageNum, $maxPage, $orderBy, $orderType, $partial_query_string);
            printLinks($links, $drawNumberLinks);

        } else {
            $failureMsg = "Nothing to display";
        }

        // main accordion
        echo '<div class="accordion m-2" id="accordion-parent">';
        include_once('include/management/userReports.php');

        userPlanInformation($username, 1);

        // userSubscriptionAnalysis with argument set to 1 for drawing the table
        userSubscriptionAnalysis($username, 1);

        // userConnectionStatus (same as above)
        userConnectionStatus($username, 1);

        echo '</div>';

    } else {
        $failureMsg = "Please specify a valid username";
    }

    include_once("include/management/actionMessages.php");

    include('library/closedb.php');

    include('include/config/logging.php');
    print_footer_and_html_epilogue();
?>
