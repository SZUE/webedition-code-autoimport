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
we_html_tools::protect();

$cmd = weRequest('raw', "cmd", "");
$we_transaction = weRequest('transaction', 'we_cmd', weRequest('transaction', "we_transaction", 0), 1);

$wf_select = weRequest('raw', "wf_select", "");
$wf_text = weRequest('raw', "wf_text", "");

###### init document #########
$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');


echo we_html_tools::getHtmlTop();

switch(weRequest('string', 'we_cmd', '', 0)){
	case "in_workflow":
		include(WE_WORKFLOW_MODULE_PATH . "we_in_workflow.inc.php");
		break;
	case "pass":
		include(WE_WORKFLOW_MODULE_PATH . "we_pass_workflow.inc.php");
		break;
	case "decline":
		include(WE_WORKFLOW_MODULE_PATH . "we_decline_workflow.inc.php");
		break;
}
