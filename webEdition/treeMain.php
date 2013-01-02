<?php

/**
 * webEdition CMS
 *
 * $Rev: 4689 $
 * $Author: mokraemer $
 * $Date: 2012-07-08 04:53:51 +0200 (Sun, 08 Jul 2012) $
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
if(isset($_REQUEST['code'])){
	exit('REQUEST[\'code\'] is forbidden!');
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();

$Tree = new weMainTree('webEdition.php', 'top', 'top.resize.left.tree', 'top.load');

print $Tree->getHTMLContruct('if(top.treeResized){top.treeResized();}');
