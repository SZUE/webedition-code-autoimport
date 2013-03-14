<?php

class notification extends notificationBase {

	function getNoImportTypeSetResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/notification/noImportTypeSet.inc.php');
		return updateUtil::getResponseString($ret);
		
	}
}

?>