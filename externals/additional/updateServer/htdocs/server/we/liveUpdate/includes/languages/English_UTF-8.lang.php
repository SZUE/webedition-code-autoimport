<?php

$lang['register']['headline'] = 'Registration';
$lang['register']['insertSerial'] = 'Please enter your serial';
$lang['register']['serial'] = 'Serial number';
$lang['register']['repeatRegistration'] = 'The registration information on our server differs from the information on your domain. This error can occur if you have modules that are registered and associated with your domain but which are no longer installed (after reinstall). Please re-enter your serial number to adjust the registered data. Thereafter you can proceed with the update/installation of modules.';
$lang['register']['reInstallModules'] = 'The registration could not be completed. Some of the registered modules are not installed on the domain. To complete the registration, these modules are now installed.';
$lang['register']['errorWithSerial'] = 'The registration could not be processed';
$lang['register']['registerSuccess'] = 'Your webEdition licence has been successfully registered. webEdition will be restarted now to use all included features.';
$lang['register']['registerError'] = 'The registration could not be completed. There was the follwoing error.';
$lang['register']['registerErrorDetail'] = 'The files needed for registration could not be changed!';
$lang['register']['informAboutUpdates'] = 'Inform me about updates';
$lang['register']['email'] = 'E-Mail Address';
$lang['register']['salutation'] = 'Salutation';
$lang['register']['titel'] = 'Title';
$lang['register']['forename'] = 'Forename';
$lang['register']['surname'] = 'Surname';
$lang['register']['language'] = 'Language';
$lang['register']['enterValidEmail'] = 'Please enter a valid E-Mail address.';
$lang['register']['salutationMr'] = 'Mr.';
$lang['register']['salutationMrs'] = 'Mrs.';

$lang['license']['undefinedError'] = 'Undefined Error';

