<?php
/**
 * $Id$
 */
// include config files
require_once(LIVEUPDATE_SERVER_DIR . '/includes/conf/conf.inc.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/conf/define.inc.php');

// replaceCode
require_once(LIVEUPDATE_SERVER_DIR . '/includes/extras/replaceCode.inc.php');

// include system classes
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/installerUpdate.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/languagesUpdate.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/notificationUpdate.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/progressBarUpdate.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/updateUpdate.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/updateUtilUpdate.class.php');
require_once(LIVEUPDATE_SERVER_DIR . '/includes/classes/upgradeUpdate.class.php');

// include languages
require_once(LIVEUPDATE_SERVER_LANGUAGE_DIR . '/' . SHARED_LANGUAGE . '.lang.php');

