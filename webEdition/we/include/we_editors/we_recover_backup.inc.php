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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
$what = we_base_request::_(we_base_request::STRING, 'pnt', 'frameset');
$step = we_base_request::_(we_base_request::INT, 'step', 1);
$weBackupWizard = new we_backup_wizard(we_backup_wizard::RECOVER);

switch($what){
	case 'frameset':
		echo $weBackupWizard->getHTMLFrameset();
		break;
	case 'body':
		echo $weBackupWizard->getHTMLStep($step);
		break;
	case 'cmd':
		echo we_html_tools::getHtmlTop('', '', '', $weBackupWizard->getHTMLCmd(), we_html_element::htmlBody());
		break;
	case 'busy':
		echo $weBackupWizard->getHTMLBusy();
		break;
	case 'extern':
		echo $weBackupWizard->getHTMLExtern();
		break;
	default:
		t_e(__FILE__ . ' unknown reference: ' . $what);
}