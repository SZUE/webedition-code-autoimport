<?php
/*
Attempt to update
*/
// only execute on liveUpdate:
//aber meiner Meinung nach nicht notwendig, patches werden nicht ausgeführt
if(!is_readable("../../we/include/conf/we_conf.inc.php")) {
	//return true;
}
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");
include_once(WE_INCLUDES_PATH."we_classes/base/weFile.class.php");

function up6370_addNavigationToModules(){
	$file = $_SERVER["DOCUMENT_ROOT"] . '/webEdition/we/include/conf/we_active_integrated_modules.inc.php';
	if(file_exists($file)){
		include($file);
		if(!in_array('navigation', $GLOBALS['_we_active_integrated_modules'])){
			$GLOBALS['_we_active_integrated_modules'][] = 'navigation';
			$content = '<?php
$GLOBALS[\'_we_active_integrated_modules\'] = array(
\'' . implode("',\n'", $GLOBALS['_we_active_integrated_modules']) . '\'
);';
			file_put_contents($file, $content);
		}
	}
	return true;
}

up6370_addNavigationToModules();
we_updater::fixHistory();

return true;
