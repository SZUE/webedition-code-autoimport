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
//FIXME make this a class
$_db = $GLOBALS['DB_WE'];

function doNotShowFields($k){
	$notshow = array(
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

	return !(in_array($k, $notshow));
}

function doNotMarkFields($k){
	$notmark = array(
		'timestamp',
		'version'
	);

	return !(in_array($k, $notmark));
}

$ID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);

$newDoc = we_versions_version::loadVersion(' WHERE ID=' . intval($ID));

$compareID = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2);
$oldDoc = we_versions_version::loadVersion(($compareID ?
			' WHERE ID=' . $compareID :
			' WHERE version<' . intval($newDoc['version']) . ' AND documentTable="' . $_db->escape($newDoc['documentTable']) . '" AND documentID=' . intval($newDoc['documentID']) . ' ORDER BY version DESC LIMIT 1'));


$isObj = false;
$isTempl = false;
switch($newDoc['ContentType']){
	case we_base_ContentTypes::TEMPLATE:
		$isTempl = true;
		break;
	case we_base_ContentTypes::OBJECT_FILE:
		$isObj = true;
		break;
}
if(!($isObj OR $isTempl)){
	//get path of preview-file
	$binaryPathNew = $newDoc['binaryPath'];
	if(!$binaryPathNew){
		$binaryPathNew = f('SELECT binaryPath FROM ' . VERSIONS_TABLE . " WHERE binaryPath!='' AND version<" . intval($newDoc['version']) . ' AND documentTable="' . $_db->escape($newDoc['documentTable']) . '" AND documentID=' . intval($newDoc['documentID']) . ' ORDER BY version DESC LIMIT 1');
	}

	if($oldDoc){
		$binaryPathOld = $oldDoc['binaryPath'];
		if(!$binaryPathOld){
			$binaryPathOld = f('SELECT binaryPath FROM ' . VERSIONS_TABLE . " WHERE binaryPath!='' AND version<" . intval($oldDoc['version']) . ' AND documentTable="' . $_db->escape($oldDoc['documentTable']) . '" AND documentID=' . intval($oldDoc['documentID']) . ' ORDER BY version DESC LIMIT 1');
		}
	}

	$filePathNew = $_SERVER['DOCUMENT_ROOT'] . VERSION_DIR . $binaryPathNew;
	$fileNew = $binaryPathNew;
	if($oldDoc){
		$fileOld = $binaryPathOld;
	}

	if(!file_exists($filePathNew) && isset($fileOld)){
		$fileNew = $fileOld;
	}
}

$we_tabs = new we_tabs();

