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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
we_html_tools::protect();

$cmd = we_base_request::_(we_base_request::RAW, 'cmd', '');
$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0), 1);

$wf_select = we_base_request::_(we_base_request::INT, 'wf_select', 0);
$wf_text = we_base_request::_(we_base_request::STRING, 'wf_text', '');

###### init document #########
$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');


echo we_html_tools::getHtmlTop();

switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0)){
	case 'workflow_isIn':
		include(WE_WORKFLOW_MODULE_PATH . 'we_in_workflow.inc.php');
		break;
	case 'workflow_pass':
		include(WE_WORKFLOW_MODULE_PATH . 'we_pass_workflow.inc.php');
		break;
	case 'workflow_decline':
		include(WE_WORKFLOW_MODULE_PATH . 'we_decline_workflow.inc.php');
		break;
}
