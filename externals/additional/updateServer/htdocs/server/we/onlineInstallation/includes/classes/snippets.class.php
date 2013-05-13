<?php

class snippets {


	/**
	 * returns form to register webedition
	 *
	 * @return string
	 */
	function getSnippetsFormResponse() {

		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/snippets/snippetsForm.inc.php');
		return updateUtil::getResponseString($ret);

	}

}

?>