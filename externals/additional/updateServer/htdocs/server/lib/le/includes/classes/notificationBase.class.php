<?php

class notificationBase {

	/**
	 * Maintenance
	 *
	 * @return string
	 */
	function getMaintenanceResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/maintenance.inc.php');
		return updateUtil::getResponseString($ret);

	}

	function getInstallerVersionCheckResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/installerVersion.inc.php');
		return updateUtil::getResponseString($ret);

	}

	function getAnnouncementResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/announcement.inc.php');
		return updateUtil::getResponseString($ret);

	}
	
	function getHighloadResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/highload.inc.php');
		return updateUtil::getResponseString($ret);

	}

	function getHighloadSourceforgeResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/highloadSourceforge.inc.php');
		return updateUtil::getResponseString($ret);

	}
	
	/**
	 * Feature not available
	 *
	 * @return string
	 */
	function getNotAvailableAtTheMomentResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/notAvailableAtTheMoment.inc.php');
		return updateUtil::getResponseString($ret);

	}


	/**
	 * This is used during development of liveUpdate
	 *
	 * @return string
	 */
	function getLiveUpdateNotReadyYet() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/liveUpdateNotReadyYet.inc.php');
		return updateUtil::getResponseString($ret);

	}


	/**
	 * Session was lost
	 *
	 * @return string
	 */
	function getLostSessionResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/lostSession.inc.php');
		return updateUtil::getResponseString($ret);

	}


	/**
	 * Failure with database
	 *
	 * @return string
	 */
	function getDatabaseFailureResponse() {
		print notification::getStateResponseString('error', $GLOBALS['lang']['notification']['databaseFailure']);

	}


	/**
	 * Not known Command
	 *
	 * @return string
	 */
	function getCommandNotKnownResponse() {
		print notification::getStateResponseString('false', 'Eine Kombination aus<br />update_cmd->' . $_REQUEST['update_cmd'] . '<br />und<br />detail->' . $_REQUEST['detail'] . '<br /> ist nicht bekannt');

	}


	/**
	 * returns serealized state-response object
	 *
	 * @param boolean $state
	 * @return string
	 */
	function getStateResponseString($state='success', $message = 'no message') {

		$ret = array(
			'Type' => 'state',
			'State' => $state,
			'Message' => $message
		);
		return updateUtil::getResponseString($ret);

	}


}

?>