$lang['upgrade']['headline'] = 'Upgrade to webEdition 5';
$lang['upgrade']['registerBeforeUpgrade'] = 'The registration information on our server differs from the information on your domain. This error can occur if you have modules that are registered and associated with your domain but which are no longer installed (after reinstall). To correct this, use the update functions for webEdition 5. Thereafter you can proceed with the update to webEdition version 6.';
$lang['upgrade']['registerBeforeUpgrade_we4'] = 'The registration information on our server differs from the information on your domain. This error can occur if you have modules that are registered and associated with your domain but which are no longer installed (after reinstall). To correct this, use the update functions for webEdition 4. Thereafter you can proceed with the update to webEdition version 5.';
$lang['upgrade']['registerBeforeUpgrade_we5light'] = 'The registration information on our server differs from the information on your domain. To correct this, use the update functions for webEdition 5 light. Thereafter you can proceed with the update to webEdition version 5.';
$lang['upgrade']['upgradePossibleText'] = 'Select to which version you want to update';
$lang['upgrade']['upgradeToVersion'] = 'Upgrade to version';
$lang['upgrade']['confirmUpgradeWarning'] = 'You are about to upgrade to webEdition 6. <b>In a first step, you can upgrade only to version 6.0.0.6.</b> During this process, all webEdition programme files will be replaced. This process can take some time.<br /><br /><b>Attention:</b><ul><li>webEdition 6 requires at least <u>PHP version 5.2</u> or newer.</li><li>After the update webEdition must be restarted.</li><li>After restart, you must make a complete rebuild of your web-site.</li><li><b>Finally you can update to the latest version of webEdition 6.</b></li><li>We recommend to do this step by step 6.0.0.6 -&gt; 6.1.0.2, 6.1.0.2 -&gt; 6.2.X (latest) with rebuilds after each step.</li></ul>';
$lang['upgrade']['confirmUpgradeWarningTitle'] = 'Please confirm to continue:';
$lang['upgrade']['confirmUpgradeWarningCheckbox'] = 'I hereby confirm that I have read the above notice.';
$lang['upgrade']['confirmUpdateWarning'] = 'You are about to update your webEdition 6 installation.<br /><br /><b>Attention:</b><ul><li>After the update webEdition should be restarted.</li><li>You should make a rebuild after the update.</li></ul>';
$lang['upgrade']['confirmUpdateHint'][6007] = '<b>webEdition 6.0.0.7:</b><ul><li><b>Starting with this webEdition version, <u>PHP version 5.2.4</u> or newer is required.</b><br/>The used PHP version can be found over the dialog "system information" (menu Help).</li><li>After the rebuild over documents and templates, please rebuild also:<ul><li>Index table</li><li>Objects</li></ul></li></ul>';
$lang['upgrade']['confirmUpdateHint'][6008] = '<b>webEdition 6.0.0.8:</b><ul><li><b>For this webEdition version, <u>PHP version 5.2.4</u> or newer is required.</b><br/>The used PHP version can be found over the dialog "system information" (menu Help).</li><li>After the rebuild over documents and templates, please rebuild also:<ul><li>Navigation</li></ul></li></ul>';
$lang['upgrade']['confirmUpdateHint'][6100] = '<b>webEdition 6.1.0.0:</b><ul><li>This update requires temporarily about <b>62 MB free webspace (Quota!)</b> since all files are going to be replaced</li><li><b>For this webEdition version, <u>PHP version 5.2.4</u> or newer is required.</b><br/>The used PHP version can be found over the dialog "system information" (menu Help).</li><li>After the rebuild over documents and templates, please rebuild also:<ul><li>Navigation</li><li>Objects</li><li>Templates</li></ul></li><li>The PHP classes smtp.class.php, we_mailer_class.inc.php, weNewsletterMailer.php will not be available in future and are declared as DEPRECATED. If this classes are used in direct PHP programming in templates, (the we:tags are not affected), they are to be replaced by calls to the class we_util_Mailer (or Zend_Mail).<br/><b>In this installation, the classes are not deleted and can still be used.</b></li><li>Due to the update of the JS framework YUI, <strong>already installed WE-Apps do not work in this version of webEdition</strong> and have to be adopted before the update. A documentation of the necessary changes can be found at <a href="http://documentation.webedition.org/wiki/en/webedition/developer-information/software-development-kit-sdk/changes-from-sdk6000-to-sdk6100/start" target="_blank">Changes from SDK version 6.0.0.0 to SDK version 6.1.0</a</li></ul>';
$lang['upgrade']['confirmUpdateHint'][6101] = '<b>webEdition 6.1.0.1:</b><ul><li><b>For this webEdition version, <u>PHP version 5.2.4</u> or newer is required.</b></li></ul>';
$lang['upgrade']['confirmUpdateHint'][6102] = '<b>webEdition 6.1.0.2:</b><ul><li>For this webEdition version, <u>PHP version 5.2.4</u> or newer is required.</li><li>The wrong behavior of &lt;we:ifRegisteredUser cfilter="true" /&gt; with set customer filter and document setting "Filter is off (all visitors have access)" was corrected.</b> If this exact setting os used in documents, all visitors get now access to these documents. <b>This should be checked <u>before</u> and after the update.</b></li></ul>';
$lang['upgrade']['confirmUpdateHint'][6200] = '<b>webEdition 6.2.0.0:</b><ul><li>This update requires temporarily about <b>80 MB free webspace (Quota!)</b> since the files of the Zend framework are replaced</li><li>For this webEdition version, <u>PHP version 5.2.4</u> or newer is required.</li><li>After the rebuild over documents and templates, please rebuild also:<ul><li>Navigation</li><li>Objects</li><li>Index</li></ul></li><li>In this version, new DB indices are introduced. Please check the update log after the update. If there are errors due to double entries, you have to clean up the tables (delete double entries) with an external tool. After cleaning up, please repeat the update and all rebuilds</li><li>The loading of WE tags was optimized. If problems occur, you can go back to the old behavior in the settings dialog, tab system at backward compatibility</li></ul>';
$lang['upgrade']['confirmUpdateHint'][6210] = '<b>webEdition 6.2.1.0:</b><ul><li>This update requires temporarily about <b>35 MB free webspace (Quota!)</b> since the files of the Zend framework are replaced</li><li>For this webEdition version, <u>PHP version 5.2.4</u> or newer is required and a MySQL databse Version 5.x or higher.</li><li>In 6.2.0, new DB indices were introduced. Please check the update log after the update. If there are errors due to double entries, you have to clean up the tables (delete double entries) with an external tool. After cleaning up, please repeat the update and all rebuilds</li></ul>';
$lang['upgrade']['confirmUpdateHint'][6220] = '<b>webEdition 6.2.2.0:</b><ul><li>For this webEdition version, <u>PHP version 5.2.4</u> or newer is required and a MySQL databse Version 5.x or higher.</li><li>This update fixes an old error which was relevant only in version 6.2.1. The spelling of the tags is correctd to the old standard: &lt;we:conditionAnd&gt; (not AND) und &lt;we:conditionOr&gt; (not OR). If problems occur, you can select backward compatibility in the settings, tab system.</li></ul>';
$lang['upgrade']['confirmUpdateHint'][6230] = '<b>webEdition 6.2.3.0:</b><ul><li>This update fixes a severe security problem in the customer module. To fix it, among other changes, the standard value for the attribute register of the tag we:saveRegisteredUser had to be changed. Should the registration of new customers not work properly after the update, you can set the old behaviour in the settings dialog of the customer module.</li></ul>';
$lang['upgrade']['confirmUpdateHint'][6300] = '<b>webEdition 6.3.0.0:</b><ul><li>This update optimizes the complete webEdition infra strukture. Due to the many changes it is possible to run into problems after the update!</li><li><b>Perform a komplete backup of the entire Site</b></li><li>Follow the hints in the version history in detail about <b>possible problems und solutions Problemen und Lösungen, see <a href="http://www.webedition.org/de/webedition-cms/versionshistorie/webedition-6/version-6.3.0.0" target="_blank">version 6.3.0.0</a></b></li><li>Possibly, perform a <b>test update</b> on a copy of the site.</li><li>After rebuilding all templates and Ddkuments, please check the error log for further hints on problems</li></ul>';

