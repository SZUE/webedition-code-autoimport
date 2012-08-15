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
class deleteProgressDialog{

	function main(){

		$WE_PB = new we_progressBar(0, 0, true);
		$WE_PB->setStudLen(490);
		$WE_PB->addText("", 0, "pb1");
		$js = $WE_PB->getJSCode();
		$pb = $WE_PB->getHTML();

		$cancelButton = we_button::create_button("cancel", "javascript:top.close();");
		$pb = we_html_tools::htmlDialogLayout($pb, g_l('delete', "[delete]"), $cancelButton);

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(
					STYLESHEET .
					$js) .
				we_html_element::htmlBody(array(
					"class" => "weDialogBody"
					), $pb
				)
		);
	}

	function frameset(){
		$body = we_html_element::htmlBody(array('style' => 'background-color:grey;margin: 0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;', "onload" => "delcmd.location='" . WEBEDITION_DIR . "delFrag.php?frame=cmd" . (isset($_REQUEST["table"]) ? ("&amp;table=" . rawurlencode($_REQUEST["table"])) : "") . "&currentID=" . rawurlencode($_REQUEST["currentID"]) . "';")
				, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
					, we_html_element::htmlIFrame('delmain', WEBEDITION_DIR . "delFrag.php?frame=main", 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;overflow: hidden') .
					we_html_element::htmlIFrame('delcmd', HTML_DIR . "white.html", 'position:absolute;bottom:0px;height:0px;left:0px;right:0px;overflow: hidden;')
				));


		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(
					we_html_tools::getHtmlInnerHead(g_l('delete', "[delete]")) .
					we_html_element::jsScript(JS_DIR . "we_showMessage.js") . STYLESHEET
				) . $body);
	}

	function cmd(){
		if(isset($_SESSION["backup_delete"]) && $_SESSION["backup_delete"]){
			$taskname = md5(session_id() . "_backupdel");
			$fr = new delBackup($taskname, 1, 0);
		} else{
			$taskname = md5(session_id() . "_del");
			$table = (isset($_REQUEST["table"]) && $_REQUEST["table"]) ? $_REQUEST["table"] : FILE_TABLE;
			$fr = new delFragment($taskname, 1, 0, $table);
		}
	}

}