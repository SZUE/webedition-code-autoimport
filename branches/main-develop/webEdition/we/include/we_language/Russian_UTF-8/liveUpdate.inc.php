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
 * @package    webEdition_language
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
$l_liveUpdate = array(
		'tabs' => array(
				'register' => 'Registration', // TRANSLATE
				'update' => 'Update', // TRANSLATE
				'upgrade' => 'Update webEdition 5', // TRANSLATE
				'modules' => 'Modules', // TRANSLATE
				'updatelog' => 'Log', // TRANSLATE
				'languages' => 'Languages', // TRANSLATE
				'connect' => 'Test connection', // TRANSLATE
				'nextVersion' => 'Next version', // TRANSLATE
				'beta' => 'Pre-Release Versions', // TRANSLATE
		),
		'register' => array(
				'headline' => 'register webEdition',
				'description' => 'register webEdition and discover its complete functionality.',
		),
		'update' => array(
				'headline' => 'Look for new version', // TRANSLATE
				'actualVersion' => 'Running version', // TRANSLATE
				'lastUpdate' => 'Last installation', // TRANSLATE
				'neverUpdated' => '-', // TRANSLATE
				'lookForUpdate' => 'Look for update', // TRANSLATE
		),
		'upgrade' => array(
				'headline' => 'Update to webEdition 5', // TRANSLATE
				'actualVersion' => 'Running version', // TRANSLATE
				'lookForUpdate' => 'Install webEdition 5', // TRANSLATE
		),
		'modules' => array(
				'headline' => 'Installation of modules', // TRANSLATE
				'installedModules' => 'Installed modules', // TRANSLATE
				'noModulesInstalled' => 'There are no modules installed', // TRANSLATE
				'showModules' => 'Select modules', // TRANSLATE
		),
		'languages' => array(
				'headline' => 'Installation of languages', // TRANSLATE
				'installedLngs' => 'The following languages are installed', // TRANSLATE
				'showLanguages' => 'Install more languages', // TRANSLATE
				'deleteSelectedLanguages' => 'Delete selected languages', // TRANSLATE
				'systemLanguage' => 'system language', // TRANSLATE
				'usedLanguage' => 'used language', // TRANSLATE
				'languagesDeleted' => 'The following languages were deleted', // TRANSLATE
				'languagesNotDeleted' => 'The following languages could not be deleted', // TRANSLATE
		),
		'connect' => array(
				'headline' => 'Check connection to update server', // TRANSLATE
				'description' => 'If there are problems running the update, you can test here, if it is possible to open a connection to the webEdition update server.', // TRANSLATE
				'connectionSuccess' => 'A connection to the update server could be established.', // TRANSLATE
				'connectionSuccessError' => 'A connection to the update server could be established, but there was an error on the server.<br />', // TRANSLATE
				'connectionError' => 'It is not possible to connect to the update server at the moment',
				'connectionErrorJs' => 'It is not possible to establish a connection to the update server',
				'connectionInfo' => "Connection informations", // TRANSLATE
				'availableConnectionTypes' => "Available connection types", // TRANSLATE
				'connectionType' => "Used connection type", // TRANSLATE
				'proxyHost' => "Proxy host", // TRANSLATE
				'proxyPort' => "Proxy port", // TRANSLATE
				'hostName' => "Hostname", // TRANSLATE
				'addressResolution' => "Address resolution", // TRANSLATE
				'updateServer' => "Update server", // TRANSLATE
				'ipResolutionTest' => "IP resolution test", // TRANSLATE
				'dnsResolutionTest' => "DNS resolution test", // TRANSLATE
				'succeeded' => "succeeded", // TRANSLATE
				'failed' => "failed", // TRANSLATE
				'ipAddresses' => "IP address(es)", // TRANSLATE
		),
		'state' => array(
				'headline' => 'Message from update server', // TRANSLATE
				'descriptionTrue' => 'The update server has completed this request.', // TRANSLATE
				'descriptionError' => 'The update server could not fullfill this request. The following error occured.', // TRANSLATE
		),
		'updatelog' => array(
				'headline' => 'Update Log', // TRANSLATE
				'logIsEmpty' => 'The update log is empty', // TRANSLATE
				'date' => 'Date / time', // TRANSLATE
				'action' => 'Action', // TRANSLATE
				'version' => 'Version', // TRANSLATE
				'entriesTotal' => 'Entries total', // TRANSLATE
				'page' => 'page', // TRANSLATE
				'noEntriesMatchFilter' => 'There are not entries matching the selected filter.', // TRANSLATE
				'legendMessages' => 'Messages', // TRANSLATE
				'legendNotices' => 'Notices', // TRANSLATE
				'legendErrors' => 'Errors', // TRANSLATE
				'confirmDelete' => 'Do you really want to delete all selected entries?', // TRANSLATE
		),
		'beta' => array(
				'headline' => 'Shall Pre-Release Versions be included in the search?', // TRANSLATE
				'lookForUpdate' => 'search for Pre-Release Versions', // TRANSLATE
				'warning' => '<b>Pre-Release versions,<br/> such as <u>nightly Builds, Alpha-, Beta- und RC-Versions</u>,<br/> should never be used in produktion sites!</b><br/><br/>They are provided for testing purposes only and aim at easing <br/>the search for severe erros before an official version is published.', // TRANSLATE
		),
);