$lang['upgrade']['confirmUpdateDiskquotaWarning0']='<br/>You have more than 100 MB free Webspace.';
$lang['upgrade']['confirmUpdateDiskquotaWarning1']='<br/>You have only <b>';
$lang['upgrade']['confirmUpdateDiskquotaWarning2']='MB</b> free WebSpace (Quota) left, <br/><b>check the update hints carefully</b> for the required disk space!';
$lang['upgrade']['repeatUpdateDiskquotaWarning1']='<br/>You have only <b>';
$lang['upgrade']['repeatUpdateDiskquotaWarning2']='MB</b> free WebSpace (Quota) left, <br/><b>this will not be enough for an repeat update!</b>';
$lang['upgrade']['confirmUpdateWarningEnd'] = '';
$lang['upgrade']['confirmUpdateWarningTitle'] = 'Please confirm to continue:';
$lang['upgrade']['confirmUpdateWarningCheckbox'] = 'I hereby confirm that I have read the above notice.';
$lang['upgrade']['pleaseSelectVersion'] = 'Please select a target version for the update.';
$lang['upgrade']['noUpgradeForLanguages'] = 'An update to webEdition 6 is not possible at the moment. Several of your installed languages prevent the update.';
$lang['upgrade']['copyFilesSuccess'] = 'All needed webEdition files are stored, the webEdition folder successful moved.';
$lang['upgrade']['copyFilesError'] = 'Could not move the webEdition 5 folder';
$lang['upgrade']['copyFilesInstalledModulesError'] = 'Could not create the file we_installed_modules';
$lang['upgrade']['copyFilesVersionError'] = 'Could not create the file version.php';
$lang['upgrade']['copyFilesConfError'] = 'Could not create the configuration file';
$lang['upgrade']['copyFilesBackupError'] = 'Could not move the backup folder';
$lang['upgrade']['copyFilesDirectoryError'] = 'Could not create the folder %s';
$lang['upgrade']['copyFilesMoveDirectoryError'] = 'Could not move the folder %s';
$lang['upgrade']['copyFilesFileError'] = 'Could not copy the file %s';
$lang['upgrade']['executePatchesDatabase'] = 'Could no adjust Tables for webEdition 6. The following tables could not be adjusted.';
$lang['upgrade']['notEnoughLicenses'] = 'You do not own enough licenses to update to webEdition 6. You can buy updates in our shop.';
$lang['upgrade']['finishInstallationError'] = 'Could not complete the update to webEdition 6.<br />Please check, if<br /><ul><li>The webEdition folder was renamed to webEdition5 (Does the folder /webEdition5 exist?)</li><li>The webEdition6 folder was renamed to webEdition (Is there also the folder /webEdition?)</li><br /><li>The backup folder was moved to webEdition/we_backup (Does the folder /webEdition/we_backup exist?)</li><br /><li>The site folder was moved to webEdition/site. (Does the folder /webEdition/site exist?)</li></ul><br />Please try to refresh this site first (press the refresh button), or try to make the described changes by yourself, or contact our support.';
$lang['upgrade']['finishInstallationError_we4'] = 'Could not complete the update to webEdition 5.<br />Please check, if<br /><ul><li>The webEdition folder was renamed to webEdition4 (Does the folder /webEdition4 exist?)</li><li>The webEdition5 folder was renamed to webEdition (Is there also the folder /webEdition?)</li><br /><li>The backup folder was moved to webEdition/we_backup (Does the folder /webEdition/we_backup exist?)</li><br /><li>The site folder was moved to webEdition/site. (Does the folder /webEdition/site exist?)</li></ul><br />Please try to refresh this site first (press the refresh button), or try to make the described changes by yourself, or contact our support.';
$lang['upgrade']['finishInstallationError_we5light'] = 'Could not complete the update to webEdition 5.<br />Please check, if<br /><ul><li>The webEdition folder was renamed to webEdition5light (Does the folder /webEdition5light exist?)</li><li>The webEdition5 folder was renamed to webEdition (Is there also the folder /webEdition?)</li><br /><li>The backup folder was moved to webEdition/we_backup (Does the folder /webEdition/we_backup exist?)</li><br /><li>The site folder was moved to webEdition/site. (Does the folder /webEdition/site exist?)</li></ul><br />Please try to refresh this site first (press the refresh button), or try to make the described changes by yourself, or contact our support.';
$lang['upgrade']['finished'] = 'Update to webEdition version 6 completed';
$lang['upgrade']['finished_note'] = 'The installation is completed. To activate all changes, webEdition is restarted now.<br /><strong>Please dont\'t forget to delete your browser cache and make a complete rebuild of your web-site.</strong>';
$lang['upgrade']['notepad_category'] = 'Sonstiges';
$lang['upgrade']['notepad_headline'] = 'Welcome to webEdition 6';
//$lang['upgrade']['notepad_text'] = 'One of the new features in version 5 is the cockpit. You can select several widgets in the cockpit menu. Each widget can be adjusted and positioned in the title bar.';
$lang['upgrade']['notepad_text'] = '';


