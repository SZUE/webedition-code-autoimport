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
/* this file contains vertical tabs
 * it has variables:
 * - $allTabs => array of all tabnames
 * - $activeTab => current selected tab
 */

// initialise tabs
$tabs = new we_tabs();
foreach($this->Data['allTabs'] as $tabname){
	$tabs->addTab(g_l('liveUpdate', '[tabs][' . $tabname . ']'), ($this->Data['activeTab'] == $tabname), "'" . $tabname . "'");
}


// get output

$bodyContent = '<div id="main">' . $tabs->getHTML() . '</div>';

$body = we_html_element::htmlBody([
		'id' => 'eHeaderBody',
		'onload' => 'weTabs.setFrameSize();',
		'onresize' => 'weTabs.setFrameSize()'], $bodyContent);

echo we_html_tools::getHtmlTop('', '', '', we_html_element::cssLink(we_tabs::CSS) .
	we_html_element::jsElement('
function setTab(tab){
	top.updatecontent.location="?section="+tab;
}
' . we_tabs::JS_LOAD), $body);
