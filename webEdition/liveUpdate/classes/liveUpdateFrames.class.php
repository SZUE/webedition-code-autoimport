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

/**
 * This class deals with the frames for the liveUpdate
 * Not much functionality here, just show the requested frame
 */
class liveUpdateFrames{
	var $Section;
	var $Data;

	function __construct(){

		// depending on section variables execute different stuff to gather
		// data for the frame


		$this->Section = we_base_request::_(we_base_request::STRING, 'section', 'frameset');

		switch($this->Section){
			case 'frameset':
				// open frameset
				$this->Data['activeTab'] = $this->getValidTab(we_base_request::_(we_base_request::STRING, 'active', ''));

				break;
			case 'tabs':
				// frame with tabs
				$this->Data['activeTab'] = we_base_request::_(we_base_request::STRING, 'active', '');
				break;

			case 'update':
				$this->processUpdateVariables();
				break;

			case 'beta':
				$this->processBeta();
				break;

			case 'updatelog':
				$this->processUpdateLogVariables();
				break;

			case 'languages':
				$this->processDeleteLanguages();
				break;
		}
	}

	function getFrame(){

		switch($this->Section){
			case 'tabs':
				include(LIVEUPDATE_TEMPLATE_DIR . 'tabs.inc.php');
				return '';
			case 'frameset':
				return $this->htmlFrameset();
			case 'upgrade':
				include(LIVEUPDATE_TEMPLATE_DIR . 'upgrade.inc.php');
				return '';
			case 'beta':
				include(LIVEUPDATE_TEMPLATE_DIR . 'beta.inc.php');
				return '';
			case 'update':
				include(LIVEUPDATE_TEMPLATE_DIR . 'update.inc.php');
				return '';
			case 'modules':
				return $this->htmlModules();
			case 'languages':
				return $this->htmlLanguages();
			case 'updatelog':
				include(LIVEUPDATE_TEMPLATE_DIR . 'updatelog.inc.php');
				return'';
			case 'connect':
				include(LIVEUPDATE_TEMPLATE_DIR . 'connect.inc.php');
				return '';
			default:
				echo "Frame $this->Section is not known!";
				return;
		}
	}

	function getData($name){
		if(isset($this->Data[$name])){
			return $this->Data[$name];
		}
	}

	function processBeta(){
		if(($setTestUpdate = we_base_request::_(we_base_request::BOOL, 'setTestUpdate', '-1')) !== '-1'){
			$_SESSION['weS']['testUpdate'] = $setTestUpdate;
		}
	}

	function processUpdateVariables(){
		$this->Data['lastUpdate'] = g_l('liveUpdate', '[update][neverUpdated]');

		if(($date = f('SELECT DATE_FORMAT(datum, "%d.%m.%y - %T ") FROM ' . UPDATE_LOG_TABLE . ' WHERE error=0 ORDER BY ID DESC LIMIT 1'))){
			$this->Data['lastUpdate'] = $date;
		}
	}

	function processDeleteLanguages(){
		$deletedLngs = [];
		$notDeletedLngs = [];

		if(($langs = we_base_request::_(we_base_request::STRING, 'deleteLanguages'))){
			// update prefs_table

			$GLOBALS['DB_WE']->query('UPDATE ' . PREFS_TABLE . ' SET value="' . WE_LANGUAGE . '" WHERE `key`="Language" AND value IN ("","' . implode('","', array_map('escape_sql_query', $langs)) . '")');

			// delete folders
			foreach($langs as $lng){
				if(strpos($lng, "..") === false && $lng != ""){
					if(we_base_file::deleteLocalFolder(LIVEUPDATE_SOFTWARE_DIR . '/webEdition/we/include/we_language/' . $lng, true, true)){
						$deletedLngs[] = $lng;
					} else {
						$notDeletedLngs[] = $lng;
					}
				}
			}
		}
		$this->Data['deletedLngs'] = $deletedLngs;
		$this->Data['notDeletedLngs'] = $notDeletedLngs;
	}

