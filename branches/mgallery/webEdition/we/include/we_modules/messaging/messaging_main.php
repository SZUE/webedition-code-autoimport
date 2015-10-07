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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();
$transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction');
if(!$transaction){
	exit();
}

echo we_html_tools::getHtmlTop() .
 we_modules_frame::getJSToggleTreeCode('messaging');
$_treewidth = isset($_COOKIE["treewidth_messaging"]) && ($_COOKIE["treewidth_messaging"] >= weTree::MinWidth) ? $_COOKIE["treewidth_users"] : 204;

$_incDecTree = '<div id="baumArrows">
	<div class="baumArrow" id="incBaum" ' . ($this->treeWidth <= 30 ? 'style="background-color: grey"' : '') . ' onclick="top.content.incTree();"><i class="fa fa-plus"></i></div>
	<div class="baumArrow" id="decBaum" ' . ($this->treeWidth <= 30 ? 'style="background-color: grey"' : '') . ' onclick="top.content.decTree();"><i class="fa fa-minus"></i></div>
	<div class="baumArrow" onclick="top.content.toggleTree();"><i id="arrowImg" class="fa fa-lg fa-caret-' . ($this->treeWidth <= 30 ? "right" : "left") . '" ></i></div>
</div>
</div>';

echo we_html_element::htmlBody(array('style' => 'background-color:#bfbfbf; background-repeat:repeat;'), we_html_element::htmlDiv(array('style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;'), we_html_element::htmlDiv(array('id' => 'lframeDiv', 'style' => 'position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;width: ' . $_treewidth . 'px;'), we_html_element::htmlDiv(array('id' => 'vtabs', 'style' => 'width: ' . weTree::HiddenWidth . 'px;'), $incDecTree) .
			we_html_element::htmlIFrame('messaging_tree', 'about:blank', 'position: absolute; top: 0px; bottom: 0px; left: ' . weTree::HiddenWidth . 'px; right: 0px; border-top: 1px solid white;')
		) .
		we_html_element::htmlIFrame('right', WE_MESSAGING_MODULE_DIR . 'messaging_right.php?we_transaction=' . $transaction, 'position: absolute; top: 0px; bottom: 0px; left: ' . $_treewidth . 'px; right: 0px; width:auto; border-left: 1px solid black; overflow: hidden;')
	)
);
