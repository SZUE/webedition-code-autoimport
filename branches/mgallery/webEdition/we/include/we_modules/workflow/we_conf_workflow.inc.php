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
define('WE_WORKFLOW_MODULE_DIR', WE_MODULES_DIR . 'workflow/');
define('WE_JS_WORKFLOW_MODULE_DIR', WE_JS_MODULES_DIR . 'workflow/');
define('WE_WORKFLOW_MODULE_PATH', WE_MODULES_PATH . 'workflow/');

define('WORKFLOW_TABLE', TBL_PREFIX . 'tblWorkflowDef');
define('WORKFLOW_DOC_TABLE', TBL_PREFIX . 'tblWorkflowDoc');
define('WORKFLOW_DOC_STEP_TABLE', TBL_PREFIX . 'tblWorkflowDocStep');
define('WORKFLOW_DOC_TASK_TABLE', TBL_PREFIX . 'tblWorkflowDocTask');
define('WORKFLOW_LOG_TABLE', TBL_PREFIX . 'tblWorkflowLog');
define('WORKFLOW_STEP_TABLE', TBL_PREFIX . 'tblWorkflowStep');
define('WORKFLOW_TASK_TABLE', TBL_PREFIX . 'tblWorkflowTask');

we_base_request::registerTables(array(
	'WORKFLOW_TABLE' => WORKFLOW_TABLE,
	'WORKFLOW_DOC_TABLE' => WORKFLOW_DOC_TABLE,
	'WORKFLOW_DOC_STEP_TABLE' => WORKFLOW_DOC_STEP_TABLE,
	'WORKFLOW_DOC_TASK_TABLE' => WORKFLOW_DOC_TASK_TABLE,
	'WORKFLOW_LOG_TABLE' => WORKFLOW_LOG_TABLE,
	'WORKFLOW_STEP_TABLE' => WORKFLOW_STEP_TABLE,
	'WORKFLOW_TASK_TABLE' => WORKFLOW_TASK_TABLE
));
