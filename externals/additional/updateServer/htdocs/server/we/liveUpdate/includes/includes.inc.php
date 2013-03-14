<?php

// include config files
require_once(LIVEUPDATE_SERVER_DIR . '/includes/conf/conf.inc.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/conf/define.inc.php');

// replaceCode
require_once(LIVEUPDATE_SERVER_DIR . '/includes/extras/replaceCode.inc.php');

// include system classes
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/banner.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/debug.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/installationLog.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/installer.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/languages.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/license.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/modules.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/notification.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/progressBar.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/register.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/update.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/updateUtil.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/upgrade.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/community.class.php');

// include languages
require_once(LIVEUPDATE_SERVER_LANGUAGE_DIR . '/' . SHARED_LANGUAGE . '.lang.php');

?>