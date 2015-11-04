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
$tableMap = array(
	'core' => array(
		strtolower(stripTblPrefix(FILE_TABLE)) => FILE_TABLE,
		strtolower(stripTblPrefix(LINK_TABLE)) => LINK_TABLE,
		strtolower(stripTblPrefix(LANGLINK_TABLE)) => LANGLINK_TABLE,
		strtolower(stripTblPrefix(TEMPLATES_TABLE)) => TEMPLATES_TABLE,
		strtolower(stripTblPrefix(CONTENT_TABLE)) => CONTENT_TABLE,
		strtolower(stripTblPrefix(CATEGORY_TABLE)) => CATEGORY_TABLE,
		strtolower(stripTblPrefix(DOC_TYPES_TABLE)) => DOC_TYPES_TABLE,
		strtolower(stripTblPrefix(THUMBNAILS_TABLE)) => THUMBNAILS_TABLE,
		strtolower(stripTblPrefix(NAVIGATION_TABLE)) => NAVIGATION_TABLE,
		strtolower(stripTblPrefix(NAVIGATION_RULE_TABLE)) => NAVIGATION_RULE_TABLE,
		strtolower(stripTblPrefix(METADATA_TABLE)) => METADATA_TABLE,
		strtolower(stripTblPrefix(VFILE_TABLE)) => VFILE_TABLE,
		strtolower(stripTblPrefix(FILELINK_TABLE)) => FILELINK_TABLE,
	),
	'versions' => array(
		strtolower(stripTblPrefix(VERSIONS_TABLE)) => VERSIONS_TABLE,
		strtolower(stripTblPrefix(VERSIONSLOG_TABLE)) => VERSIONSLOG_TABLE
	),
	'settings' => array(
		strtolower(stripTblPrefix(PREFS_TABLE)) => PREFS_TABLE,
		strtolower(stripTblPrefix(RECIPIENTS_TABLE)) => RECIPIENTS_TABLE,
		strtolower(stripTblPrefix(VALIDATION_SERVICES_TABLE)) => VALIDATION_SERVICES_TABLE,
		strtolower(stripTblPrefix(SETTINGS_TABLE)) => SETTINGS_TABLE,
	),
	'user' => array(
		strtolower(stripTblPrefix(USER_TABLE)) => USER_TABLE
	),
	'temporary' => array(
		strtolower(stripTblPrefix(TEMPORARY_DOC_TABLE)) => TEMPORARY_DOC_TABLE
	),
	'history' => array(
		strtolower(stripTblPrefix(HISTORY_TABLE)) => HISTORY_TABLE
	),
	'backup' => array(
		'tblbackup' => addTblPrefix('tblbackup')
	),
	'configuration' => array(
	),
);


if(defined('OBJECT_TABLE')){
	$tableMap['object'] = array(
		strtolower(stripTblPrefix(OBJECT_TABLE)) => OBJECT_TABLE,
		strtolower(stripTblPrefix(OBJECT_FILES_TABLE)) => OBJECT_FILES_TABLE,
		strtolower(stripTblPrefix(OBJECT_X_TABLE)) => OBJECT_X_TABLE,
		strtolower(stripTblPrefix(OBJECTLINK_TABLE)) => OBJECTLINK_TABLE,
	);
}

if(defined('CUSTOMER_TABLE')){
	$tableMap['customer'] = array(
		strtolower(stripTblPrefix(CUSTOMER_TABLE)) => CUSTOMER_TABLE,
		strtolower(stripTblPrefix(CUSTOMER_FILTER_TABLE)) => CUSTOMER_FILTER_TABLE,
		strtolower(stripTblPrefix(CUSTOMER_AUTOLOGIN_TABLE)) => CUSTOMER_AUTOLOGIN_TABLE
	);
}

if(defined('SHOP_TABLE')){
	$tableMap['shop'] = array(
		strtolower(stripTblPrefix(SHOP_TABLE)) => SHOP_TABLE,
		strtolower(stripTblPrefix(WE_SHOP_VAT_TABLE)) => WE_SHOP_VAT_TABLE
	);
}

