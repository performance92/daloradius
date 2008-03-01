<?php

    include ("library/checklogin.php");
    $operator = $_SESSION['operator_user'];

	include('library/check_operator_perm.php');

	include_once('library/config_read.php');
    $log = "visited page: ";
    include('include/config/logging.php');


    include ("library/config_read.php");

    if (isset($_REQUEST['submit'])) {

		if (isset($_REQUEST['config_dbengine']))
			$configValues['CONFIG_DB_ENGINE'] = $_REQUEST['config_dbengine'];
	
		if (isset($_REQUEST['config_dbhost']))
			$configValues['CONFIG_DB_HOST'] = $_REQUEST['config_dbhost'];

		if (isset($_REQUEST['config_dbuser']))
			$configValues['CONFIG_DB_USER'] = $_REQUEST['config_dbuser'];

		if (isset($_REQUEST['config_dbpass']))
			$configValues['CONFIG_DB_PASS'] = $_REQUEST['config_dbpass'];

		if (isset($_REQUEST['config_dbname']))
			$configValues['CONFIG_DB_NAME'] = $_REQUEST['config_dbname'];


		if (isset($_REQUEST['config_dbtbl_radcheck']))
			$configValues['CONFIG_DB_TBL_RADCHECK'] = $_REQUEST['config_dbtbl_radcheck'];

		if (isset($_REQUEST['config_dbtbl_radcheck']))
			$configValues['CONFIG_DB_TBL_RADREPLY'] = $_REQUEST['config_dbtbl_radreply'];

		if (isset($_REQUEST['config_dbtbl_radcheck']))
			$configValues['CONFIG_DB_TBL_RADGROUPCHECK'] = $_REQUEST['config_dbtbl_radgroupcheck'];

		if (isset($_REQUEST['config_dbtbl_radcheck']))
			$configValues['CONFIG_DB_TBL_RADGROUPREPLY'] = $_REQUEST['config_dbtbl_radgroupreply'];

		if (isset($_REQUEST['config_dbtbl_usergroup']))
			$configValues['CONFIG_DB_TBL_RADUSERGROUP'] = $_REQUEST['config_dbtbl_usergroup'];

		if (isset($_REQUEST['config_dbtbl_radacct']))
			$configValues['CONFIG_DB_TBL_RADACCT'] = $_REQUEST['config_dbtbl_radacct'];

		if (isset($_REQUEST['config_dbtbl_operators']))
			$configValues['CONFIG_DB_TBL_DALOOPERATOR'] = $_REQUEST['config_dbtbl_operators'];

		if (isset($_REQUEST['config_dbtbl_rates']))
			$configValues['CONFIG_DB_TBL_DALORATES'] = $_REQUEST['config_dbtbl_rates'];

		if (isset($_REQUEST['config_dbtbl_hotspots']))
			$configValues['CONFIG_DB_TBL_DALOHOTSPOTS'] = $_REQUEST['config_dbtbl_hotspots'];

			
			
		// this should probably move to some other page at some point
		if (isset($_REQUEST['config_db_pass_encrypt']))
			$configValues['CONFIG_DB_PASSWORD_ENCRYPTION'] = $_REQUEST['config_db_pass_encrypt'];
			
        include ("library/config_write.php");
    }	

	
?>		


<?php
        include_once ("library/tabber/tab-layout.php");
?>
		
<?php

    include ("menu-config.php");

