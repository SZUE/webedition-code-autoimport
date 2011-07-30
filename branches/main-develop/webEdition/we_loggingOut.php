<?php
/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we.inc.php");

if (isset($_SESSION)){
    while(list($name, $val) = each($_SESSION)){
        unset($_SESSION[$name]);
    }
}
$_SESSION = array();
header("location: " . WEBEDITION_DIR ."index.php");


?>
<html>
<head>
<title>webEdition</title>
</head>
<body>
<?php echo g_l('global',"[redirect_to_login_failed]"). '<a href="' . WEBEDITION_DIR .'index.php">' . g_l('global',"[redirect_to_login_name]") . '</a>'; ?>
</body>
</html>