<?php
// include config files

require_once(LIVEUPDATE_SERVER_DIR . '/../conf/conf.inc.php');
require_once(LIVEUPDATE_SERVER_DIR . "/../conf/define.inc.php");

//require_once('DB.php');
// include the PEAR Db class
require_once(LIVEUPDATE_SERVER_DIR.'/../database/we_database_base.class.php');
require_once(LIVEUPDATE_SERVER_DIR.'/../database/we_database_mysqli.class.php');

// include system classes
require_once(SHARED_DIR . '/includes/classes/bannerBase.class.php');
require_once(SHARED_DIR . '/includes/classes/debugBase.class.php');
require_once(SHARED_DIR . '/includes/classes/installationLogBase.class.php');
require_once(SHARED_DIR . '/includes/classes/installerBase.class.php');
require_once(SHARED_DIR . '/includes/classes/languagesBase.class.php');
require_once(SHARED_DIR . '/includes/classes/licenseBase.class.php');
require_once(SHARED_DIR . '/includes/classes/notificationBase.class.php');
require_once(SHARED_DIR . '/includes/classes/progressBarBase.class.php');
require_once(SHARED_DIR . '/includes/classes/updateBase.class.php');
require_once(SHARED_DIR . '/includes/classes/updateUtilBase.class.php');
require_once(SHARED_DIR . '/includes/classes/communityBase.class.php');
require_once(SHARED_DIR . '/includes/classes/soapRequest.class.php');