?>		
			
		<div id="contentnorightbar">
		
				<h2 id="Intro"><a href="#" onclick="javascript:toggleShowDiv('helpPage')"><?php echo $l['Intro']['configdb.php']?>
				<h144>+</h144></a></h2>

                <div id="helpPage" style="display:none;visibility:visible" >
					<?php echo $l['helpPage']['configdb'] ?>
					<br/>
				</div>
				<br/>

				<form name="dbsettings" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<div class="tabber">

     <div class="tabbertab" title="<?php echo $l['title']['Settings']; ?>">
        <br/>


        <fieldset>

                <h302><?php echo $l['title']['Settings']; ?></h302>
		<br/>

		<ul>

                <li class='fieldset'>
                <label for='config_dbengine' class='form'><?php echo $l['all']['DBEngine']?></label>
		<select class='form' name="config_dbengine">
			<option value="<?php echo $configValues['CONFIG_DB_ENGINE'] ?>"> <?php echo $configValues['CONFIG_DB_ENGINE'] ?> </option>
			<option value=""></option>
			<option value="mysql"> MySQL </option>
			<option value="pgsql"> PostgreSQL </option>
			<option value="odbc"> ODBC </option>
			<option value="mssql"> MsSQL </option>
			<option value="mysqli"> MySQLi </option>
			<option value="msql"> MsQL </option>
			<option value="sybase"> Sybase </option>
			<option value="sqlite"> Sqlite </option>
			<option value="oci8"> Oci8  </option>
			<option value="ibase"> ibase </option>
			<option value="fbsql"> fbsql </option>
			<option value="informix"> informix </option>
		</select>
		</li>


		<li class='fieldset'>
		<label for='config_dbhost' class='form'><?php echo $l['all']['DatabaseHostname'] ?></label>
		<input value="<?php echo $configValues['CONFIG_DB_HOST'] ?>" name="config_dbhost"/>
		</li>

		<li class='fieldset'>
		<label for='config_dbuser' class='form'><?php echo $l['all']['DatabaseUser'] ?></label>
		<input value="<?php echo $configValues['CONFIG_DB_USER'] ?>" name="config_dbuser" />
		</li>

		<li class='fieldset'>
		<label for='config_dbpass' class='form'><?php echo $l['all']['DatabasePass'] ?></label>
		<input value="<?php echo $configValues['CONFIG_DB_PASS'] ?>" name="config_dbpass" />
		</li>

		<li class='fieldset'>
		<label for='db_name' class='form'><?php echo  $l['all']['DatabaseName'] ?></label>
		<input value="<?php echo $configValues['CONFIG_DB_NAME'] ?>" name="config_dbname" />
		</li>

                <li class='fieldset'>
                <br/>
                <hr><br/>
                <input type='submit' name='submit' value='<?php echo $l['buttons']['apply'] ?>' class='button' />
                </li>

                </ul>
	
	</fieldset>

	</div>

     <div class="tabbertab" title="<?php echo $l['title']['DatabaseTables']; ?>">
        <br/>

		<fieldset>

                <h302><?php echo $l['title']['DatabaseTables']; ?></h302>
		<br/>

		<ul>

		<li class='fieldset'>
                <label for='config_dbtbl_radcheck' class='form'><?php echo $l['all']['radcheck']?></label>
		<input value="<?php echo $configValues['CONFIG_DB_TBL_RADCHECK'] ?>" name="config_dbtbl_radcheck"/>
		</li>

                <li class='fieldset'>
                <label for='config_dbtbl_radreply' class='form'><?php echo $l['all']['radreply']?></label>
		<input value="<?php echo $configValues['CONFIG_DB_TBL_RADREPLY'] ?>" name="config_dbtbl_radreply" />
		</li>

                <li class='fieldset'>
                <label for='config_dbtbl_radgroupreply' class='form'><?php echo $l['all']['radgroupreply']?></label>
		<input value="<?php echo $configValues['CONFIG_DB_TBL_RADGROUPREPLY'] ?>" name="config_dbtbl_radgroupreply" />
		</li>

                <li class='fieldset'>
                <label for='config_dbtbl_radgroupcheck' class='form'><?php echo $l['all']['radgroupcheck']?></label>
		<input value="<?php echo $configValues['CONFIG_DB_TBL_RADGROUPCHECK'] ?>" name="config_dbtbl_radgroupcheck" />
		</li>

                <li class='fieldset'>
                <label for='config_dbtbl_usergroup' class='form'><?php echo $l['all']['usergroup']?></label>
		<input value="<?php echo $configValues['CONFIG_DB_TBL_RADUSERGROUP'] ?>" name="config_dbtbl_usergroup" />
		</li>

                <li class='fieldset'>
                <label for='config_dbtbl_radacct' class='form'><?php echo $l['all']['radacct']?></label>
		<input value="<?php echo $configValues['CONFIG_DB_TBL_RADACCT'] ?>" name="config_dbtbl_radacct" />
		</li>

                <li class='fieldset'>
                <label for='config_dbtbl_operators' class='form'><?php echo $l['all']['operators']?></label>
		<input value="<?php echo $configValues['CONFIG_DB_TBL_DALOOPERATOR'] ?>" name="config_dbtbl_operators" />
		</li>

                <li class='fieldset'>
                <label for='config_dbtbl_rates' class='form'><?php echo $l['all']['rates']?></label>
		<input value="<?php echo $configValues['CONFIG_DB_TBL_DALORATES'] ?>" name="config_dbtbl_rates" />
		</li>

                <li class='fieldset'>
                <label for='config_dbtbl_hotspots' class='form'><?php echo $l['all']['hotspots']?></label>
		<input value="<?php echo $configValues['CONFIG_DB_TBL_DALOHOTSPOTS'] ?>" name="config_dbtbl_hotspots" />
		</li>

                <li class='fieldset'>
                <br/>
                <hr><br/>
                <input type='submit' name='submit' value='<?php echo $l['buttons']['apply'] ?>' class='button' />
                </li>

                </ul>

</table>

</div>

     <div class="tabbertab" title="<?php echo $l['title']['AdvancedSettings']; ?>">
        <br/>

	<fieldset>

                <h302> <?php echo $l['title']['AdvancedSettings']; ?> </h302>
		<br/>

		<ul>

                <li class='fieldset'>
                <label for='' class='form'><?php echo $l['all']['DBPasswordEncryption']?></label>
		<select class='form' name="config_db_pass_encrypt">
			<option value="<?php echo $configValues['CONFIG_DB_PASSWORD_ENCRYPTION'] ?>"> <?php echo $configValues['CONFIG_DB_PASSWORD_ENCRYPTION'] ?> </option>
			<option value=""></option>
			<option value="cleartext"> cleartext </option>
			<option value="crypt"> unix crypt </option>
			<option value="md5"> md5 </option>
		</select>
		</li>

                <li class='fieldset'>
                <br/>
                <hr><br/>
                <input type='submit' name='submit' value='<?php echo $l['buttons']['apply'] ?>' class='button' />
                </li>

                </ul>

	</fieldset>

	</div>
</div>


				</form>

	
				<br/><br/>






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