$lang['update']['headline'] = 'Update';
$lang['update']['nightly-build'] = 'nightly build';
$lang['update']['alpha'] = 'Alpha';
$lang['update']['beta'] = 'Beta';
$lang['update']['rc'] = 'RC';
$lang['update']['release'] = 'official Release';
$lang['update']['installedVersion'] = 'Running version';
$lang['update']['newestVersionSameBranch'] = '<br/>Available version from the same branch';
$lang['update']['newestVersion'] = '<br/>Newest available version';
$lang['update']['updateAvailableText'] = 'Your installed webEdition is not up to date anymore. Please select the version you want to update to';
$lang['update']['updatetoVersion'] = 'Update to version:';
$lang['update']['suggestCurrentVersion'] = 'We strongly recommend to use the most recent version all the time.';
$lang['update']['noUpdateNeeded'] = 'There is currently no update available. You are already running the most recent version.';
$lang['update']['repeatUpdatePossible'] = 'You can repeat an update. During that process all webEdition program files are replaced.<br />Attention, this process will need some time.<br/><b>This process needs up to max. 100 MB free Webspace.</b>';
$lang['update']['repeatUpdateNeeded'] = '<b>Before you can update to the new version, you have to repeat the update of the current installed version (replacing all webEdition program files)</b>, since your installed SVN revison is lower than the SVN revision stored in the database.<br />Attention, this process will need some time.<br/><b>This process needs up to max. 100 MB free Webspace.</b>';
$lang['update']['repeatUpdateNotPossible'] = 'Your installed version is newer than the version available for update. <b>Therefore, you can not repeat the update.</b> If you want to repeat  nightly builds or Alpha, Beta or RC versions, please activate the option in the tab "Pre-Release Versions"';

