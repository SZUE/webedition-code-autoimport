<?php

abstract class notification extends notificationBase{

	/**
	 * Maintenance of upgrade to webEdition 4
	 *
	 * @return string
	 */
	static function getUpgradeMaintenanceResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/notification/upgradeMaintenance.inc.php');
		return updateUtil::getResponseString($ret);
	}

	/**
	 * This is used for old or deprecated beta-flags
	 *
	 * @return string
	 */
	static function getLiveUpdateNotPossibleForOldBetaResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/notification/liveUpdateNotPossibleForOldBeta.inc.php');
		return updateUtil::getResponseString($ret);
	}

	/**
	 * Upgrade not possible at the moment
	 *
	 * @return string
	 */
	static function getUpgradeNotPossibleYetResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/notification/upgradeNotPossibleYet.inc.php');
		return updateUtil::getResponseString($ret);
	}

	/**
	 * Update not possible until release
	 *
	 * @return string
	 */
	static function getUpdateNotPossibleUntilReleaseResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/notification/updateNotPossibleUntilRelease.inc.php');
		return updateUtil::getResponseString($ret);
	}

	/**
	 * Beta expired
	 *
	 * @return string
	 */
	static function getBetaExpiredResponse(){
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/betaExpired.inc.php');
		return updateUtil::getResponseString($ret);
	}

}
