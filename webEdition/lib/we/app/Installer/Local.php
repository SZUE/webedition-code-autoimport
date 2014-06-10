<?php

/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package none
 * @subpackage we_app_Installer
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/lib/we/core/autoload.inc.php');

/**
 * @see we_app_Installer
 */
Zend_Loader::loadClass('we_app_Installer');

/**
 * class for installing and uninstalling webEdition applications (formerly known as "tools")
 *
 * @category   we
 * @package none
 * @subpackage we_app_Installer
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_app_Installer_Local extends we_app_Installer{

	protected function _preInstall(){
		if(!$this->_prepareInstallationFiles($this->_source) || !$this->_validateInstallationFiles($this->_source)){
			return false;
		}
		return true;
	}

	/**
	 * does the following actions:
	 * - remove installation files
	 * - inserts application entry into application toc
	 */
	protected function _postInstall(){
		if(!we_app_Common::rebuildAppTOC($this->_appname) || !$this->_removeInstallationFiles()){
			return false;
		}
		return true;
	}

	/**
	 * no update possible for local installations
	 */
	public function update(){
		return false;
	}

}
