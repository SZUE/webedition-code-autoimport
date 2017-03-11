<?php
/**
 * This file contains an array with changes necessary during registration of webEdition
 * AND
 * changes needed for the onlineInstaller to downgrade this version to a demo version
 */
/*
 * changes for BETA-3900
 */

// add licensee to we_conf.inc.php
$replaceCode['we_conf']['path']['3900'] = '/webEdition/we/include/conf/we_conf.inc%s';
$replaceCode['we_conf']['needle']['3900'] = '"WE_LIZENZ","[0-9A-Za-z -]*"';
$replaceCode['we_conf']['replace']['3900'] = '"WE_LIZENZ","%s"';

// add version and uid
$replaceCode['we_version']['path']['3900'] = '/webEdition/we/include/we_version%s';
$replaceCode['we_version']['replace']['3900'] = '<?php
define("WE_VERSION","%s");
define("WE_VERSION_SUPP","%s");
define("WE_ZFVERSION","%s");
define("WE_SVNREV","%s");
define("WE_VERSION_SUPP_VERSION","%s");
define("WE_VERSION_BRANCH","%s");
define("WE_VERSION_NAME","%s");
';

// remove demo pop-up webEdition.php
$replaceCode['webEdition']['path']['3900'] = '/webEdition/webEdition%s';
$replaceCode['webEdition']['needle']['3900'] = 'var we_demo = true;';
$replaceCode['webEdition']['replace']['3900'] = 'var we_demo = false;';
