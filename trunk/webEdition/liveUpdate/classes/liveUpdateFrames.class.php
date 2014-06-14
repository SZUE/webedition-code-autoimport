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


		$this->Section = weRequest('string', 'section', 'frameset');

		switch($this->Section){
			case 'frameset':
				// open frameset
				$this->Data['activeTab'] = $this->getValidTab(weRequest('raw', 'active', ''));

				break;
			case 'tabs':
				// frame with tabs
				$this->Data['activeTab'] = weRequest('raw', 'active', '');
				$this->Data['allTabs'] = $this->getAllTabs();
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
				return $this->htmlTabs();
			case 'frameset':
				return $this->htmlFrameset();
			case 'upgrade':
				return $this->htmlUpgrade();
			case 'beta':
				return $this->htmlBeta();
			case 'update':
				return $this->htmlUpdate();
			case 'modules':
				return $this->htmlModules();
			case 'languages':
				return $this->htmlLanguages();
			case 'updatelog':
				return $this->htmlUpdatelog();
			case 'connect':
				return $this->htmlConnect();
			case 'nextVersion':
				return $this->htmlNextVersion();
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
		if(($setTestUpdate = weRequest('bool', 'setTestUpdate', '-1')) !== '-1'){
			$conf = we_base_file::load(LIVEUPDATE_DIR . 'conf/conf.inc.php');

			if(strpos($conf, '$_REQUEST[\'testUpdate\']') !== false){
				if(strpos($conf, '$_REQUEST[\'testUpdate\'] = ' . intval(!$setTestUpdate) . ';') !== false){
					$conf = str_replace('$_REQUEST[\'testUpdate\'] = ' . intval(!$setTestUpdate) . ';', '$_REQUEST[\'testUpdate\'] = ' . intval($setTestUpdate) . ';', $conf);
					we_base_file::save(LIVEUPDATE_DIR . 'conf/conf.inc.php', $conf);
				}
			} else {
				$conf.='$_REQUEST[\'testUpdate\'] = ' . intval($setTestUpdate) . ';';
				we_base_file::save(LIVEUPDATE_DIR . 'conf/conf.inc.php', $conf);
			}
			$_REQUEST['testUpdate'] = $_REQUEST['setTestUpdate'];
		}
	}

	function processUpdateVariables(){
		$this->Data['lastUpdate'] = g_l('liveUpdate', '[update][neverUpdated]');

		if(($date = f('SELECT DATE_FORMAT(datum, "%d.%m.%y - %T ") FROM ' . UPDATE_LOG_TABLE . ' WHERE error=0 ORDER BY ID DESC LIMIT 1'))){
			$this->Data['lastUpdate'] = $date;
		}
	}

	function processDeleteLanguages(){
		$deletedLngs = array();
		$notDeletedLngs = array();

		if(($langs = weRequest('string', 'deleteLanguages'))){
			// update prefs_table
			$cond = '';

			foreach($langs as $lng){
				$cond .= ' OR Language="' . $GLOBALS['DB_WE']->escape($lng) . '"';
			}

			$GLOBALS['DB_WE']->query('UPDATE ' . PREFS_TABLE . ' SET value="' . WE_LANGUAGE . '" WHERE `key`="Language" AND ( 0 ' . $cond . ' )');

			$liveUpdateFunc = new liveUpdateFunctions();
			// delete folders
			foreach($langs as $lng){
				if(strpos($lng, "..") === false && $lng != ""){
					if($liveUpdateFunc->deleteDir(LIVEUPDATE_SOFTWARE_DIR . '/webEdition/we/include/we_language/' . $lng)){
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
		if(weRequest('int', 'start') === false){
			$_REQUEST['messages'] = true;
			$_REQUEST['notices'] = true;
			$_REQUEST['errors'] = true;
			$_REQUEST['start'] = 0;
		}

		$this->Data['amountPerPage'] = 5;

		$condition = ' WHERE 1 ' .
			(weRequest('bool', 'messages') ? '' : " AND error!=0") .
			(weRequest('bool', 'notices') ? '' : " AND error!=2") .
			(weRequest('bool', 'errors') ? '' : " AND error!=1");

		/*
		 * process update_cmd
		 */

		switch(weRequest('string', 'log_cmd', '')){
			case "deleteEntries":
				$GLOBALS['DB_WE']->query('DELETE FROM ' . UPDATE_LOG_TABLE . " $condition");
				$_REQUEST['start'] = 0;

				break;
			case "nextEntries":
				$_REQUEST['start'] += $this->Data['amountPerPage'];
				break;
			case "lastEntries":
				$_REQUEST['start'] -= $this->Data['amountPerPage'];
				break;
			default:
				$_REQUEST['start'] = 0;
				break;
		}

		if($_REQUEST['start'] < 0){
			$_REQUEST['start'] = 0;
		}

		/*
		 * Check if there are Log-Entries
		 */
		// complete amount

		$this->Data['amountMessages'] = 0;
		$this->Data['amountNotices'] = 0;
		$this->Data['amountErrors'] = 0;

		$this->Data['allEntries'] = 0;

		$this->Data['amountEntries'] = 0;

		$GLOBALS['DB_WE']->query('SELECT COUNT(ID) as amount, error FROM ' . UPDATE_LOG_TABLE . ' GROUP BY error');
		while($GLOBALS['DB_WE']->next_record()){

			$this->Data['allEntries'] += $GLOBALS['DB_WE']->f('amount');

			if($GLOBALS['DB_WE']->f('error') == 0){
				$this->Data['amountMessages'] = $GLOBALS['DB_WE']->f('amount');
				if(isset($_REQUEST['messages'])){
					$this->Data['amountEntries'] += $GLOBALS['DB_WE']->f('amount');
				}
			}
			if($GLOBALS['DB_WE']->f('error') == 1){
				$this->Data['amountErrors'] = $GLOBALS['DB_WE']->f('amount');
				if(isset($_REQUEST['errors'])){
					$this->Data['amountEntries'] += $GLOBALS['DB_WE']->f('amount');
				}
			}
			if($GLOBALS['DB_WE']->f('error') == 2){
				$this->Data['amountNotices'] = $GLOBALS['DB_WE']->f('amount');
				if(isset($_REQUEST['notices'])){
					$this->Data['amountEntries'] += $GLOBALS['DB_WE']->f('amount');
				}
			}
		}


		if($this->Data['allEntries']){

			/*
			 * There are entries available, get them
			 */
			$this->Data['logEntries'] = array();

			$GLOBALS['DB_WE']->query("SELECT DATE_FORMAT(datum, '%d.%m.%y&nbsp;/&nbsp;%H:%i') AS date, aktion, versionsnummer, error FROM " . UPDATE_LOG_TABLE . " $condition ORDER BY datum DESC LIMIT " . abs($_REQUEST['start']) . ", " . abs($this->Data['amountPerPage']));
			while(($row = $GLOBALS['DB_WE']->next_record())){

				$this->Data['logEntries'][] = array(
					'date' => $GLOBALS['DB_WE']->f('date'),
					'action' => $GLOBALS['DB_WE']->f('aktion'),
					'version' => $GLOBALS['DB_WE']->f('versionsnummer'),
					'state' => $GLOBALS['DB_WE']->f('error'),
				);
			}
		}
	}

	/**
	 * @return string
	 */
	function htmlFrameset(){

		$activeTab = liveUpdateFrames::getValidTab($this->Data['activeTab']);

		$show = "?section=$activeTab";
		$active = "&active=$activeTab";
		we_html_tools::headerCtCharset('text/html', $GLOBALS['WE_BACKENDCHARSET']);
		return we_html_tools::getHtmlTop('webEdition Update') . '
</head>
<frameset rows="30, *, 0" border="0" framespacing="0" frameborder="no">
	<frame name="updatetabs" src="' . $_SERVER['SCRIPT_NAME'] . '?section=tabs' . $active . '"  noresize scrolling="no" />
	<frame name="updatecontent" src="' . $_SERVER['SCRIPT_NAME'] . $show . '"  noresize scrolling="no" />
	<frame name="updateload" src="about:blank" />
</frameset>
</html>';
	}

	function htmlTabs(){
		include(LIVEUPDATE_TEMPLATE_DIR . 'tabs.inc.php');
	}

	function htmlUpgrade(){

		include(LIVEUPDATE_TEMPLATE_DIR . 'upgrade.inc.php');
	}

	function htmlBeta(){
		include(LIVEUPDATE_TEMPLATE_DIR . 'beta.inc.php');
	}

	function htmlUpdate(){
		include(LIVEUPDATE_TEMPLATE_DIR . 'update.inc.php');
	}

	function htmlNextVersion(){
		include(LIVEUPDATE_TEMPLATE_DIR . 'nextVersion.inc.php');
	}

	function htmlModules(){
		include(LIVEUPDATE_TEMPLATE_DIR . 'modules.inc.php');
	}

	function htmlLanguages(){
		include(LIVEUPDATE_TEMPLATE_DIR . 'languages.inc.php');
	}

	function htmlConnect(){
		include(LIVEUPDATE_TEMPLATE_DIR . 'connect.inc.php');
	}

	function htmlConnectionSuccess($errorMessage = ''){
		include(LIVEUPDATE_TEMPLATE_DIR . 'connectSuccess.inc.php');
	}

	function htmlConnectionError(){
		include(LIVEUPDATE_TEMPLATE_DIR . 'connectError.inc.php');
	}

	function htmlStateMessage(){
		include(LIVEUPDATE_TEMPLATE_DIR . 'stateMessage.inc.php');
	}

	function htmlUpdatelog(){
		include(LIVEUPDATE_TEMPLATE_DIR . 'updatelog.inc.php');
	}

	function getValidTab($showTab = ''){
		if(in_array($showTab, $GLOBALS['updatecmds'])){
			return $showTab;
		}
		return $GLOBALS['updatecmds'][0];
	}

	function getAllTabs(){
		return $GLOBALS['updatecmds'];
	}

}