$we_tabs->addTab(new we_tab(g_l('versions', '[versionDiffs]'), '((activ_tab==1) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('1');", array("id" => "tab_1")));

if(!$isObj){
	$we_tabs->addTab(new we_tab(g_l('versions', '[previewVersionNew]'), '((activ_tab==2) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('2');", array("id" => "tab_2")));
}
if(!empty($oldDoc) && !$isObj){
	$we_tabs->addTab(new we_tab(g_l('versions', '[previewVersionOld]'), '((activ_tab==3) ? ' . we_tab::ACTIVE . ' : ' . we_tab::NORMAL . ')', "setTab('3');", array("id" => "tab_3")));
}

$pathLength = 40;

$tabsBody = $we_tabs->getHTML() . we_html_element::jsElement('
if(!activ_tab){
 activ_tab = 1;
}
document.getElementById("tab_"+activ_tab).className="tabActive";');

$contentNew = $contentOld = $contentDiff = '';

if(!($isObj || $isTempl)){
	$contentNew = '<iframe name="previewNew" src="' . WEBEDITION_DIR . 'showTempFile.php?file=' . str_replace(WEBEDITION_DIR, '', VERSION_DIR . $fileNew) . '&charset=' . $newDoc['Charset'] . '" style="border:0px;width:100%;height:100%;overflow: hidden;"></iframe>';
}
if($isTempl){
	$nDocElements = ($newDoc['documentElements'] ?
			we_unserialize((substr_compare($newDoc['documentElements'], 'a%3A', 0, 4) == 0 ?
					html_entity_decode(urldecode($newDoc['documentElements']), ENT_QUOTES) :
					$newDoc['documentElements'])
			) :
			array());
	$contentNew = '<textarea style="width:99%;height:99%">' . ($nDocElements ? $nDocElements['data']['dat'] : '') . '</textarea>';
}
if(!empty($oldDoc) && !($isObj || $isTempl)){
	$contentOld = '<iframe name="previewOld" src="' . WEBEDITION_DIR . 'showTempFile.php?file=' . str_replace(WEBEDITION_DIR, '', VERSION_DIR . $fileOld) .'&charset=' . $oldDoc['Charset'] . '" style="border:0px;width:100%;height:100%;overflow: hidden;"></iframe>';
}
if(!empty($oldDoc) && $isTempl){
	$oDocElements = ($oldDoc['documentElements'] ?
			we_unserialize((substr_compare($oldDoc['documentElements'], 'a%3A', 0, 4) == 0 ?
					html_entity_decode(urldecode($oldDoc['documentElements']), ENT_QUOTES) :
					$oldDoc['documentElements'])
			) :
			array());
	$contentOld = '<textarea style="width:99%;height:99%">' . ($oDocElements ? $oDocElements['data']['dat'] : '') . '</textarea>';
}
$_versions_time_days = new we_html_select(array(
	'name' => 'versions_time_days',
	'style' => '',
	'class' => 'weSelect',
	'onchange' => 'previewVersion(' . $ID . ', this.value);'
	)
);

$versionOld = '';
if(!empty($oldDoc)){
	$versionOld = ' AND version!=' . intval($oldDoc['version']);
}
$_db->query('SELECT ID,version, FROM_UNIXTIME(timestamp,"' . g_l('weEditorInfo', '[mysql_date_format]') . '") AS timestamp FROM ' . VERSIONS_TABLE . ' WHERE documentID=' . intval($newDoc['documentID']) . ' AND documentTable="' . $_db->escape($newDoc['documentTable']) . '" AND version!=' . intval($newDoc['version']) . ' ' . $versionOld . "  ORDER BY version ASC");
$versions = $_db->getAllFirst(true, MYSQL_ASSOC);

$_versions_time_days->addOption('', g_l('versions', '[pleaseChoose]'));
foreach($versions as $k => $v){
	$txt = g_l('versions', '[version]') . ' ' . $v['version'] . " " . g_l('versions', '[from]') . ' ' . $v['timestamp'];
	$_versions_time_days->addOption($k, $txt);
}

$contentDiff = '<div id="top">' . g_l('versions', '[VersionChangeTxt]') . '<br/><br/>' .
	g_l('versions', '[VersionNumber]') . " " . $_versions_time_days->getHtml() . '
			<div style="margin:20px 0px 0px 0px;" class="defaultfont"><a href="javascript:window.print()">' . g_l('versions', '[printPage]') . '</a></div>
			</div>
			<div id="topPrint">
					<strong>' . g_l('versions', '[versionDiffs]') . ':</strong><br/>
					<br/><strong>' . g_l('versions', '[Text]') . ':</strong> ' . $newDoc["Text"] . '
					<br/><strong>' . g_l('versions', '[documentID]') . ':</strong> ' . $newDoc["documentID"] . '
					<br/><strong>' . g_l('versions', '[path]') . ':</strong> ' . $newDoc["Path"] . '
			</div>
			<table class="propDiff">
			<thead><tr>
			<td></td>
	  		<td class="defaultfont bold">' . g_l('versions', '[VersionNew]') . '</td>' .
	(empty($oldDoc) ? '' :
		'<td class="defaultfont bold">' . g_l('versions', '[VersionOld]') . '</td>') .
	'</tr></thead>';

foreach($newDoc as $k => $v){
	if(!doNotShowFields($k)){
		continue;
	}
	$name = g_l('versions', '[' . $k . ']');

	$oldVersion = true;
	$newVal = ($k === "ParentID" ?
			$newDoc['Path'] :
			we_versions_version::showValue($k, $newDoc[$k], $newDoc['documentTable'])
		);

	if($k === "Owners" && $newDoc[$k] == ""){
		$newVal = g_l('versions', '[CreatorID]');
	}

	$mark = false;
	if(!empty($oldDoc)){
		$oldVal = ($k === "ParentID" ?
				$oldDoc['Path'] :
				we_versions_version::showValue($k, $oldDoc[$k], $oldDoc['documentTable']));

		if($k === "Owners" && $oldDoc[$k] == ""){
			$oldVal = g_l('versions', '[CreatorID]');
		}
		if(doNotMarkFields($k)){
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

$contentDiff .= '</table>';

//elements

$contentDiff .= '<table><thead>
		<tr>
		<td colspan="3" class="defaultfont bold">' . g_l('versions', '[contentElementsMod]') .
	'</td></tr></thead>';
if($newDoc['documentElements']){
	$newDocElements = we_unserialize((substr_compare($newDoc['documentElements'], 'a%3A', 0, 4) == 0 ?
			html_entity_decode(urldecode($newDoc['documentElements']), ENT_QUOTES) :
			$newDoc['documentElements'])
	);
} else {
	$newDocElements = array();
}

if(isset($oldDoc['documentElements'])){
	if($oldDoc['documentElements']){
		$oldDocElements = we_unserialize((substr_compare($oldDoc['documentElements'], 'a%3A', 0, 4) == 0 ?
				html_entity_decode(urldecode($oldDoc['documentElements']), ENT_QUOTES) :
				$oldDoc['documentElements'])
		);
	} else {
		$oldDocElements = array();
	}
}
if($newDocElements){
	foreach($newDocElements as $k => $v){
		$name = $k;
		$oldVersion = true;
		//skip this value - it is of no interest; everything is in data
		if($isTempl && $k === 'completeData'){
			continue;
		}

		$newVal = ($k == we_base_constants::WE_VARIANTS_ELEMENT_NAME ?
				we_versions_version::showValue($k, $newDocElements[$k]['dat']) :
				(!empty($v['dat']) ? $v['dat'] : '')
			);

		$mark = false;
		if($oldDoc){

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
		if(true || $isTempl){
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
			$diff = new Horde_Text_Diff('Native', array(($oldVersion ? $oldVal : array()), is_array($newVal) ? $newVal : array()));
			$renderer = new Horde_Text_Diff_Renderer_Inline(array('ins_prefix' => '###INS_START###', 'ins_suffix' => '###INS_END###',
				'del_prefix' => '###DEL_START###', 'del_suffix' => '###DEL_END###',));

			$text = str_replace('###INS_START###', '<span class="insA">+<span class="bold insB">', str_replace('###INS_END###', '</span>+</span>', str_replace('###DEL_END###', '</span>-</span>', str_replace('###DEL_START###', '<span class="delA">-<span class="bold delB">-', $renderer->render($diff)))));

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

$contentDiff .= '</table>' .
//scheduler
	'<table class="propDiff"><thead>
<tr>
	<td colspan="3" class="defaultfont bold">' . g_l('versions', '[schedulerMod]') . '</td>
</tr></thead>';

if($newDoc['documentScheduler']){
	$newDocScheduler = $newDoc['documentScheduler'] ? we_unserialize((substr_compare($newDoc['documentScheduler'], 'a%3A', 0, 4) == 0 ?
				html_entity_decode(urldecode($newDoc['documentScheduler']), ENT_QUOTES) :
				$newDoc['documentScheduler'])
		) :
		array();
} else {
	$newDocScheduler = array();
}
if(isset($oldDoc['documentScheduler'])){
	if($oldDoc['documentScheduler']){
		$oldDocScheduler = $oldDoc['documentScheduler'] ? we_unserialize((substr_compare($oldDoc['documentScheduler'], 'a%3A', 0, 4) == 0 ?
					html_entity_decode(urldecode($oldDoc['documentScheduler']), ENT_QUOTES) :
					$oldDoc['documentScheduler'])
			) :
			array();
	} else {
		$oldDocScheduler = array();
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
				$oldVal = we_versions_version::showValue($key, $val, $oldDoc['documentTable']);
			} else {
				$oldVal = (is_array($val) ?
						we_versions_version::showValue($key, $val, $oldDoc['documentTable']) :
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
			(empty($oldDoc) ? '' : '<td style="background-color:#FFF;"></td>') . '
</tr>';


		foreach($v as $key => $val){
			$mark = false;
			$name = g_l('versions', '[' . $key . ']');

			if(!is_array($val)){
				$newVal = we_versions_version::showValue($key, $val, $newDoc['documentTable']);

				if(!empty($oldDocScheduler)){
					$oldVal = (isset($oldDocScheduler[$k][$key]) && !is_array($oldDocScheduler[$k][$key]) ?
							we_versions_version::showValue($key, $oldDocScheduler[$k][$key], $oldDoc['documentTable']) :
							'');

					if($newVal != $oldVal){
						$mark = true;
					}
				} else {
					$oldVal = '';
				}
			} else {
				$newVal = we_versions_version::showValue($key, $val, $newDoc['documentTable']);
				if(!empty($oldDocScheduler)){
					$oldVal = (isset($oldDocScheduler[$k][$key]) && is_array($oldDocScheduler[$k][$key]) ?
							we_versions_version::showValue($key, $oldDocScheduler[$k][$key], $oldDoc['documentTable']) :
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
				(empty($oldDoc) ? '' : '<td>' . $oldVal . '</td>') . '
</tr>';
		}
	}
}

$contentDiff .= '</table>' .
//customfilter
	'<table class="propDiff"><thead>
<tr>
	<td colspan="3" class="defaultfont bold">' . g_l('versions', '[customerMod]') . '</td>
</tr></thead>';

if($newDoc['documentCustomFilter']){
	$newCustomFilter = we_unserialize((substr_compare($newDoc['documentCustomFilter'], 'a%3A', 0, 4) == 0 ?
			html_entity_decode(urldecode($newDoc['documentCustomFilter']), ENT_QUOTES) :
			$newDoc['documentCustomFilter'])
	);
} else {
	$newCustomFilter = array();
}
if(isset($oldDoc['documentCustomFilter'])){
	if($oldDoc['documentCustomFilter']){
		$oldCustomFilter = we_unserialize((substr_compare($oldDoc['documentCustomFilter'], 'a%3A', 0, 4) == 0 ?
				html_entity_decode(urldecode($oldDoc['documentCustomFilter']), ENT_QUOTES) :
				$oldDoc['documentCustomFilter'])
		);
	} else {
		$oldCustomFilter = array();
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
			$oldVal = we_versions_version::showValue($key, $val, $oldDoc['documentTable']);
		} else {
			$oldVal = (is_array($val) ?
					we_versions_version::showValue($key, $val, $oldDoc['documentTable']) :
					'');
		}

		$contentDiff .= '<tr>
	<td style="' . $mark . '"><strong>' . $name . '</strong></td>
	<td style="' . $mark . 'border-right:1px solid #000;">' . $newVal . '</td>' .
			(empty($oldDoc) ? '' : '<td style="' . $mark . '">' . $oldVal . '</td>') . '
</tr>';
	}
} else {
	foreach($newCustomFilter as $key => $val){

		$name = g_l('versions', '[' . $key . ']');

		$mark = false;

		if(!is_array($val)){
			$newVal = we_versions_version::showValue($key, $val, $newDoc['documentTable']);
			if(!empty($oldCustomFilter)){
				$oldVal = (!is_array($oldCustomFilter[$key]) ?
						we_versions_version::showValue($key, $oldCustomFilter[$key], $oldDoc['documentTable']) :
						'');

				if($newVal != $oldVal){
					$mark = true;
				}
			} else {
				$oldVal = '';
			}
		} else {
			$newVal = we_versions_version::showValue($key, $val, $newDoc['documentTable']);
			if(!empty($oldCustomFilter)){
				$oldVal = (isset($oldCustomFilter[$key]) && is_array($oldCustomFilter[$key]) ?
						we_versions_version::showValue($key, $oldCustomFilter[$key], $oldDoc['documentTable']) :
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
			(empty($oldDoc) ? '' : '<td>' . $oldVal . '</td>') . '
</tr>';
	}
}

$contentDiff .= '</table>';
$_tab_1 = $contentDiff;

if(!$isObj){
	$_tab_2 = $contentNew;
	$_tab_3 = $contentOld;
} else {
	$_tab_2 = "";
	$_tab_3 = "";
}

echo we_html_tools::getHtmlTop('webEdition - ' . g_l('versions', '[versioning]'), ($newDoc['Charset'] ? : DEFAULT_CHARSET)) .
 STYLESHEET .
 we_tabs::getHeader();
?>

<script><!--
	var activ_tab = 1;

	function toggle(id) {
		var elem = document.getElementById(id);
		elem.style.display = (elem.style.display == "none" ? "block" : "none");
	}

	function previewVersion(ID, newID) {
		top.opener.top.we_cmd("versions_preview", ID, newID);
	}
	function setTab(tab) {
		toggle("tab" + activ_tab);
		toggle("tab" + tab);
		activ_tab = tab;
	}//-->
</script>
<style>
	#top{
		margin-left:25px;
	}
	td {
		font-size:11px;
		vertical-align:top;
		padding: 5px;
		border-bottom:1px solid #B8B8B7;
		border-left:1px solid #B8B8B7;
	}
	#tab1 {
		position:absolute;
		overflow:auto;
	}
	#topPrint {
		margin:0px 0px 0px 25px;
		display: none;
	}
	#content{
		position:absolute;
		margin: 0px;
		top:21px;
		bottom:40px;
		left:0px;
		right:0px;
		overflow:auto;
	}
	table{
		width:95%;
		background-color:#F5F5F5;
		margin:15px 15px 15px 25px;
		border-left:1px solid #B8B8B7;
		border-right:1px solid #B8B8B7;
	}
	thead td{
		background-color:#BCBBBB;
		text-align:left;
	}

	table.propDiff td{
		width:33%;
	}

	tr.changedElement{
		background-color:#BFD5FF;
	}

	span.insA{
		color:blue;
	}
	span.insB{
		text-decoration:underline;
	}
	span.delA{
		color:red;
	}
	span.delB{
		text-decoration: line-through;
	}


	@media print{
		td {
			font-size:9px;
			vertical-align:top;
			padding: 5px;
		}
		#tab1 {
			position:relative;
			overflow: visible;
			font-size:12px;
		}
		#tab2,
		#tab3,
		#eHeaderBody,
		#top {
			display: none
		}
		#topPrint {
			display: block
		}
	}
</style>
</head>

<body class="weDialogBody">
	<div id="eHeaderBody">
		<?php echo $tabsBody; ?>
	</div>
	<div id="content">
		<div id="tab1" style="display:block;width:100%;">
			<?php echo $_tab_1 ?>
		</div>
		<div id="tab2" style="display:none;height:100%;width:100%">
			<?php echo $_tab_2 ?>
		</div>
		<div id="tab3" style="display:none;height:100%;width:100%">
			<?php echo $_tab_3 ?>
		</div>
	</div>

	<div class="editfooter"><?php echo we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();"); ?></div>
</body>
</html>