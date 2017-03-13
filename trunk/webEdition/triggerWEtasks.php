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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
if(!defined('NO_SESS')){
	define('NO_SESS', 1);
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
//remove all parameters in case some functions might hear to them
unset($_REQUEST, $_GET, $_POST);
define('SCHEDULED_BY_CRON', 1);
if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
	we_schedpro::trigger_schedule();
}
if(defined('WORKFLOW_TABLE')){
	echo we_workflow_utility::forceOverdueDocuments();
}
//clean old sessions
we_base_sessionHandler::cleanSessions();

$tooltasks = we_tool_lookup::getExternTriggeredTasks();
foreach($tooltasks as $task){
	include($task);
}
?>OK