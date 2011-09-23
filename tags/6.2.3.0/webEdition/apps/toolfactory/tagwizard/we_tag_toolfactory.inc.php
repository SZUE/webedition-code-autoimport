<?php

/**
 * webEdition CMS
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
 * @package    webEdition_toolfactory
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;
$GLOBALS['weTagWizard']['weTagData']['noDocuLink'] = true;
$GLOBALS['weTagWizard']['weTagData']['DocuLink'] = 'tags.webedition.org/de/toolfactory/';
$GLOBALS['weTagWizard']['weTagData']['description']='Example tag for WE-Apps';

$GLOBALS['weTagWizard']['attribute']['id111_name'] = new weTagData_textAttribute('111', 'name', false, '');
