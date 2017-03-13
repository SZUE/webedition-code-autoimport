<?php
/**
 * $Id$
 */
// include config files
require_once(LIVEUPDATE_SERVER_DIR . '/includes/conf/conf.inc.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/conf/define.inc.php');

// include system classes
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/installerInstaller.class.php');
require_once(LIVEUPDATE_SERVER_DIR . "/includes/classes/installApplication.class.php");
require_once(LIVEUPDATE_SERVER_DIR . "/includes/classes/installerDownload.class.php");
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/languagesInstaller.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/licenseInstaller.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/notificationInstaller.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/updateInstaller.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/updateUtilInstaller.class.php');

// include languages
require_once(LIVEUPDATE_SERVER_LANGUAGE_DIR . '/' . SHARED_LANGUAGE . '.lang.php');

