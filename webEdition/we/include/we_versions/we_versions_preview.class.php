<?php

/**
 * webEdition CMS
 *
 * $Rev: 12331 $
 * $Author: mokraemer $
 * $Date: 2016-06-24 20:36:16 +0200 (Fr, 24. Jun 2016) $
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
class we_versions_preview{
	private $db;
	private $document = [];
	private $newDoc;
	private $oldDoc;
	private $compareID;
	private $isObj = false;
	private $isTempl = false;
	private $contentDiff = '';
	private $contentNew = '';
	private $contentOld = '';
	private static $notmark = array(
		'timestamp',
		'version'
	);
	private static $notshow = array(
		'ID',
		'documentElements',
		'documentScheduler',
		'documentCustomFilter',
		'documentTable',
		'binaryPath',
		'ContentType',
		'modifications',
		'IP',
		'Browser',
		'CreationDate',
		'Path',
		'ClassName',
		'TableID',
		'ObjectID',
		'IsClassFolder',
		'IsNotEditable',
		'active'
	);

	public function __construct(){
		$this->db = $GLOBALS['DB_WE'];
		$this->document = [
			'table' => we_base_request::_(we_base_request::TABLE, 'we_cmd', 0, 1),
			'ID' => we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2),
			'version' => we_base_request::_(we_base_request::INT, 'we_cmd', 0, 3),
		];

		$this->newDoc = we_versions_version::loadVersion(' WHERE documentTable="' . $this->document['table'] . '" AND documentID=' . $this->document['ID'] . ' AND version=' . $this->document['version']);

		$this->compareID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 4);
		$this->oldDoc = we_versions_version::loadVersion(
				' WHERE documentTable="' . $this->db->escape($this->newDoc['documentTable']) . '" AND documentID=' . intval($this->newDoc['documentID']) .
				($this->compareID ?
					' AND version=' . $this->compareID :
					' AND version<' . intval($this->newDoc['version']) . ' ORDER BY version DESC LIMIT 1'));


		switch($this->newDoc['ContentType']){
			case we_base_ContentTypes::TEMPLATE:
				$this->isTempl = true;
				break;
			case we_base_ContentTypes::OBJECT_FILE:
				$this->isObj = true;
				break;
		}
		$this->contentDiff = $this->getPropChanges() .
			$this->getElementsDiff() .
			$this->getChangesScheduler() .
			$this->getChangesCustomerFilter();
	}

	private function getTabsBody(){
		$we_tabs = new we_tabs();
		$we_tabs->addTab(new we_tab(g_l('versions', '[versionDiffs]'), false, "setTab(1);", array("id" => "tab_1")));
		if(!$this->isObj){
			$we_tabs->addTab(new we_tab(g_l('versions', '[previewVersionNew]'), false, "setTab(2);", array("id" => "tab_2")));
		}
		if(!empty($this->oldDoc) && !$this->isObj){
			$we_tabs->addTab(new we_tab(g_l('versions', '[previewVersionOld]'), false, "setTab(3);", array("id" => "tab_3")));
		}

		return $we_tabs->getHTML() . we_html_element::jsElement('
if(!activ_tab){
 activ_tab = 1;
}
document.getElementById("tab_"+activ_tab).className="tabActive";');
	}

	private function getChangesCustomerFilter(){
		$contentDiff = '<table class="propDiff"><thead>
<tr>
	<td colspan="3" class="defaultfont bold">' . g_l('versions', '[customerMod]') . '</td>
</tr></thead>';

		if($this->newDoc['documentCustomFilter']){
			$newCustomFilter = we_unserialize((substr_compare($this->newDoc['documentCustomFilter'], 'a%3A', 0, 4) == 0 ?
					html_entity_decode(urldecode($this->newDoc['documentCustomFilter']), ENT_QUOTES) :
					$this->newDoc['documentCustomFilter'])
			);
		} else {
			$newCustomFilter = [];
		}
		if(isset($this->oldDoc['documentCustomFilter'])){
			if($this->oldDoc['documentCustomFilter']){
				$oldCustomFilter = we_unserialize((substr_compare($this->oldDoc['documentCustomFilter'], 'a%3A', 0, 4) == 0 ?
						html_entity_decode(urldecode($this->oldDoc['documentCustomFilter']), ENT_QUOTES) :
						$this->oldDoc['documentCustomFilter'])
				);
			} else {
				$oldCustomFilter = [];
			}
		}

		$mark = "";

		if(empty($newCustomFilter) && empty($oldCustomFilter)){
			$contentDiff .= '<tr><td>-</td></tr>';
		} elseif(empty($newCustomFilter) && !empty($oldCustomFilter)){

			foreach($oldCustomFilter as $key => $val){

				$name = g_l('versions', '[' . $key . ']');
				$newVal = '';
				if(!is_array($val)){
					$oldVal = we_versions_version::showValue($key, $val, $this->oldDoc['documentTable']);
				} else {
					$oldVal = (is_array($val) ?
							we_versions_version::showValue($key, $val, $this->oldDoc['documentTable']) :
							'');
				}

				$contentDiff .= '<tr>
	<td style="' . $mark . '"><strong>' . $name . '</strong></td>
	<td style="' . $mark . 'border-right:1px solid #000;">' . $newVal . '</td>' .
					(empty($this->oldDoc) ? '' : '<td style="' . $mark . '">' . $oldVal . '</td>') . '
</tr>';
			}
		} else {
			foreach($newCustomFilter as $key => $val){

				$name = g_l('versions', '[' . $key . ']');

				$mark = false;

				if(!is_array($val)){
					$newVal = we_versions_version::showValue($key, $val, $this->newDoc['documentTable']);
					if(!empty($oldCustomFilter)){
						$oldVal = (!is_array($oldCustomFilter[$key]) ?
								we_versions_version::showValue($key, $oldCustomFilter[$key], $this->oldDoc['documentTable']) :
								'');

						if($newVal != $oldVal){
							$mark = true;
						}
					} else {
						$oldVal = '';
					}
				} else {
					$newVal = we_versions_version::showValue($key, $val, $this->newDoc['documentTable']);
					if(!empty($oldCustomFilter)){
						$oldVal = (isset($oldCustomFilter[$key]) && is_array($oldCustomFilter[$key]) ?
								we_versions_version::showValue($key, $oldCustomFilter[$key], $this->oldDoc['documentTable']) :
								'');

						if($newVal != $oldVal){
							$mark = true;
						}
					} else {
						$oldVal = '';
					}
				}

				$contentDiff .= '<tr' . ($mark ? ' class="changedElement"' : '') . '>
	<td><strong>' . $name . '</strong></td>
	<td>' . $newVal . '</td>' .
					(empty($this->oldDoc) ? '' : '<td>' . $oldVal . '</td>') . '
</tr>';
			}
		}

		return $contentDiff . '</table>';
	}

	private function getChangesScheduler(){
		//scheduler
		$contentDiff = '<table class="propDiff"><thead>
<tr>
	<td colspan="3" class="defaultfont bold">' . g_l('versions', '[schedulerMod]') . '</td>
</tr></thead>';

		if($this->newDoc['documentScheduler']){
			$newDocScheduler = $this->newDoc['documentScheduler'] ? we_unserialize((substr_compare($this->newDoc['documentScheduler'], 'a%3A', 0, 4) == 0 ?
						html_entity_decode(urldecode($this->newDoc['documentScheduler']), ENT_QUOTES) :
						$this->newDoc['documentScheduler'])
				) :
				[];
		} else {
			$newDocScheduler = [];
		}
		if(isset($this->oldDoc['documentScheduler'])){
			if($this->oldDoc['documentScheduler']){
				$oldDocScheduler = $this->oldDoc['documentScheduler'] ? we_unserialize((substr_compare($this->oldDoc['documentScheduler'], 'a%3A', 0, 4) == 0 ?
							html_entity_decode(urldecode($this->oldDoc['documentScheduler']), ENT_QUOTES) :
							$this->oldDoc['documentScheduler'])
					) :
					[];
			} else {
				$oldDocScheduler = [];
			}
		}

		if(empty($newDocScheduler) && empty($oldDocScheduler)){
			$contentDiff .= '<tr><td>-</td></tr>';
		} elseif(empty($newDocScheduler) && !empty($oldDocScheduler)){

			foreach($oldDocScheduler as $k => $v){
				$number = $k + 1;
				$contentDiff .= '<tr>
	<td style="background-color:#FFF; "><strong>' . g_l('versions', '[scheduleTask]') . ' ' . $number . '</strong></td>
	<td style="background-color:#FFF;"></td>
	<td style="background-color:#FFF;"></td>
</tr>';

				foreach($v as $key => $val){
					$name = g_l('versions', '[' . $key . ']');
					$newVal = '';
					if(!is_array($val)){
						$oldVal = we_versions_version::showValue($key, $val, $this->oldDoc['documentTable']);
					} else {
						$oldVal = (is_array($val) ?
								we_versions_version::showValue($key, $val, $this->oldDoc['documentTable']) :
								'');
					}


					$contentDiff .= '<tr>
	<td ><strong>' . $name . '</strong></td>
	<td >' . $newVal . '</td>
	<td >' . $oldVal . '</td>
</tr>';
				}
			}
		} else {
			foreach($newDocScheduler as $k => $v){
				$number = $k + 1;

				$contentDiff .= '<tr>
	<td style="background-color:#FFF; "><strong>' . g_l('versions', '[scheduleTask]') . ' ' . $number . '</strong></td>
	<td style="background-color:#FFF;"></td>' .
					(empty($this->oldDoc) ? '' : '<td style="background-color:#FFF;"></td>') . '
</tr>';


				foreach($v as $key => $val){
					$mark = false;
					$name = g_l('versions', '[' . $key . ']');

					if(!is_array($val)){
						$newVal = we_versions_version::showValue($key, $val, $this->newDoc['documentTable']);

						if(!empty($oldDocScheduler)){
							$oldVal = (isset($oldDocScheduler[$k][$key]) && !is_array($oldDocScheduler[$k][$key]) ?
									we_versions_version::showValue($key, $oldDocScheduler[$k][$key], $this->oldDoc['documentTable']) :
									'');

							if($newVal != $oldVal){
								$mark = true;
							}
						} else {
							$oldVal = '';
						}
					} else {
						$newVal = we_versions_version::showValue($key, $val, $this->newDoc['documentTable']);
						if(!empty($oldDocScheduler)){
							$oldVal = (isset($oldDocScheduler[$k][$key]) && is_array($oldDocScheduler[$k][$key]) ?
									we_versions_version::showValue($key, $oldDocScheduler[$k][$key], $this->oldDoc['documentTable']) :
									'');

							if($newVal != $oldVal){
								$mark = true;
							}
						} else {
							$oldVal = '';
						}
					}

					$contentDiff .= '<tr' . ($mark ? ' class="changedElement"' : '') . '>
	<td ><strong>' . $name . '</strong></td>
	<td >' . $newVal . '</td>' .
						(empty($this->oldDoc) ? '' : '<td>' . $oldVal . '</td>') . '
</tr>';
				}
			}
		}

		return $contentDiff . '</table>';
	}

	private function getElementsDiff(){
		$contentDiff = '<table><thead>
		<tr>
		<td colspan="3" class="defaultfont bold">' . g_l('versions', '[contentElementsMod]') .
			'</td></tr></thead>';
		if($this->newDoc['documentElements']){
			$newDocElements = we_unserialize((substr_compare($this->newDoc['documentElements'], 'a%3A', 0, 4) == 0 ?
					html_entity_decode(urldecode($this->newDoc['documentElements']), ENT_QUOTES) :
					$this->newDoc['documentElements'])
			);
		} else {
			$newDocElements = [];
		}

		if(isset($this->oldDoc['documentElements'])){
			if($this->oldDoc['documentElements']){
				$oldDocElements = we_unserialize((substr_compare($this->oldDoc['documentElements'], 'a%3A', 0, 4) == 0 ?
						html_entity_decode(urldecode($this->oldDoc['documentElements']), ENT_QUOTES) :
						$this->oldDoc['documentElements'])
				);
			} else {
				$oldDocElements = [];
			}
		}
		if($newDocElements){
			foreach($newDocElements as $k => $v){
				$name = $k;
				$oldVersion = true;
				//skip this value - it is of no interest; everything is in data
				if($this->isTempl && $k === 'completeData'){
					continue;
				}

				$newVal = ($k == we_base_constants::WE_VARIANTS_ELEMENT_NAME ?
						we_versions_version::showValue($k, $newDocElements[$k]['dat']) :
						(!empty($v['dat']) ? $v['dat'] : '')
					);

				$mark = false;
				if($this->oldDoc){

					if($k === 'weInternVariantElement' && isset($oldDocElements[$k]['dat'])){
						$oldVal = we_versions_version::showValue($k, $oldDocElements[$k]['dat']);
					} elseif(!empty($oldDocElements[$k]['dat'])){
						$oldVal = $oldDocElements[$k]['dat'];
					} else {
						$oldVal = '';
					}

					if($newVal != $oldVal){
						$mark = true;
					}
				} else {
					$oldVersion = false;
					$oldVal = '';
				}

				//make sure none of them is an array
				if(is_array($newVal)){
					$newVal = print_r($newVal, true);
				}
				if(is_array($oldVal)){
					$oldVal = print_r($oldVal, true);
				}

				//if one of them contains newlines, format it as pre-block
				if(true || $this->isTempl){
					if(preg_match("/(%0A|%0D|\\n+|\\r+)/i", $newVal) || preg_match("/(%0A|%0D|\\n+|\\r+)/i", $oldVal)){
						$pre = '<pre style="font-size:0.9em;overflow:auto;">';
						$div = '';
					} else {
						$pre = '';
						$div = '<div style="width:400px;overflow:auto">';
					}
				} else {
					$pre = $div = '';
				}

				$contentDiff .= '<tr' . ($mark ? ' class="changedElement"' : '') . '>
<td><strong>' . $name . '</strong></td>';
				if($pre){
					$oldVal = explode("\n", str_replace("\r", "\n", str_replace("\r\n", "\n", $oldVal)));
					$newVal = explode("\n", str_replace("\r", "\n", str_replace("\r\n", "\n", $newVal)));
					$diff = new Horde_Text_Diff('Native', [($oldVersion ? $oldVal : []), is_array($newVal) ? $newVal : []]);
					$renderer = new Horde_Text_Diff_Renderer_Inline(['ins_prefix' => '<span class="insA">+<span class="bold insB">', 'ins_suffix' => '</span>+</span>',
						'del_prefix' => '<span class="delA">-<span class="bold delB">-', 'del_suffix' => '</span>-</span>',]);

					$text = $renderer->render($diff);

					$contentDiff .= '<td colspan="2" >' . $pre . $text . '</pre></td>';
				} else {
					if($newVal && $k != 'weInternVariantElement'){
						$newVal = oldHtmlspecialchars($newVal);
					}

					$contentDiff .= '<td>' . $div . $pre . $newVal . ($pre ? '</pre>' : '') . ($div ? '</div>' : '') . '</td>';
					if($oldVersion){
						if($oldVal && $k != 'weInternVariantElement'){
							$oldVal = oldHtmlspecialchars($oldVal);
						}
						$contentDiff .= '<td>' . $div . $pre . $oldVal . ($pre ? '</pre>' : '') . ($div ? '</div>' : '') . '</td>';
					}
				}
				$contentDiff .= '</tr>';
			}
		}

		return $contentDiff . '</table>';
	}

	private function getPropChanges(){
		$contentDiff = '';


		if(!($this->isObj || $this->isTempl)){
			//get path of preview-file
			$binaryPathNew = $this->newDoc['binaryPath']? :
				f('SELECT binaryPath FROM ' . VERSIONS_TABLE . " WHERE binaryPath!='' AND version<" . intval($this->newDoc['version']) . ' AND documentTable="' . $this->db->escape($this->newDoc['documentTable']) . '" AND documentID=' . intval($this->newDoc['documentID']) . ' ORDER BY version DESC LIMIT 1');

			if($this->oldDoc){
				$binaryPathOld = $this->oldDoc['binaryPath']? :
					f('SELECT binaryPath FROM ' . VERSIONS_TABLE . " WHERE binaryPath!='' AND version<" . intval($this->oldDoc['version']) . ' AND documentTable="' . $this->db->escape($this->oldDoc['documentTable']) . '" AND documentID=' . intval($this->oldDoc['documentID']) . ' ORDER BY version DESC LIMIT 1');
			}

			$filePathNew = $_SERVER['DOCUMENT_ROOT'] . VERSION_DIR . $binaryPathNew;
			$fileNew = $binaryPathNew;
			if($this->oldDoc){
				$fileOld = $binaryPathOld;
			}

			if(!file_exists($filePathNew) && isset($fileOld)){
				$fileNew = $fileOld;
			}
		}

		if(!($this->isObj || $this->isTempl)){
			$this->contentNew = '<iframe name="previewNew" src="' . WEBEDITION_DIR . 'showTempFile.php?file=' . str_replace(WEBEDITION_DIR, '', VERSION_DIR . $fileNew) . '&charset=' . $this->newDoc['Charset'] . '" style="border:0px;width:100%;height:100%;overflow: hidden;"></iframe>';
		}
		if($this->isTempl){
			$nDocElements = ($this->newDoc['documentElements'] ?
					we_unserialize((substr_compare($this->newDoc['documentElements'], 'a%3A', 0, 4) == 0 ?
							html_entity_decode(urldecode($this->newDoc['documentElements']), ENT_QUOTES) :
							$this->newDoc['documentElements'])
					) :
					[]);
			$this->contentNew = '<textarea style="width:99%;height:99%">' . ($nDocElements ? $nDocElements['data']['dat'] : '') . '</textarea>';
		}

		if(!empty($this->oldDoc) && !($this->isObj || $this->isTempl)){
			$this->contentOld = '<iframe name="previewOld" src="' . WEBEDITION_DIR . 'showTempFile.php?file=' . str_replace(WEBEDITION_DIR, '', VERSION_DIR . $fileOld) . '&charset=' . $this->oldDoc['Charset'] . '" style="border:0px;width:100%;height:100%;overflow: hidden;"></iframe>';
		}

		if(!empty($this->oldDoc) && $this->isTempl){
			$oDocElements = ($this->oldDoc['documentElements'] ?
					we_unserialize((substr_compare($this->oldDoc['documentElements'], 'a%3A', 0, 4) == 0 ?
							html_entity_decode(urldecode($this->oldDoc['documentElements']), ENT_QUOTES) :
							$this->oldDoc['documentElements'])
					) :
					[]);
			$this->contentOld = '<textarea style="width:99%;height:99%">' . ($oDocElements ? $oDocElements['data']['dat'] : '') . '</textarea>';
		}

		$versions_time_days = new we_html_select(array(
			'name' => 'versions_time_days',
			'style' => '',
			'class' => 'weSelect',
			'onchange' => "previewVersion('" . $this->document['table'] . "'," . $this->document['ID'] . "," . $this->document['version'] . ", this.value);"
			)
		);

		$versionOld = '';
		if(!empty($this->oldDoc)){
			$versionOld = ' AND version!=' . intval($this->oldDoc['version']);
		}
		$this->db->query('SELECT version AS ID,version, FROM_UNIXTIME(timestamp,"' . g_l('weEditorInfo', '[mysql_date_format]') . '") AS timestamp FROM ' . VERSIONS_TABLE . ' WHERE documentID=' . intval($this->newDoc['documentID']) . ' AND documentTable="' . $this->db->escape($this->newDoc['documentTable']) . '" AND version!=' . intval($this->newDoc['version']) . ' ' . $versionOld . "  ORDER BY version ASC");
		$versions = $this->db->getAllFirst(true, MYSQL_ASSOC);

		$versions_time_days->addOption('', g_l('versions', '[pleaseChoose]'));
		foreach($versions as $k => $v){
			$txt = g_l('versions', '[version]') . ' ' . $v['version'] . " " . g_l('versions', '[from]') . ' ' . $v['timestamp'];
			$versions_time_days->addOption($k, $txt);
		}

		$contentDiff = '<div id="top">' . g_l('versions', '[VersionChangeTxt]') . '<br/><br/>' .
			g_l('versions', '[VersionNumber]') . " " . $versions_time_days->getHtml() . '
			<div style="margin:20px 0px 0px 0px;" class="defaultfont"><a href="javascript:window.print()">' . g_l('versions', '[printPage]') . '</a></div>
			</div>
			<div id="topPrint">
					<strong>' . g_l('versions', '[versionDiffs]') . ':</strong><br/>
					<br/><strong>' . g_l('versions', '[Text]') . ':</strong> ' . $this->newDoc["Text"] . '
					<br/><strong>' . g_l('versions', '[documentID]') . ':</strong> ' . $this->newDoc["documentID"] . '
					<br/><strong>' . g_l('versions', '[path]') . ':</strong> ' . $this->newDoc["Path"] . '
			</div>
			<table class="propDiff">
			<thead><tr>
			<td></td>
	  		<td class="defaultfont bold">' . g_l('versions', '[VersionNew]') . '</td>' .
			(empty($this->oldDoc) ? '' :
				'<td class="defaultfont bold">' . g_l('versions', '[VersionOld]') . '</td>') .
			'</tr></thead>';

		foreach($this->newDoc as $k => $v){
			if(in_array($k, self::$notshow)){
				continue;
			}
			$name = g_l('versions', '[' . $k . ']');

			$oldVersion = true;
			$newVal = ($k === "ParentID" ?
					$this->newDoc['Path'] :
					we_versions_version::showValue($k, $this->newDoc[$k], $this->newDoc['documentTable'])
				);

			if($k === "Owners" && $this->newDoc[$k] == ""){
				$newVal = g_l('versions', '[CreatorID]');
			}

			$mark = false;
			if(!empty($this->oldDoc)){
				$oldVal = ($k === "ParentID" ?
						$this->oldDoc['Path'] :
						we_versions_version::showValue($k, $this->oldDoc[$k], $this->oldDoc['documentTable']));

				if($k === "Owners" && $this->oldDoc[$k] == ""){
					$oldVal = g_l('versions', '[CreatorID]');
				}
				if(!(in_array($k, self::$notmark))){
					if($newVal != $oldVal){
						$mark = true;
					}
				}
			} else {
				$oldVersion = false;
			}

			$contentDiff .= '<tr' . ($mark ? ' class="changedElement"' : '') . '>
<td><strong>' . $name . '</strong></td>
<td>' . $newVal . '</td>' .
				($oldVersion ? '<td>' . $oldVal . '</td>' : '') .
				'</tr>';
		}

		return $contentDiff . '</table>';
	}

	public function showHtml(){
		echo we_html_tools::getHtmlTop('webEdition - ' . g_l('versions', '[versioning]'), ($this->newDoc['Charset'] ? : DEFAULT_CHARSET), '', STYLESHEET .
			we_html_element::cssLink(CSS_DIR . 'we_version_preview.css') .
			we_tabs::getHeader('
var activ_tab = 1;

function toggle(id) {
	var elem = document.getElementById(id);
	elem.style.display = (elem.style.display == "none" ? "block" : "none");
}

function previewVersion(table,ID,version, newID) {
	top.opener.top.we_cmd("versions_preview", table,ID,version, newID);
}
function setTab(tab) {
	toggle("tab" + activ_tab);
	toggle("tab" + tab);
	activ_tab = tab;
}'), we_html_element::htmlBody(['class' => "weDialogBody"], '
	<div id="eHeaderBody">' . $this->getTabsBody() . '</div>
	<div id="content">
		<div id="tab1" style="display:block;width:100%;">' . $this->contentDiff . '</div>
		<div id="tab2" style="display:none;height:100%;width:100%">' . ($this->isObj ? '' : $this->contentNew) . '</div>
		<div id="tab3" style="display:none;height:100%;width:100%">' . ($this->isObj ? '' : $this->contentOld) . '</div>
	</div>

	<div class="editfooter">' . we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();") . '</div>
'));
	}

}
