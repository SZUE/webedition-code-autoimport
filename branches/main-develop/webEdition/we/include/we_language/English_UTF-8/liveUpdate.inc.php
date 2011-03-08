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
				'beta' => 'Pre-Release Versions', // TRANSLATE
				'register' => 'Registration',
				'update' => 'Update',
				'upgrade' => 'Update webEdition 5',
				'modules' => 'Modules',
				'updatelog' => 'Log',
				'languages' => 'Languages',
				'connect' => 'Test connection',
				'nextVersion' => 'Next version',
		),
		'register' => array(
				'headline' => 'Register webEdition',
				'description' => 'Register webEdition and discover its complete functionality.',
		),
		'update' => array(
				'headline' => 'Look for new version',
				'actualVersion' => 'Running version',
				'lastUpdate' => 'Last installation',
				'neverUpdated' => '-',
				'lookForUpdate' => 'Look for update',
		),
		'upgrade' => array(
				'headline' => 'Update to webEdition 5',
				'actualVersion' => 'Running version',
				'lookForUpdate' => 'Install webEdition 5',
		),
		'modules' => array(
				'headline' => 'Installation of modules',
				'installedModules' => 'Installed modules',
				'noModulesInstalled' => 'There are no modules installed',
				'showModules' => 'Select modules',
		),
		'languages' => array(
				'headline' => 'Installation of languages',
				'installedLngs' => 'The following languages are installed',
				'showLanguages' => 'Install more languages',
				'deleteSelectedLanguages' => 'Delete selected languages',
				'systemLanguage' => 'system language',
				'usedLanguage' => 'used language',
				'languagesDeleted' => 'The following languages were deleted',
				'languagesNotDeleted' => 'The following languages could not be deleted',
		),
		'connect' => array(
				'headline' => 'Check connection to update server',
				'description' => 'If there are problems running the update, you can test here, if it is possible to open a connection to the webEdition update server.',
				'connectionSuccess' => 'A connection to the update server could be established.',
				'connectionSuccessError' => 'A connection to the update server could be established, but there was an error on the server.<br />',
				'connectionError' => 'It is not possible to connect to the update server at the moment.',
				'connectionErrorJs' => 'It is not possible to establish a connection to the update server.',
				'connectionInfo' => "Connection informations",
				'availableConnectionTypes' => "Available connection types",
				'connectionType' => "Used connection type",
				'proxyHost' => "Proxy host",
				'proxyPort' => "Proxy port",
				'hostName' => "Hostname",
				'addressResolution' => "Address resolution",
				'updateServer' => "Update server",
				'ipResolutionTest' => "IP resolution test",
				'dnsResolutionTest' => "DNS resolution test",
				'succeeded' => "succeeded",
				'failed' => "failed",
				'ipAddresses' => "IP address(es)",
		),
		'state' => array(
				'headline' => 'Message from update server',
				'descriptionTrue' => 'The update server has completed this request.',
				'descriptionError' => 'The update server could not fullfill this request. The following error occured.',
		),
		'updatelog' => array(
				'headline' => 'Update Log',
				'logIsEmpty' => 'The update log is empty',
				'date' => 'Date / time',
				'action' => 'Action',
				'version' => 'Version',
				'entriesTotal' => 'Entries total',
				'page' => 'page',
				'noEntriesMatchFilter' => 'There are not entries matching the selected filter.',
				'legendMessages' => 'Messages',
				'legendNotices' => 'Notices',
				'legendErrors' => 'Errors',
				'confirmDelete' => 'Do you really want to delete all selected entries?',
		),
		'beta' => array(
				'headline' => 'Shall Pre-Release Versions be included in the search?', // TRANSLATE
				'lookForUpdate' => 'search for Pre-Release Versions', // TRANSLATE
				'warning' => '<b>Pre-Release versions,<br/> such as <u>nightly Builds, Alpha-, Beta- und RC-Versions</u>,<br/> should never be used in produktion sites!</b><br/><br/>They are provided for testing purposes only and aim at easing <br/>the search for severe erros before an official version is published.', // TRANSLATE
		),
);