$lang['update']['noUpdateForLanguagesText'] = 'You are running webEdition vesrion %s. There is no update available currently, as the update is not available for all your installed languages.';
$lang['update']['installedLanguages'] = 'The following languages are installed on your system';
$lang['update']['updatePreventingLanguages'] = 'These languages prevent the update:';
$lang['update']['confirmUpdateText'] = 'You are using version&nbsp;%s and want to update to version&nbsp;%s.';
$lang['update']['confirmUpdateVersionDetails'] = 'Details to the versions can be found in our <a target="_blank" href="http://documentation.webedition.org/wiki/en/webedition/change-log/version-6/start">version history</a>.';
$lang['update']['confirmRepeatUpdateText'] = 'You are currently using version&nbsp;%s and want to install it again.';
$lang['update']['confirmRepeatUpdateMessage'] = 'During a reinstallation (update repeat) all webEdition programm files are replaced by the original programme files. This process could take some time.';
$lang['update']['finished'] = 'Update completed';
$lang['update']['we51Notification'] = '<h2>Important information before updating!</h2><p>This information is relevant for you if you update from webEdition 5.0 to version 5.1 or higher.</p><ul><li><b>Changes in the user interface:</b> The GUI has been improved. Further information can be found in the <a target="_blank" href="http://documentation.webedition.org/wiki/en/webedition/change-log/version-5/start">version history</a>.</li><li><b>System requirements have changed:</b> From version 5.1, webEdition requires at least PHP 4.3. You can check the installed PHP version on your server from within webEdition using the menu item Help => system information.</li><li><b>Navigation tool:</b> If the customer management is installed, it might be necessary to set the permissions for customers in the navigation tool anew. The filters were redesigned completely, so some settings from 5.0 cannot be taken over with the update automatically.</li></ul>';

$lang['update']['ReqWarnung'] = 'Attention!';
$lang['update']['ReqWarnungText'] = 'Your system does not fulfill all system requirements:';
$lang['update']['ReqWarnungKritisch'] = 'Update blocking: ';
$lang['update']['ReqWarnungHinweis'] = 'Hint: ';
$lang['update']['ReqWarnungPCREold1'] = 'Your PCRE version (';
$lang['update']['ReqWarnungPCREold2'] = ') is outdated. This can lead to some problems.';
$lang['update']['ReqWarnungPHPextension'] = 'A required PHP extension is missing: ';
$lang['update']['ReqWarnungPHPextensionND'] = 'The required PHP extensions can not be checked';
$lang['update']['ReqWarnungNoCheck'] = 'It can not be determined if your system meets the current system requirements for the update. Please check the system requirements yourself at <a href="http://www.webedition.org/de/webedition-cms/systemvoraussetzungen.php" target="_blank">http://www.webedition.org/de/webedition-cms/systemvoraussetzungen.php</a><br/>We recommend, <b>after manually checking the system requirements above,</b> to update first to <b>version 6.1.0.2</b>. The requirements in this version are lower and be checked in later updates automatically.';
$lang['update']['ReqWarnungMySQL4'] = 'For the desired version, MySQL version 4.1 or higher is required. benötigt. This requirement is not met.';
$lang['update']['ReqWarnungMySQL5'] = 'For the desired version, MySQL version 5.0 or higher is required. benötigt. This requirement is not met.';
$lang['update']['ReqWarnungSDKdb'] = 'SDK DB operations and WE-APPS using database access are not availlable, the PHP extensions PDO und PDO_mysql are not available';
$lang['update']['ReqWarnungMbstring'] = 'MultiByte String support (PHP extension mbstring) is not available. Therefore utf-8 sites are not possible, SDK und Apps are not usuable and future versions of webEdition may not work at all.';
$lang['update']['ReqWarnungGdlib'] = 'The PHP GDlib funktions (PHP extension gd) are not available, therefore, many image manipulation and preview functions are not available.';
$lang['update']['ReqWarnungExif'] = "The exif PHP extension is not available, therefore, EXIF metadata for images are nut usuable.";
$lang['update']['ReqWarnungPHPversion'] = 'PHP version 5.2.4 or newer is required for the update. Determined was version ';
$lang['update']['ReqWarnungPHPversionForV640'] = 'PHP version 5.3.7 or newer is required for the update to a webEdition version newer than 6.3.9.0. Determined was PHP version ';

