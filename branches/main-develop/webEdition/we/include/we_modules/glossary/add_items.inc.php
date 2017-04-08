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
include_once(WE_SPELLCHECKER_MODULE_PATH . '/spellchecker.conf.inc.php');

we_html_tools::protect();
// Transaction
if(!($Transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', 0, 2))){
	die('No Transaction');
}
$langs = array_keys(getWELangs());
$Modes = [];
if((
	empty($_SESSION['prefs']['force_glossary_action'])
	) && $cmd3 != "checkOnly"
){
	$Modes[''] = g_l('modules_glossary', '[please_choose]');
}
$Modes['ignore'] = g_l('modules_glossary', '[ignore]');
if(we_base_permission::hasPerm('NEW_GLOSSARY')){
	$Modes[we_glossary_glossary::TYPE_ABBREVATION] = g_l('modules_glossary', '[abbreviation]');
	$Modes[we_glossary_glossary::TYPE_ACRONYM] = g_l('modules_glossary', '[acronym]');
	$Modes[we_glossary_glossary::TYPE_FOREIGNWORD] = g_l('modules_glossary', '[foreignword]');
	$Modes[we_glossary_glossary::TYPE_TEXTREPLACE] = g_l('modules_glossary', '[textreplacement]');
}
if(we_base_permission::hasPerm("EDIT_GLOSSARY_DICTIONARY")){
	$Modes['exception'] = g_l('modules_glossary', '[to_exceptionlist]');
}
$Modes['correct'] = g_l('modules_glossary', '[correct_word]');
$Modes['dictionary'] = g_l('modules_glossary', '[to_dictionary]');

$cmd3 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3);

echo we_html_tools::getHtmlTop(g_l('modules_glossary', '[glossary_check]')) .
 we_html_element::cssLink(CSS_DIR . 'glossary_add.css') .
 we_html_element::jsScript(JS_DIR . 'weCombobox.js') .
 we_html_element::jsScript(WE_JS_MODULES_DIR . 'glossary/add_items.js', '', [
	'id' => 'loadAddItems',
	'data-doc' => setDynamicVar([
		'EditPageNr' => intval($we_doc->EditPageNr),
		'transaction' => $Transaction,
		'langs' => $langs,
		'modes' => $modes,
		'cmd3' => $cmd3,
	])]
);


//
// ---> Main Frame
//

