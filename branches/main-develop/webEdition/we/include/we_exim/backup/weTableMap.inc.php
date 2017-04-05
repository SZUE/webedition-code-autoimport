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
return [
	'core' => [
		strtolower(stripTblPrefix(FILE_TABLE)) => FILE_TABLE,
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
	],
	'versions' => [
		strtolower(stripTblPrefix(VERSIONS_TABLE)) => VERSIONS_TABLE,
		strtolower(stripTblPrefix(VERSIONSLOG_TABLE)) => VERSIONSLOG_TABLE
	],
	'settings' => [
		strtolower(stripTblPrefix(PREFS_TABLE)) => PREFS_TABLE,
		strtolower(stripTblPrefix(RECIPIENTS_TABLE)) => RECIPIENTS_TABLE,
		strtolower(stripTblPrefix(VALIDATION_SERVICES_TABLE)) => VALIDATION_SERVICES_TABLE,
		strtolower(stripTblPrefix(SETTINGS_TABLE)) => SETTINGS_TABLE,
	],
	'user' => [
		strtolower(stripTblPrefix(USER_TABLE)) => USER_TABLE
	],
	'temporary' => [
		strtolower(stripTblPrefix(TEMPORARY_DOC_TABLE)) => TEMPORARY_DOC_TABLE
	],
	'history' => [
		strtolower(stripTblPrefix(HISTORY_TABLE)) => HISTORY_TABLE
	],
	'backup' => [
		'tblbackup' => addTblPrefix('tblbackup')
	],
	'configuration' => [],
	'object' => (defined('OBJECT_TABLE') ? [
	strtolower(stripTblPrefix(OBJECT_TABLE)) => OBJECT_TABLE,
	strtolower(stripTblPrefix(OBJECT_FILES_TABLE)) => OBJECT_FILES_TABLE,
	strtolower(stripTblPrefix(OBJECT_X_TABLE)) => OBJECT_X_TABLE,
	strtolower(stripTblPrefix(OBJECTLINK_TABLE)) => OBJECTLINK_TABLE,
	] :
	[]
	),
	'customer' => (defined('CUSTOMER_TABLE') ? [
	strtolower(stripTblPrefix(CUSTOMER_TABLE)) => CUSTOMER_TABLE,
	strtolower(stripTblPrefix(CUSTOMER_FILTER_TABLE)) => CUSTOMER_FILTER_TABLE,
	strtolower(stripTblPrefix(CUSTOMER_AUTOLOGIN_TABLE)) => CUSTOMER_AUTOLOGIN_TABLE
	] :
	[]
	),
	'shop' => (defined('SHOP_ORDER_TABLE') ? [
	strtolower(stripTblPrefix(SHOP_TABLE)) => SHOP_TABLE,
	strtolower(stripTblPrefix(WE_SHOP_VAT_TABLE)) => WE_SHOP_VAT_TABLE,
	strtolower(SHOP_ORDER_TABLE) => SHOP_ORDER_TABLE,
	strtolower(SHOP_ORDER_DATES_TABLE) => SHOP_ORDER_DATES_TABLE,
	strtolower(SHOP_ORDER_DOCUMENT_TABLE) => SHOP_ORDER_DOCUMENT_TABLE,
	strtolower(SHOP_ORDER_ITEM_TABLE) => SHOP_ORDER_ITEM_TABLE
	] :
	[]
	),
	'workflow' => (defined('WORKFLOW_TABLE') ? [
	strtolower(stripTblPrefix(WORKFLOW_TABLE)) => WORKFLOW_TABLE,
	strtolower(stripTblPrefix(WORKFLOW_STEP_TABLE)) => WORKFLOW_STEP_TABLE,
	strtolower(stripTblPrefix(WORKFLOW_TASK_TABLE)) => WORKFLOW_TASK_TABLE,
	strtolower(stripTblPrefix(WORKFLOW_DOC_TABLE)) => WORKFLOW_DOC_TABLE,
	strtolower(stripTblPrefix(WORKFLOW_DOC_STEP_TABLE)) => WORKFLOW_DOC_STEP_TABLE,
	strtolower(stripTblPrefix(WORKFLOW_DOC_TASK_TABLE)) => WORKFLOW_DOC_TASK_TABLE,
	strtolower(stripTblPrefix(WORKFLOW_LOG_TABLE)) => WORKFLOW_LOG_TABLE
	] :
	[]
	),
	'todo' => [],
	'newsletter' => (defined('NEWSLETTER_TABLE') ? [
	strtolower(stripTblPrefix(NEWSLETTER_TABLE)) => NEWSLETTER_TABLE,
	strtolower(stripTblPrefix(NEWSLETTER_GROUP_TABLE)) => NEWSLETTER_GROUP_TABLE,
	strtolower(stripTblPrefix(NEWSLETTER_BLOCK_TABLE)) => NEWSLETTER_BLOCK_TABLE,
	strtolower(stripTblPrefix(NEWSLETTER_LOG_TABLE)) => NEWSLETTER_LOG_TABLE,
	strtolower(stripTblPrefix(NEWSLETTER_CONFIRM_TABLE)) => NEWSLETTER_CONFIRM_TABLE
	] :
	[]
	),
	'banner' => (defined('BANNER_TABLE') ? [
	strtolower(stripTblPrefix(BANNER_TABLE)) => BANNER_TABLE,
	strtolower(stripTblPrefix(BANNER_CLICKS_TABLE)) => BANNER_CLICKS_TABLE,
	strtolower(stripTblPrefix(BANNER_VIEWS_TABLE)) => BANNER_VIEWS_TABLE
	] :
	[]
	),
	'schedule' => (we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) ? [
	strtolower(stripTblPrefix(SCHEDULE_TABLE)) => SCHEDULE_TABLE
	] :
	[]
	),
	'export' => (we_base_moduleInfo::isActive(we_base_moduleInfo::EXPORT) ? [
	strtolower(stripTblPrefix(EXPORT_TABLE)) => EXPORT_TABLE
	] :
	[]
	),
	'voting' => (defined('VOTING_TABLE') ? [
	strtolower(stripTblPrefix(VOTING_TABLE)) => VOTING_TABLE,
	strtolower(stripTblPrefix(VOTING_LOG_TABLE)) => VOTING_LOG_TABLE
	] :
	[]
	),
	'glossary' => (defined('GLOSSARY_TABLE') ? [
	strtolower(stripTblPrefix(GLOSSARY_TABLE)) => GLOSSARY_TABLE
	] :
	[]
	),
	'tblsearchtool' => [
		strtolower(stripTblPrefix(SEARCH_TABLE)) => SEARCH_TABLE
	],
];
