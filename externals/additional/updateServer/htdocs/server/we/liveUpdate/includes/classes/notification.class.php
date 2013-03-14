<?php

class notification extends notificationBase {


	/**
	 * Maintenance of upgrade to webEdition 4
	 *
	 * @return string
	 */
	function getUpgradeMaintenanceResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/notification/upgradeMaintenance.inc.php');
		return updateUtil::getResponseString($ret);

	}


	/**
	 * This is used for old or deprecated beta-flags
	 *
	 * @return string
	 */
	function getLiveUpdateNotPossibleForOldBetaResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/notification/liveUpdateNotPossibleForOldBeta.inc.php');
		return updateUtil::getResponseString($ret);

	}


	/**
	 * Upgrade not possible at the moment
	 *
	 * @return string
	 */
	function getUpgradeNotPossibleYetResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/notification/upgradeNotPossibleYet.inc.php');
		return updateUtil::getResponseString($ret);

	}


	/**
	 * Update not possible until release
	 *
	 * @return string
	 */
	function getUpdateNotPossibleUntilReleaseResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/notification/updateNotPossibleUntilRelease.inc.php');
		return updateUtil::getResponseString($ret);

	}


	/**
	 * Beta expired
	 *
	 * @return string
	 */
	function getBetaExpiredResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(SHARED_TEMPLATE_DIR . '/notification/betaExpired.inc.php');
		return updateUtil::getResponseString($ret);

	}

}

?>