switch(we_base_request::_(we_base_request::STRING, 'we_cmd', 'frameset', 1)){
	default:
	case 'frameset':

		$ClassName = $_SESSION['weS']['we_data'][$Transaction][0]['ClassName'];

		$we_doc = new $ClassName();
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$Transaction]);

		$Language = $we_doc->Language;

		$LanguageDict = null;
		if(isset($spellcheckerConf['lang']) && is_array($spellcheckerConf['lang'])){
			$LanguageDict = array_search($Language, $spellcheckerConf['lang']);
			$LanguageDict = in_array($LanguageDict, $spellcheckerConf['active']) ? $LanguageDict : null;
		}
		if(is_null($LanguageDict)){
			$LanguageDict = $spellcheckerConf['default'];
		}

		$UserDict = WE_SPELLCHECKER_MODULE_PATH . '/dict/' . $_SESSION['user']['Username'] . '@' . $_SERVER['SERVER_NAME'] . '.dict';

		//
		// --> get the content
		//

	$SrcBody = "";
		foreach($we_doc->elements as $key => $name){
			switch($key){
				case 'data':
				case 'Title':
				case 'Description':
				case 'Keywords':
				case 'Charset':
				default:
					if(isset($we_doc->elements[$key]['type']) && (
						$we_doc->elements[$key]['type'] === "txt" || $we_doc->elements[$key]['type'] === "input"
						)
					){
						$SrcBody .= $we_doc->elements[$key]['dat'] . " ";
					}
			}
		}

		/*
		  This is the fastest variant
		 */
		// split the source into tag and non-tag pieces
		$Pieces = preg_split('!(<[^>]*>)!', $SrcBody, -1, PREG_SPLIT_DELIM_CAPTURE);

		// replace words in non-tag pieces
		$ReplBody = $Before = " ";
		foreach($Pieces as $Piece){
			if(strpos($Piece, '<') !== 0 && stripos($Before, '<script') === false){
				$ReplBody .= $Piece . " ";
			}
			$Before = $Piece;
		}

		$Text = preg_replace("=<br(>|([\s/][^>]*)>)\r?\n?=i", "\n", $ReplBody);
		$Text = str_replace(["\r\n", "\n", "\""], [' ', ' ', "\\\""], $Text);
		$Text = preg_replace("=<br(>|([\s/][^>]*)>)\r?\n?=i", "\n", $Text);
		$Text = str_replace(["\r\n", "\n", "&nbsp;"], ' ', $Text);
		$Text = preg_replace(["/[\t]+/", "/[ ]+/"], " ", $Text);

		$ExceptionListFilename = we_glossary_glossary::getExceptionFilename($Language);

		if(!file_exists($ExceptionListFilename)){
			we_glossary_glossary::editException($Language, "");
		}

		$ExceptionList = we_glossary_glossary::getException($Language);
		$PublishedEntries = we_glossary_glossary::getEntries($Language, 'published');
		foreach($PublishedEntries as $Key => $Value){
			$ExceptionList[] = $Value['Text'];
		}
		$UnpublishedEntries = we_glossary_glossary::getEntries($Language, 'unpublished');
		$List = [];
		foreach($UnpublishedEntries as $Key => $Value){
			if($UnpublishedEntries[$Key]['Type'] != we_glossary_glossary::TYPE_LINK){
				$List[] = $Value;
			}
		}
		//FIMXE: old js
		?>
			<script><!--
						function setDialog() {

		<?php
		foreach($List as $Key => $Value){
			$Value['Text'] = str_replace(["\r", "\n"], '', $Value['Text']);
			$TextReplaced = preg_replace('-(^|\s|[!"#$%&\'()*+,\-./:;=?@[\\]^_`{\|}~])(' . preg_quote($Value['Text'], '-') . ')(\s|[!"#$%&\'()*+,\-./:;=?@[\\]^_`{\|}~]|$)-', '${1}${3}', $Text);
			$Replaced = (trim($TextReplaced) != trim($Text));
			$Text = trim($TextReplaced);
			if($Replaced){
				echo "top.frames.glossarycheck.addPredefinedRow('" . $Value['Text'] . "',[],'" . $Value['Type'] . "','" . $Value['Title'] . "','" . $Value['Lang'] . "');\n";
			}
		}

		foreach($ExceptionList as $Key => $Value){
			$Value = str_replace(["\r", "\n"], '', $Value);
			$Text = preg_replace('-(^|\s|[!"#$%&\'()*+,\-./:;=?@[\\]^_`{\|}~])(' . preg_quote($Value, '-') . ')(\s|[!"#$%&\'()*+,\-./:;=?@[\\]^_`{\|}~]|$)-', '${1}${3}', $Text);
		}
		?>
					orginal = "<?= $Text; ?>";
					window.setTimeout(spellcheck, 1000);

				}

				//-->
			</script>
			</head>

			<body style="margin:0px;padding:0px;">
				<form name="we_form" action="<?= WEBEDITION_DIR; ?>we_cmd.php" method="post">
					<?php
					if(($cnt = count($_REQUEST['we_cmd'])) > 3){
						for($i = 3; $i < $cnt; $i++){
							echo we_html_element::htmlHidden('we_cmd[' . ($i - 3) . ']', we_base_request::_(we_base_request::RAW, 'we_cmd', '', $i));
						}
					}
					echo '<iframe id="glossarycheck" name="glossarycheck" src="' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=' . we_base_request::_(we_base_request::RAW, 'we_cmd', '', 0) . '&we_cmd[1]=prepare&we_cmd[2]=' . we_base_request::_(we_base_request::RAW, 'we_cmd', '', 2) . (($cmd3 = we_base_request::_(we_base_request::RAW, 'we_cmd', false, 3)) !== false ? '&we_cmd[3]=' . $cmd3 : '' ) . '"  style="width:730px;height:400px;overflow: hidden;"></iframe>';