$lang['update']['spenden'] = 'This webEdition version was made possible bei the work of the charitable foundation webEdition e.V. Support the work of the foundation and its members with your donation<br>
It allows the foundation to employ professional developers which make it <br/>
possible to fix bugs and to implement new features faster and to ensure <br/>
the development of webEdition on the long run';

$lang['modules']['headline'] = 'Installation of modules';
$lang['modules']['textConfirmModules'] = 'The following modules will be installed. Confirm them and the installation will start. After the installation webEdition must be restarted.';
$lang['modules']['reselectModules'] = 'The selected Modules can not be installed. You do not have enough licenses for the selected modules.<br /><br />Please reselect the modules.<br /><br />You selected the follwing modules:';
$lang['modules']['noModulesSelected'] = 'You have no module selected';
$lang['modules']['moduleAlreadyInstalled'] = 'This module is already installed. You can repeat the installation if you want to.';
$lang['modules']['normalModules'] = 'Modules';
$lang['modules']['proModules'] = '';
$lang['modules']['dependentModules'] = 'Dependent modules';
$lang['modules']['noInstallableModules'] = 'You can not install any modules now. All the modules you have are already installed.<br />You can buy new modules in our shop.';
$lang['modules']['finished'] = 'Installation of modules completed';


$lang['installer']['headline'] = 'Installation';
$lang['installer']['headlineConfirmInstallation'] = 'Confirm Installation';
$lang['installer']['confirmInstallation'] = 'ATTENTION !<br />Your data may become corrupted during the update process. If you continue without a backup, you risk losing data.';
$lang['installer']['downloadInstaller'] = 'Download installer';
$lang['installer']['getChanges'] = 'Determine needed files';
$lang['installer']['downloadChanges'] = 'Download files';
$lang['installer']['prepareChanges'] = 'Prepare files';
$lang['installer']['updateDatabase'] = 'Update database';
$lang['installer']['copyFiles'] = 'Copy files';
$lang['installer']['executePatches'] = 'Execute patches';
$lang['installer']['finishInstallation'] = 'Finish installation';
$lang['installer']['downloadFilesTotal'] = 'This update contains %s files';
$lang['installer']['downloadFilesFiles'] = 'Files';
$lang['installer']['downloadFilesPatches'] = 'Patches';
$lang['installer']['downloadFilesQueries'] = 'Database queries';
$lang['installer']['updateDatabaseNotice'] = 'Notice during step: Update database';
$lang['installer']['tableExists'] = 'Table already exists';
$lang['installer']['tableChanged'] = 'Altered Table';
$lang['installer']['entryAlreadyExists'] = 'Entries already exist';
$lang['installer']['errorExecutingQuery'] = 'Could not execute several queries.';
$lang['installer']['amountFilesCopied'] = '%s files installed';
$lang['installer']['amountPatchesExecuted'] = '%s Patch(es) executed';
$lang['installer']['finished'] = 'The installation is completed. To activate all changes, webEdition is restarted now.';

