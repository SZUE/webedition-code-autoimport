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
class we_backup_delete extends we_fragment_base{
	private $db;

	function __construct($name, $taskPerFragment, $pause = 0){
		$this->db = new DB_WE();
		parent::__construct($name, $taskPerFragment, $pause);
	}

	function init(){
		if(!empty($_SESSION['weS']['backup_delete'])){

			$this->db->query('SELECT ContentType,Path FROM ' . FILE_TABLE . ' ORDER BY IsFolder, CHAR_LENGTH(Path) DESC');
			while($this->db->next_record()){
				$this->alldata[] = array($_SERVER['DOCUMENT_ROOT'] . $this->db->f("Path"), $this->db->f("ContentType"));
				$this->alldata[] = array($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $this->db->f("Path"), $this->db->f("ContentType"));
			}
			$this->db->query('SELECT ContentType,Path FROM ' . TEMPLATES_TABLE . ' ORDER BY IsFolder, CHAR_LENGTH(Path) DESC');
			while($this->db->next_record()){
				$this->alldata[] = array(TEMPLATES_PATH . '/' . preg_replace('/\.tmpl$/i', '.php', $this->db->f("Path")), $this->db->f("ContentType"));
			}

			if(!$this->alldata){
				echo we_html_element::jsElement(
					we_message_reporting::getShowMessageCall(g_l('backup', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_WARNING)
				);
				$this->finish();
			}
		}
	}

	function doTask(){
		if(!we_base_file::delete($this->data[0])){
			if(file_exists($this->data[0])){
				$_SESSION['weS']['delete_files_nok'][] = array(
					"ContentType" => $this->data[1],
					"path" => $this->data[0]
				);
			}
		}
		$percent = round((100 / count($this->alldata)) * (1 + $this->currentTask));
		$text = str_replace($_SERVER['DOCUMENT_ROOT'], "", we_base_file::clearPath($this->data[0]));
		if(strlen($text) > 75){
			$text = addslashes(substr($text, 0, 65) . '&hellip;' . substr($text, -10));
		}
		echo we_html_element::jsElement('
			parent.delmain.setProgressText("pb1","' . sprintf(g_l('backup', '[delete_entry]'), $text) . '");
			parent.delmain.setProgress(' . $percent . ');
		');
	}

	function finish(){
		if(!empty($_SESSION['weS']['delete_files_nok']) && is_array($_SESSION['weS']['delete_files_nok'])){
			echo we_html_element::jsScript(JS_DIR . "windows.js") .
			we_html_element::jsScript(JS_DIR . 'global.js').
				we_html_element::jsElement('
					new jsWindow("' . WEBEDITION_DIR . 'delInfo.php","we_delinfo",-1,-1,600,550,true,true,true);
			');
		}
		unset($_SESSION['weS']['backup_delete']);
		echo we_html_element::jsElement('top.close();');
	}

	function printHeader(){
		we_html_tools::protect();
		echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', ' ');
	}

}
