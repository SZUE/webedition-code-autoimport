<?php

// include config files
/*
if ( isset($_SERVER['SERVER_ADDR']) && ($_SERVER['SERVER_ADDR'] == '192.168.0.8' || $_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_ADDR'] == '10.10.1.51') ) {
	require_once(LIVEUPDATE_SERVER_DIR . '/../conf/conf.inc.hal.php');
	
} else {
	require_once(LIVEUPDATE_SERVER_DIR . '/../conf/conf.inc.php');
	
}
*/
require_once(LIVEUPDATE_SERVER_DIR . '/../conf/conf.inc.php');
require_once(LIVEUPDATE_SERVER_DIR . "/../conf/define.inc.php");

// include the PEAR Db class
require_once('DB.php');
// include the PEAR i18n class
require_once('I18Nv2.php');

// include system classes
require_once(SHARED_DIR . '/includes/classes/bannerBase.class.php');
require_once(SHARED_DIR . '/includes/classes/debugBase.class.php');
require_once(SHARED_DIR . '/includes/classes/installationLogBase.class.php');
require_once(SHARED_DIR . '/includes/classes/installerBase.class.php');
require_once(SHARED_DIR . '/includes/classes/languagesBase.class.php');
require_once(SHARED_DIR . '/includes/classes/licenseBase.class.php');
require_once(SHARED_DIR . '/includes/classes/modulesBase.class.php');
require_once(SHARED_DIR . '/includes/classes/notificationBase.class.php');
require_once(SHARED_DIR . '/includes/classes/progressBarBase.class.php');
require_once(SHARED_DIR . '/includes/classes/registerBase.class.php');
require_once(SHARED_DIR . '/includes/classes/updateBase.class.php');
require_once(SHARED_DIR . '/includes/classes/updateUtilBase.class.php');
require_once(SHARED_DIR . '/includes/classes/communityBase.class.php');
require_once(SHARED_DIR . '/includes/classes/soapRequest.class.php');

?>