if(defined('WORKFLOW_TABLE')){
	$tableMap['workflow'] = array(
		strtolower(stripTblPrefix(WORKFLOW_TABLE)) => WORKFLOW_TABLE,
		strtolower(stripTblPrefix(WORKFLOW_STEP_TABLE)) => WORKFLOW_STEP_TABLE,
		strtolower(stripTblPrefix(WORKFLOW_TASK_TABLE)) => WORKFLOW_TASK_TABLE,
		strtolower(stripTblPrefix(WORKFLOW_DOC_TABLE)) => WORKFLOW_DOC_TABLE,
		strtolower(stripTblPrefix(WORKFLOW_DOC_STEP_TABLE)) => WORKFLOW_DOC_STEP_TABLE,
		strtolower(stripTblPrefix(WORKFLOW_DOC_TASK_TABLE)) => WORKFLOW_DOC_TASK_TABLE,
		strtolower(stripTblPrefix(WORKFLOW_LOG_TABLE)) => WORKFLOW_LOG_TABLE
	);
}

if(defined('MSG_TODO_TABLE')){
	$tableMap['todo'] = array(
		strtolower(stripTblPrefix(MSG_TODO_TABLE)) => MSG_TODO_TABLE,
		strtolower(stripTblPrefix(MSG_TODOHISTORY_TABLE)) => MSG_TODOHISTORY_TABLE,
		strtolower(stripTblPrefix(MESSAGES_TABLE)) => MESSAGES_TABLE,
		strtolower(stripTblPrefix(MSG_ACCOUNTS_TABLE)) => MSG_ACCOUNTS_TABLE,
		strtolower(stripTblPrefix(MSG_ADDRBOOK_TABLE)) => MSG_ADDRBOOK_TABLE,
		strtolower(stripTblPrefix(MSG_FOLDERS_TABLE)) => MSG_FOLDERS_TABLE,
	);
}

if(defined('NEWSLETTER_TABLE')){
	$tableMap['newsletter'] = array(
		strtolower(stripTblPrefix(NEWSLETTER_TABLE)) => NEWSLETTER_TABLE,
		strtolower(stripTblPrefix(NEWSLETTER_GROUP_TABLE)) => NEWSLETTER_GROUP_TABLE,
		strtolower(stripTblPrefix(NEWSLETTER_BLOCK_TABLE)) => NEWSLETTER_BLOCK_TABLE,
		strtolower(stripTblPrefix(NEWSLETTER_LOG_TABLE)) => NEWSLETTER_LOG_TABLE,
		strtolower(stripTblPrefix(NEWSLETTER_CONFIRM_TABLE)) => NEWSLETTER_CONFIRM_TABLE
	);
}

if(defined('BANNER_TABLE')){
	$tableMap['banner'] = array(
		strtolower(stripTblPrefix(BANNER_TABLE)) => BANNER_TABLE,
		strtolower(stripTblPrefix(BANNER_CLICKS_TABLE)) => BANNER_CLICKS_TABLE,
		strtolower(stripTblPrefix(BANNER_VIEWS_TABLE)) => BANNER_VIEWS_TABLE
	);
}

if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
	$tableMap['schedule'] = array(
		strtolower(stripTblPrefix(SCHEDULE_TABLE)) => SCHEDULE_TABLE
	);
}

if(we_base_moduleInfo::isActive(we_base_moduleInfo::EXPORT)){
	$tableMap['export'] = array(
		strtolower(stripTblPrefix(EXPORT_TABLE)) => EXPORT_TABLE
	);
}

if(defined('VOTING_TABLE')){
	$tableMap['voting'] = array(
		strtolower(stripTblPrefix(VOTING_TABLE)) => VOTING_TABLE,
		strtolower(stripTblPrefix(VOTING_LOG_TABLE)) => VOTING_LOG_TABLE
	);
}

if(defined('GLOSSARY_TABLE')){
	$tableMap['glossary'] = array(
		strtolower(stripTblPrefix(GLOSSARY_TABLE)) => GLOSSARY_TABLE
	);
}