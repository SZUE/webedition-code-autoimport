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
 * class for error_reporting, uses the javascript function showmessage in
 * webEdition.php
 *
 * @category   we
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
abstract class we_core_MessageReporting extends we_message_reporting{

	// contansts for messaging!
	// these are binray checked like permissions in unix, DON'T change indexes
	const kMessageInfo = self::WE_MESSAGE_INFO;
	const kMessageFrontend = self::WE_MESSAGE_FRONTEND;
	const kMessageNotice = self::WE_MESSAGE_NOTICE;
	const kMessageWarning = self::WE_MESSAGE_WARNING;
	const kMessageError = self::WE_MESSAGE_ERROR;

}

