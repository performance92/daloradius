<?php
/*********************************************************************
* Name: userinfo.php
* Author: Liran tal <liran.tal@gmail.com>
* 
* This file extends the user management pages (new user, batch add
* users, edit user, quick add user and possibly others) by adding
* a section for user information
*
*********************************************************************/

echo "

<table border='2' class='table1'>
                                        <thead>
                                                        <tr>
                                                        <th colspan='2'> User Info </th>
                                                        </tr>
                                        </thead>
<tr><td>
                                                <b>First name</b>
</td><td>
                                                <input value='"; if (isset($ui_firstname)) echo $ui_firstname; echo "' name='firstname' tabindex=300 />
                                                </font>
</td></tr>
<tr><td>
                                                <b>Last name</b>
</td><td>
                                                <input value='"; if (isset($ui_lastname)) echo $ui_lastname; echo "' name='lastname' tabindex=301 />
                                                </font>
</td></tr>
<tr><td>
                                                <b>Email</b>
</td><td>
                                                <input value='"; if (isset($ui_email)) echo $ui_email; echo "' name='email' tabindex=302 />
                                                </font>
</td></tr>
<tr><td>
                                                <b>Department</b>
</td><td>
                                                <input value='"; if (isset($ui_department)) echo $ui_department; echo "' name='department' tabindex=303 />
                                                </font>
</td></tr>
<tr><td>
                                                <b>Company</b>
</td><td>
                                                <input value='"; if (isset($ui_company)) echo $ui_company; echo "' name='company' tabindex=304 />
                                                </font>
</td></tr>
<tr><td>
                                                <b>Work phone</b>
</td><td>
                                                <input value='"; if (isset($ui_workphone)) echo $ui_workphone; echo "' name='workphone' tabindex=305 />
                                                </font>
</td></tr>
<tr><td>
                                                <b>Home phone</b>
</td><td>
                                                <input value='"; if (isset($ui_homephone)) echo $ui_homephone; echo "' name='homephone' tabindex=306 />
                                                </font>
</td></tr>
<tr><td>
                                                <b>Mobile phone</b>
</td><td>
                                                <input value='"; if (isset($ui_mobilephone)) echo $ui_mobilephone; echo "' name='mobilephone' tabindex=307 />
                                                </font>
</td></tr>
<tr><td>
                                                <b>Notes</b>
</td><td>
                                                <input value='"; if (isset($ui_notes)) echo $ui_notes; echo "' name='notes' tabindex=308 />
                                                </font>
</td></tr>
</table>

";



?>
