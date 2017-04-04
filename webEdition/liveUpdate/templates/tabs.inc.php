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
$tabs->addTab(g_l('liveUpdate', '[tabs][update]'), 'fa-recycle', ($this->Data['activeTab'] == $tabname), "'" . $tabname . "'");
$tabs->addTab(g_l('liveUpdate', '[tabs][languages]'), 'fa-language', ($this->Data['activeTab'] == $tabname), "'" . $tabname . "'");
$tabs->addTab(g_l('liveUpdate', '[tabs][updatelog]'), 'fa-list-alt', ($this->Data['activeTab'] == $tabname), "'" . $tabname . "'");
$tabs->addTab(g_l('liveUpdate', '[tabs][connect]'), 'fa-handshake-o', ($this->Data['activeTab'] == $tabname), "'" . $tabname . "'");
$tabs->addTab(g_l('liveUpdate', '[tabs][beta]'), 'fa-unlock-alt', ($this->Data['activeTab'] == $tabname), "'" . $tabname . "'");

// get output

echo we_html_tools::getHtmlTop('', '', '', we_html_element::cssLink(CSS_DIR . 'we_tab.css') .
	we_html_element::jsScript(JS_DIR . 'initTabs.js') .
	we_html_element::jsScript(JS_DIR . 'update/updatelog.js'), we_html_element::htmlBody([
		'id' => 'eHeaderBody',
		'onload' => 'weTabs.setFrameSize();',
		'onresize' => 'weTabs.setFrameSize()'
		], '<div id="main">' . $tabs->getHTML() . '</div>')
);
