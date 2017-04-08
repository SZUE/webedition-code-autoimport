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

	function __construct($name){
		$this->db = new DB_WE();
		parent::__construct($name, 10);
	}

	protected function init(){
		if(!empty($_SESSION['weS']['backup_delete'])){

			$this->db->query('SELECT ContentType,Path FROM ' . FILE_TABLE . ' ORDER BY IsFolder, CHAR_LENGTH(Path) DESC');
			while($this->db->next_record()){
				$this->alldata[] = [$_SERVER['DOCUMENT_ROOT'] . $this->db->f("Path"), $this->db->f("ContentType")];
				$this->alldata[] = [$_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $this->db->f("Path"), $this->db->f("ContentType")];
			}
			$this->db->query('SELECT ContentType,Path FROM ' . TEMPLATES_TABLE . ' ORDER BY IsFolder, CHAR_LENGTH(Path) DESC');
			while($this->db->next_record()){
				$this->alldata[] = [TEMPLATES_PATH . '/' . preg_replace('/\.tmpl$/i', '.php', $this->db->f("Path")), $this->db->f("ContentType")];
			}

			if(!$this->alldata){
				$jsCmd = new we_base_jsCmd();
				$jsCmd->addMsg(g_l('backup', '[nothing_to_delete]'), we_base_util::WE_MESSAGE_WARNING);
				$this->finish($jsCmd);
			}
		}
	}

	protected function doTask(){
		if(!we_base_file::delete($this->data[0])){
			if(file_exists($this->data[0])){
				$_SESSION['weS']['delete_files_nok'][] = [
					"ContentType" => $this->data[1],
					"path" => $this->data[0]
				];
			}
		}
	}

	protected function updateProgressBar(we_base_jsCmd $jsCmd){
		$text = str_replace($_SERVER['DOCUMENT_ROOT'], "", we_base_file::clearPath($this->data[0]));
		if(strlen($text) > 75){
			$text = substr($text, 0, 65) . '&hellip;' . substr($text, -10);
		}
		$jsCmd->addCmd('setProgress', [
			'progress' => ((int) ((100 / count($this->alldata)) * (1 + $this->currentTask))),
			'name' => 'pb1',
			'text' => sprintf(g_l('backup', '[delete_entry]'), $text),
			'win' => 'delmain'
		]);
	}

	protected function finish(we_base_jsCmd $jsCmd){
		if(!empty($_SESSION['weS']['delete_files_nok']) && is_array($_SESSION['weS']['delete_files_nok'])){
			$jsCmd->addCmd('delFilesNOK');
		}
		$jsCmd->addCmd('close');
		unset($_SESSION['weS']['backup_delete']);
	}

}
