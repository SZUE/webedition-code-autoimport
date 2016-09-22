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
abstract class we_dialog_deleteProgress{

	public static function main(){

		$WE_PB = new we_progressBar(0, 490);
		$WE_PB->addText("", 0, "pb1");

		$cancelButton = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();");
		$pb = we_html_tools::htmlDialogLayout($WE_PB->getHTML(), g_l('delete', '[delete]'), $cancelButton);

		return we_html_tools::getHtmlTop('', '', '', we_progressBar::getJSCode(), we_html_element::htmlBody(["class" => "weDialogBody"], $pb));
	}

	public static function getHTML($table, $currentID){
		return we_html_tools::getHtmlTop(g_l('delete', '[delete]'), '', '', '', we_html_element::htmlBody(['id' => 'weMainBody', "onload" => "delcmd.location=WE().consts.dirs.WEBEDITION_DIR+'delFrag.php?frame=cmd" . ($table ? ("&amp;table=" . rawurlencode($table)) : "") . "&currentID=" . $currentID . "';"]
					, we_html_element::htmlIFrame('delmain', WEBEDITION_DIR . "delFrag.php?frame=main", 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;overflow: hidden') .
					we_html_element::htmlIFrame('delcmd', "about:blank", 'position:absolute;bottom:0px;height:0px;left:0px;right:0px;overflow: hidden;')
				)
		);
	}

	public static function cmd(){
		if(!empty($_SESSION['weS']['backup_delete'])){
			$taskname = md5(session_id() . "_backupdel");
			new we_backup_delete($taskname, 1, 0);
		} else {
			$taskname = md5(session_id() . "_del");
			$table = we_base_request::_(we_base_request::TABLE, "table", FILE_TABLE);
			new we_fragment_del($taskname, $table);
		}
	}

}
