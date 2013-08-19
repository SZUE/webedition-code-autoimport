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
include_once (WE_INCLUDES_PATH . 'we_tools/weSearch/conf/define.conf.php');

$toolTables = array();
$toolTables['tblsearchtool'] = SUCHE_TABLE;
// additional table can be specified here
// $toolTables['tool_table_TestTool_2'] = '';
// $toolTables['tool_table_TestTool_3'] = '';
