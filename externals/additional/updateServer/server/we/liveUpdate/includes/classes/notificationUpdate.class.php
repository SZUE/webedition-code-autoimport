<?php
/**
 * $Id: notificationUpdate.class.php 13561 2017-03-13 13:40:03Z mokraemer $
 */

abstract class notificationUpdate extends notificationBase{

	/**
	 * Maintenance of upgrade to webEdition 4
	 *
	 * @return string
	 */
	static function getUpgradeMaintenanceResponse(){
		$ret = updateUtilBase::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/notification/upgradeMaintenance.inc.php');
		return updateUtilBase::getResponseString($ret);
	}

	/**
	 * This is used for old or deprecated beta-flags
	 *
	 * @return string
	 */
	static function getLiveUpdateNotPossibleForOldBetaResponse(){
		$ret = updateUtilBase::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/notification/liveUpdateNotPossibleForOldBeta.inc.php');
		return updateUtilBase::getResponseString($ret);
	}

	/**
	 * Upgrade not possible at the moment
	 *
	 * @return string
	 */
	static function getUpgradeNotPossibleYetResponse(){
		$ret = updateUtilBase::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/notification/upgradeNotPossibleYet.inc.php');
		return updateUtilBase::getResponseString($ret);
	}

	/**
	 * Update not possible until release
	 *
	 * @return string
	 */
	static function getUpdateNotPossibleUntilReleaseResponse(){
		$ret = updateUtilBase::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/notification/updateNotPossibleUntilRelease.inc.php');
		return updateUtilBase::getResponseString($ret);
	}

	/**
	 * Beta expired
	 *
	 * @return string
	 */
	static function getBetaExpiredResponse(){
		$ret = updateUtilBase::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/betaExpired.inc.php');
		return updateUtilBase::getResponseString($ret);
	}

}