	function processUpdateLogVariables(){

		$this->Data['amountPerPage'] = 5;
		$show = [
			'msg' => we_base_request::_(we_base_request::BOOL, 'messages', false),
			'err' => we_base_request::_(we_base_request::BOOL, 'errors', false),
			'notice' => we_base_request::_(we_base_request::BOOL, 'notices', false),
		];
		$errors = [];
		$tmp = 0;
		foreach($show as $cur){
			if($cur){
				$errors[] = $tmp;
			}
			$tmp++;
		}
		$condition = ' WHERE ' . ($errors ? ' error IN (' . implode(',', $errors) . ')' : ' FALSE ');

		/*
		 * process update_cmd
		 */

		switch(we_base_request::_(we_base_request::STRING, 'log_cmd', '')){
			case "deleteEntries":
				$GLOBALS['DB_WE']->query('DELETE FROM ' . UPDATE_LOG_TABLE . $condition);
				$start = 0;
				break;
			case "nextEntries":
				$start = we_base_request::_(we_base_request::INT, 'start', 0) + $this->Data['amountPerPage'];
				break;
			case "lastEntries":
				$start = we_base_request::_(we_base_request::INT, 'start', 0) - $this->Data['amountPerPage'];
				break;
			default:
				$start = 0;
				break;
		}

		/*
		 * Check if there are Log-Entries
		 */
		// complete amount

		$this->Data['amountMessages'] = $this->Data['amountNotices'] = $this->Data['amountErrors'] = $this->Data['allEntries'] = $this->Data['amountEntries'] = 0;

		$GLOBALS['DB_WE']->query('SELECT COUNT(ID) as amount, error FROM ' . UPDATE_LOG_TABLE . ' GROUP BY error');
		while($GLOBALS['DB_WE']->next_record()){

			$this->Data['allEntries'] += $GLOBALS['DB_WE']->f('amount');

			if($GLOBALS['DB_WE']->f('error') == 0){
				$this->Data['amountMessages'] = $GLOBALS['DB_WE']->f('amount');
				if($show['msg']){
					$this->Data['amountEntries'] += $GLOBALS['DB_WE']->f('amount');
				}
			}
			if($GLOBALS['DB_WE']->f('error') == 1){
				$this->Data['amountErrors'] = $GLOBALS['DB_WE']->f('amount');
				if($show['err']){
					$this->Data['amountEntries'] += $GLOBALS['DB_WE']->f('amount');
				}
			}
			if($GLOBALS['DB_WE']->f('error') == 2){
				$this->Data['amountNotices'] = $GLOBALS['DB_WE']->f('amount');
				if($show['notice']){
					$this->Data['amountEntries'] += $GLOBALS['DB_WE']->f('amount');
				}
			}
		}
		$this->Data['start'] = max(0, $start);

		if($this->Data['allEntries']){

			/*
			 * There are entries available, get them
			 */
			$this->Data['logEntries'] = [];
			$GLOBALS['DB_WE']->query('SELECT DATE_FORMAT(datum, "' . str_replace(' ', '&nbsp;/&nbsp;', g_l('date', '[format][mysql]')) . '") AS date, aktion, versionsnummer, error FROM ' . UPDATE_LOG_TABLE . ' ' . $condition . ' ORDER BY datum DESC LIMIT ' . $this->Data['start'] . ',' . abs($this->Data['amountPerPage']));

			while(($row = $GLOBALS['DB_WE']->next_record())){
				$this->Data['logEntries'][] = [
					'date' => $GLOBALS['DB_WE']->f('date'),
					'action' => $GLOBALS['DB_WE']->f('aktion'),
					'version' => $GLOBALS['DB_WE']->f('versionsnummer'),
					'state' => $GLOBALS['DB_WE']->f('error'),
				];
			}
		}
	}

	/**
	 * @return string
	 */
	private function htmlFrameset(){
		$activeTab = self::getValidTab($this->Data['activeTab']);

		$show = "?section=$activeTab";
		$active = "&active=$activeTab";
		we_html_tools::headerCtCharset('text/html', $GLOBALS['WE_BACKENDCHARSET']);
		return we_html_tools::getHtmlTop('webEdition Update', '', 'frameset', ' ') . '<body style="overflow:hidden">' .
			we_html_element::htmlIFrame('updatetabs', $_SERVER['SCRIPT_NAME'] . '?section=tabs' . $active, 'position: absolute;top:0px;left:0px;right:0px;height:25px;', '', '', false) .
			we_html_element::htmlIFrame('updatecontent', $_SERVER['SCRIPT_NAME'] . $show, 'position: absolute;top:25px;left:0px;right:0px;bottom:0px;', '', '', false) .
			we_html_element::htmlIFrame('updateload', 'about:blank', 'display:none;', '', '', false) . '</body>
</html>';
	}

	function htmlLanguages(){
		include(LIVEUPDATE_TEMPLATE_DIR . 'languages.inc.php');
	}

	static function htmlConnectionSuccess($errorMessage = ''){
		include(LIVEUPDATE_TEMPLATE_DIR . 'connectSuccess.inc.php');
	}

	static function htmlConnectionError(){
		include(LIVEUPDATE_TEMPLATE_DIR . 'connectError.inc.php');
	}

	static function htmlStateMessage(){
		include(LIVEUPDATE_TEMPLATE_DIR . 'stateMessage.inc.php');
	}

	static function getValidTab($showTab = ''){
		if(in_array($showTab, $GLOBALS['updatecmds'])){
			return $showTab;
		}
		return $GLOBALS['updatecmds'][0];
	}

	public static function getJSLangConsts(){
		return 'WE().consts.g_l.liveUpdate=JSON.parse("' . setLangString([
				'confirmDelete' => g_l('liveUpdate', '[updatelog][confirmDelete]'),
				'languagesDeleted' => g_l('liveUpdate', '[languages][languagesDeleted]'),
				'languagesNotDeleted' => g_l('liveUpdate', '[languages][languagesNotDeleted]'),
				]) . '");';
	}

}
