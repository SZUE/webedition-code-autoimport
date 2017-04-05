<?php
/**
 * $Id: notificationBase.class.php 13561 2017-03-13 13:40:03Z mokraemer $
 */

abstract class notificationBase{

	/**
	 * Maintenance
	 *
	 * @return string
	 */
	static function getMaintenanceResponse(){
		$ret = updateUtilBase::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/maintenance.inc.php');
		return updateUtilBase::getResponseString($ret);
	}

	static function getInstallerVersionCheckResponse(){
		$ret = updateUtilBase::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/installerVersion.inc.php');
		return updateUtilBase::getResponseString($ret);
	}

	static function getAnnouncementResponse(){
		$ret = updateUtilBase::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/announcement.inc.php');
		return updateUtilBase::getResponseString($ret);
	}


	/**
	 * Feature not available
	 *
	 * @return string
	 */
	static function getNotAvailableAtTheMomentResponse(){
		$ret = updateUtilBase::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/notAvailableAtTheMoment.inc.php');
		return updateUtilBase::getResponseString($ret);
	}

	/**
	 * This is used during development of liveUpdate
	 *
	 * @return string
	 */
	static function getLiveUpdateNotReadyYet(){
		$ret = updateUtilBase::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/liveUpdateNotReadyYet.inc.php');
		return updateUtilBase::getResponseString($ret);
	}

	/**
	 * Session was lost
	 *
	 * @return string
	 */
	static function getLostSessionResponse(){
		$ret = updateUtilBase::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/lostSession.inc.php');
		return updateUtilBase::getResponseString($ret);
	}

	/**
	 * Failure with database
	 *
	 * @return string
	 */
	static function getDatabaseFailureResponse(){
		print self::getStateResponseString('error', $GLOBALS['lang']['notification']['databaseFailure']);
	}

	/**
	 * Not known Command
	 *
	 * @return string
	 */
	static function getCommandNotKnownResponse(){
		trigger_error('Eine Kombination aus<br />update_cmd->' . $_REQUEST['update_cmd'] . '<br />und<br />detail->' . $_REQUEST['detail'] . '<br /> ist nicht bekannt');
		print self::getStateResponseString('false', 'Eine Kombination aus<br />update_cmd->' . $_REQUEST['update_cmd'] . '<br />und<br />detail->' . $_REQUEST['detail'] . '<br /> ist nicht bekannt');
	}

	/**
	 * returns serealized state-response object
	 *
	 * @param boolean $state
	 * @return string
	 */
	static function getStateResponseString($state = 'success', $message = 'no message'){
		return updateUtilBase::getResponseString([
				'Type' => 'state',
				'State' => $state,
				'Message' => $message
		]);
	}

}
