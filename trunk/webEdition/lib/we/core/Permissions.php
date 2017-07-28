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
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * Base class for permissions
 *
 * @category   we
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
abstract class we_core_Permissions{

	/**
	 * check on specific permission
	 *
	 * @param string $perm
	 * @return boolean
	 */
	static function hasPerm($perm){
		return permissionhandler::hasPerm(strtoupper($perm));
	}

	/**
	 * check on permission to see a page
	 *
	 * @return string
	 */
	//FIXME: remove in 6.6
	static function protect(){
		//FIXME: t_e is not available here
		//t_e('deprecated', 'this will be removed in 6.6');

		//correct some settings
		if(!isset($GLOBALS['TOOLNAME']) && isset($GLOBALS['controller'])){
			$GLOBALS['controller']->setParam('appDir', str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(getScriptName())));
		}
		//make sure apps work! correct old apps!
		require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

		we_html_tools::protect();
	}

}