//
// ---> Form with all unidentified words
//
					break;
				case 'prepare':
					?>
					</head>
					<body class="weDialogBody" onload="init();">

						<div id="spinner">
							<div id="statusImage"><i class="fa fa-2x fa-spinner fa-pulse"></i></div>
							<div id="statusText" class="small" style="color: black;"><?= g_l('modules_glossary', '[download]'); ?></div>
						</div>


						<form name="we_form" action="<?= WEBEDITION_DIR; ?>we_cmd.php" method="post" target="glossarycheck"><?php
							echo we_html_element::htmlHiddens(['ItemsToPublish' => '',
								'we_cmd[0]' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0),
								'we_cmd[1]' => 'finish',
								'we_cmd[2]' => $Transaction,
								($cmd3 ? 'we_cmd[3]' : '') => $cmd3
							]);

							$Content = '
	<table style="width:650px;" class="default defaultfont">
	<colgroup><col style="width:150px;"/><col style="width:140px;"/><col style="width:200px;"/><col style="width:100px;"/></colgroup>
	<tr>
		<td colspan="7" style="padding-bottom:5px;">' . g_l('modules_glossary', '[not_identified_words]') . '</td>
	</tr>
	<tr>
		<td style="padding-right:20px;"><b>' . g_l('modules_glossary', '[not_known_word]') . '</b></td>
		<td style="padding-right:20px;"><b>' . g_l('modules_glossary', '[action]') . '</b></td>
		<td style="padding-right:20px;"><b>' . g_l('modules_glossary', '[announced_word]') . ' / ' . g_l('modules_glossary', '[suggestion]') . '</b></td>
		<td><b>' . g_l('modules_glossary', '[language]') . '</b></td>
	<tr>
	</table>
	<div style="height: 248px; width: 675px; overflow: auto;">
	<table style="width:650px;" class="default defaultfont">
	<tbody id="unknown">
	<colgroup><col style="width:150px;"/><col style="width:140px;"/><col style="width:200px;"/><col style="width:100px;"/></colgroup>
	</tbody>
	</table>';


							// Only glossary check
							if($cmd3 === "checkOnly"){
								$CancelButton = we_html_button::create_button(we_html_button::CLOSE, "javascript:top.close();", '', 0, 0, "", "", false, false);
								$PublishButton = "";

								// glossary check and publishing
							} else {
								$CancelButton = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();", '', 0, 0, "", "", false, false);
								$PublishButton = we_html_button::create_button(we_html_button::PUBLISH, "javascript:top.we_save_document();", '', 0, 0, "", "", true, false);
							}
							$ExecuteButton = we_html_button::create_button('execute', "javascript:checkForm();", '', 0, 0, "", "", true, false);


							$Buttons = we_html_button::position_yes_no_cancel($PublishButton . $ExecuteButton, "", $CancelButton);
							if($cmd3 != "checkOnly"){
								$Buttons .= we_html_element::jsElement("WE().layout.button.hide(document, 'publish');");
							}

							$Parts = [
								["headline" => "",
									"html" => $Content,
								]
							];

							echo we_html_multiIconBox::getHTML('weMultibox', $Parts, 30, $Buttons, -1, '', '', false, g_l('modules_glossary', '[glossary_check]'));

//
// --> Finish Step
//
							break;
						case 'finish':
							$ClassName = $_SESSION['weS']['we_data'][$Transaction][0]['ClassName'];

							$we_doc = new $ClassName();
							$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$Transaction]);

							$Language = $we_doc->Language;

							//
							// --> Insert or correct needed items
							//

	$AddJs = "";
							$items = we_base_request::_(we_base_request::STRING, 'item');
							if($items){

								foreach($items as $Key => $Entry){
									switch($Entry['type']){
										case 'exception':
											we_glossary_glossary::addToException($Language, $Key);
											break;
										case '':
										case 'ignore':
											break;
										case 'correct':
											foreach($we_doc->elements as &$val){
												if(isset($val['type']) && (
													$val['type'] === 'txt' || $val['type'] === 'input'
													)
												){
													$val['dat'] = preg_replace('-(^|\s|[!"#$%&\'()*+,\-./:;=?@[\\]^_`{\|}~])(' . preg_quote($Key, '-') . ')(\s|[!"#$%&\'()*+,\-./:;=?@[\\]^_`{\|}~]|$)-', '${1}' . $Entry['title'] . '${3}', $temp);
												}
											}
											unset($val);
											break;
										case "dictionary":
											$AddJs .= "AddWords += '" . addslashes($Key) . ",'\n";
											break;
										default:
											$Glossary = new we_glossary_glossary();
											$Glossary->Path = '/' . $Language . '/' . $Entry['type'] . '/' . $Key;
											$Glossary->IsFolder = 0;
											$Glossary->Text = $Key;
											$Glossary->Type = $Entry['type'];
											$Glossary->Language = $Language;
											$Glossary->Title = isset($Entry['title']) ? $Entry['title'] : '';
											$Glossary->setAttribute('lang', isset($Entry['lang']) ? $Entry['lang'] : '');
											$Glossary->Published = time();

											if($Glossary->pathExists($Glossary->Path)){
												$ID = $Glossary->getIDByPath($Glossary->Path);
												$Glossary->ID = $ID;
											}

											$Glossary->save();
											unset($Glossary);
									}
								}
							}

							$we_doc->saveinSession($_SESSION['weS']['we_data'][$Transaction]);

							//
							// --> Actualize to Cache
							//

	$Cache = new we_glossary_cache($Language);
							$Cache->write();
							unset($Cache);

							echo we_html_element::jsElement('
top.we_reloadEditPage();
var AddWords = "";
' . $AddJs . '
top.add();' .
								($cmd3 != 'checkOnly' ? "top.we_save_document();" : '') .
								'alert("' .
								g_l('modules_glossary', ($cmd4 === 'checkOnly' ?
										'[check_successful]' :
										// glossary check with publishing
										'[check_successful_and_publish]')) . '");
top.close();');
							?>
							</head>
							<body class="weDialogBody">
								<form name="we_form" action="<?= WEBEDITION_DIR; ?>we_cmd.php" method="post"><?php
							}
							?>
						</form>
					</body>

					</html>