$lang['languages']['headline'] = 'Installation of languages';
$lang['languages']['installLamguages'] = 'The following languages can be installed.<br /><i>Emphasized</i> languages are already installed on your system, but you can repeat the installation.<br /><b>Please Note:</b> Languages marked as <font color="red">[beta]</font> can be incomplete or even defective. But you are welcome to help the webEdition team completing these translations.';
$lang['languages']['languagesNotReady'] = 'The follwoing languages can not be installed for the version you are running';
$lang['languages']['confirmInstallation'] = 'The following languages will be installed.';
$lang['languages']['installLanguages'] = 'Install selected languages';
$lang['languages']['noLanguageSelectedText'] = 'You have not selected a language. Please select the languagaes you want to install.';
$lang['languages']['finished'] = 'Installation of languages completed';

$lang['notification']['upgradeNotPossibleYet'] = 'An update to version 5 is not possible until 04.06.2007';
$lang['notification']['upgradeMaintenance'] = 'Due to maintenance, an update to webEdition version 5 is not possible at the moment.';




$luSystemLanguage = array();

$luSystemLanguage['installer']['downloadInstallerError'] = 'Error during step: Download installer';
$luSystemLanguage['installer']['getChangesError'] = 'Error during step: Determine needed files';
$luSystemLanguage['installer']['downloadChangesError'] = 'Error during step: Download files';
$luSystemLanguage['installer']['updateDatabaseError'] = 'Error during step: Update database';
$luSystemLanguage['installer']['updateDatabaseNotice'] = 'Notice during step: Update database';
$luSystemLanguage['installer']['prepareChangesError'] = 'Error during step: Prepare files';
$luSystemLanguage['installer']['copyFilesError'] = 'Error during step: Install files';
$luSystemLanguage['installer']['executePatchesError'] = 'Error during step: Execute patches';
$luSystemLanguage['installer']['finishInstallationError'] = 'Error during step: Finish installation';
$luSystemLanguage['installer']['errorMessage'] = 'Error message';
$luSystemLanguage['installer']['errorIn'] = 'at';
$luSystemLanguage['installer']['errorLine'] = 'line';
$luSystemLanguage['installer']['tableExists'] = 'Table already exists';
$luSystemLanguage['installer']['tableChanged'] = 'Altered Table';
$luSystemLanguage['installer']['entryAlreadyExists'] = 'Entries already exist';
$luSystemLanguage['installer']['errorExecutingQuery'] = 'Could not execute several queries.';
$luSystemLanguage['installer']['fileNotWritableError'] = 'weBedition does not have write access for the following file, and the installer was not able to adjust the access rights by itself:<br />\\\n<code class=\\\\\"errorText\\\\\">%s</code><br />\\\nPlease adjust the rights manually and click on the button \\\\\"Load again\\\\\" to continue installation.';

$luSystemLanguage['register']['registrationError'] = 'Error during Registration process';
$luSystemLanguage['register']['finished'] = 'Registration finished';

$luSystemLanguage['repeatRegistration']['finished'] = 'Reentered registration information';

$luSystemLanguage['upgrade']['start'] = 'Start Update to webEdition version 5 (' . (isset($_SESSION['clientTargetVersion']) ? $_SESSION['clientTargetVersion'] : '') . ')';
$luSystemLanguage['upgrade']['finished'] = 'Update to webEdition version 5 completed';

$luSystemLanguage['update']['start'] = 'Start Update to version ' . (isset($_SESSION['clientTargetVersion']) ? $_SESSION['clientTargetVersion'] : '');
$luSystemLanguage['update']['finished'] = 'Update completed.';
$luSystemLanguage['update']['version'] = ' Version: ';
$luSystemLanguage['update']['branch'] = ' Branch: ';
$luSystemLanguage['update']['svn'] = ', SVN-Revision: ';

$luSystemLanguage['modules']['start'] = 'Start installation of modules';
$luSystemLanguage['modules']['finished'] = 'Installation of modules completed';

$luSystemLanguage['languages']['start'] = 'Start installation of languages';
$luSystemLanguage['languages']['finished'] = 'Installation of languages completed';

?>