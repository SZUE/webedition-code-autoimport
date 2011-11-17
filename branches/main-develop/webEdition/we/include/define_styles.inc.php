<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
define('CSS_DIR', '/webEdition/css/');
define('SCRIPT_BUTTONS_ONLY', we_htmlElement::jsScript(JS_DIR . 'weButton.js'));
define('STYLESHEET_BUTTONS_ONLY', we_htmlElement::cssLink(CSS_DIR . 'we_button.css'));
define('STYLESHEET', we_htmlElement::cssLink(CSS_DIR . 'global.php?WE_LANGUAGE=' . $GLOBALS["WE_LANGUAGE"]). STYLESHEET_BUTTONS_ONLY . SCRIPT_BUTTONS_ONLY);
define('STYLESHEET_SCRIPT', we_htmlElement::cssLink(CSS_DIR . 'global.php?WE_LANGUAGE=' . $GLOBALS["WE_LANGUAGE"]) . STYLESHEET_BUTTONS_ONLY);
