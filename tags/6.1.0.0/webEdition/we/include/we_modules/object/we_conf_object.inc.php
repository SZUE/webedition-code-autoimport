<?php
/**
 * webEdition CMS
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


define("OBJECT_TABLE", TBL_PREFIX . "tblObject");
define("OBJECT_FILES_TABLE", TBL_PREFIX . "tblObjectFiles");
define("OBJECT_X_TABLE", TBL_PREFIX . "tblObject_");

define("WE_OBJECT_MODULE_DIR", $_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_modules/object/");
define("WE_OBJECT_MODULE_PATH","/webEdition/we/include/we_modules/object/");

// Number of displayed objects in the left navigation
define("OBJECT_FILES_TREE_COUNT", 20);

?>