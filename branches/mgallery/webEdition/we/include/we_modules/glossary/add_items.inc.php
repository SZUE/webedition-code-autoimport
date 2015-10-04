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

echo we_html_tools::getHtmlTop(g_l('modules_glossary', '[glossary_check]')) .
 STYLESHEET .
 we_html_element::cssLink(CSS_DIR . 'glossary_add.css') .
 we_html_element::jsElement('
var g_l={
	checking:"' . g_l('modules_glossary', '[checking]') . '",
	all_words_identified:"' . g_l('modules_glossary', '[all_words_identified]') . '",
	no_java:"' . g_l('modules_glossary', '[no_java]') . '",
	change_to:"' . g_l('modules_glossary', '[change_to]') . '",
	input:"' . g_l('modules_glossary', '[input]') . '",
	suggestions:"' . g_l('modules_glossary', '[suggestions]') . '",
};

var doc={
EditPageNr:' . intval($we_doc->EditPageNr) . '",
};
var transaction="' . $Transaction . '";
var consts={
	TYPE_FOREIGNWORD:"' . we_glossary_glossary::TYPE_FOREIGNWORD . '",
	TYPE_ABBREVATION:"' . we_glossary_glossary::TYPE_ABBREVATION . '",
	TYPE_ACRONYM:"' . we_glossary_glossary::TYPE_ACRONYM . '",
};
') .
 we_html_element::jsScript(WE_JS_GLOSSARY_MODULE_DIR . 'add_items.js');


//
// ---> Main Frame
//

$cmd3 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3);

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

		$AppletCode = we_html_element::htmlApplet(array(
				'name' => "spellchecker",
				'code' => "LeSpellchecker.class",
				'archive' => "lespellchecker.jar",
				'codebase' => getServerUrl(true) . WE_SPELLCHECKER_MODULE_DIR,
				'width' => 2,
				'height' => 2,
				'id' => "applet",
				'style' => "visibility: hidden",
				), '
<param name="code" value="LeSpellchecker.class"/>
<param name="archive" value="lespellchecker.jar"/>
<param name="type" value="application/x-java-applet;version=1.1"/>
<param name="dictBase" value="' . getServerUrl(true) . WE_SPELLCHECKER_MODULE_DIR . 'dict/' . '"/>
<param name="dictionary" value="' . $LanguageDict . '"/>
<param name="debug" value="off"/>
<param name="user" value="' . $_SESSION['user']['Username'] . '@' . $_SERVER['SERVER_NAME'] . '"/>
<param name="udSize" value="' . (is_file($UserDict) ? filesize($UserDict) : '0') . '"/>'
		);

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
		$ReplBody = "";
		$Before = " ";
		foreach($Pieces as $Piece){
			if(strpos($Piece, '<') !== 0 && stripos($Before, '<script') === false){
				$ReplBody .= $Piece . " ";
			}
			$Before = $Piece;
		}

		$Text = preg_replace("=<br(>|([\s/][^>]*)>)\r?\n?=i", "\n", $ReplBody);
		$Text = str_replace(array("\r\n", "\n", "\""), array(' ', ' ', "\\\""), $Text);
		$Text = preg_replace("=<br(>|([\s/][^>]*)>)\r?\n?=i", "\n", $Text);
		$Text = str_replace(array("\r\n", "\n", "&nbsp;"), ' ', $Text);
		$Text = preg_replace(array("/[\t]+/", "/[ ]+/"), " ", $Text);

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
		$List = array();
		foreach($UnpublishedEntries as $Key => $Value){
			if($UnpublishedEntries[$Key]['Type'] != we_glossary_glossary::TYPE_LINK){
				$List[] = $Value;
			}
		}

		echo we_html_element::jsScript(JS_DIR . 'keyListener.js');
		?>
		<script><!--
			top.opener.top.toggleBusy();
			function setDialog() {

		<?php
		foreach($List as $Key => $Value){
			$Value['Text'] = str_replace(array("\r", "\n"), '', $Value['Text']);
			$TextReplaced = preg_replace('-(^|\s|[!"#$%&\'()*+,\-./:;=?@[\\]^_`{\|}~])(' . preg_quote($Value['Text'], '-') . ')(\s|[!"#$%&\'()*+,\-./:;=?@[\\]^_`{\|}~]|$)-', '${1}${3}', $Text);
			$Replaced = (trim($TextReplaced) != trim($Text));
			$Text = trim($TextReplaced);
			if($Replaced){
				echo "top.frames.glossarycheck.addPredefinedRow('" . $Value['Text'] . "',[],'" . $Value['Type'] . "','" . $Value['Title'] . "','" . $Value['Lang'] . "');\n";
			}
		}

		foreach($ExceptionList as $Key => $Value){
			$Value = str_replace(array("\r", "\n"), '', $Value);
			$Text = preg_replace('-(^|\s|[!"#$%&\'()*+,\-./:;=?@[\\]^_`{\|}~])(' . preg_quote($Value, '-') . ')(\s|[!"#$%&\'()*+,\-./:;=?@[\\]^_`{\|}~]|$)-', '${1}${3}', $Text);
		}
		?>
				orginal = "<?php echo $Text; ?>";
				window.setTimeout(spellcheck, 1000);

			}

			//-->
		</script>
		</head>

		<body style="margin:0px;padding:0px;">
			<form name="we_form" action="<?php echo WEBEDITION_DIR; ?>we_cmd.php" method="post">
				<?php
				if(($cnt = count($_REQUEST['we_cmd'])) > 3){
					for($i = 3; $i < $cnt; $i++){
						echo we_html_element::htmlHidden('we_cmd[' . ($i - 3) . ']', we_base_request::_(we_base_request::RAW, 'we_cmd', '', $i));
					}
				}
				echo '<iframe id="glossarycheck" name="glossarycheck" frameborder="0" src="' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=' . we_base_request::_(we_base_request::RAW, 'we_cmd', '', 0) . '&we_cmd[1]=prepare&we_cmd[2]=' . we_base_request::_(we_base_request::RAW, 'we_cmd', '', 2) . (($cmd3 = we_base_request::_(we_base_request::RAW, 'we_cmd', false, 3)) !== false ? '&we_cmd[3]=' . $cmd3 : '' ) . '" width="730px" height="400px" style="overflow: hidden;"></iframe>' .
				$AppletCode;

//
// ---> Form with all unidentified words
//
				break;
			case 'prepare':

				//FIXME: these values should be obtained from global settings
				$Languages = array(
					'de' => 'de',
					'en' => 'en',
					'es' => 'es',
					'fi' => 'fi',
					'ru' => 'ru',
					'nl' => 'nl',
					'pl' => 'pl',
				);

				$Modes = array();
				if((
					isset($_SESSION['prefs']['force_glossary_action']) && $_SESSION['prefs']['force_glossary_action'] == 0
					) && $cmd3 != "checkOnly"
				){
					$Modes[''] = g_l('modules_glossary', '[please_choose]');
				}
				$Modes['ignore'] = g_l('modules_glossary', '[ignore]');
				if(permissionhandler::hasPerm("NEW_GLOSSARY")){
					$Modes[we_glossary_glossary::TYPE_ABBREVATION] = g_l('modules_glossary', '[abbreviation]');
					$Modes[we_glossary_glossary::TYPE_ACRONYM] = g_l('modules_glossary', '[acronym]');
					$Modes[we_glossary_glossary::TYPE_FOREIGNWORD] = g_l('modules_glossary', '[foreignword]');
					$Modes[we_glossary_glossary::TYPE_TEXTREPLACE] = g_l('modules_glossary', '[textreplacement]');
				}
				if(permissionhandler::hasPerm("EDIT_GLOSSARY_DICTIONARY")){
					$Modes['exception'] = g_l('modules_glossary', '[to_exceptionlist]');
				}
				$Modes['correct'] = g_l('modules_glossary', '[correct_word]');
				$Modes['dictionary'] = g_l('modules_glossary', '[to_dictionary]');
				?>
				<?php echo we_html_element::jsScript(JS_DIR . 'weCombobox.js'); ?>
				<script><!--
			Combobox = new weCombobox();

					function init() {
						table = document.getElementById('unknown');
						top.setDialog();
					}

					function getActionColumn(word, type) {
						var td = document.createElement('td');
						var html;

						html = '<select class="defaultfont" name="item[' + word + '][type]" size="1" id="type_' + counter + '" onchange="disableItem(' + counter + ', this.value);" style="width: 140px">'
		<?php
		foreach($Modes as $Key => $Value){
			echo "		+	'<option value=\"" . $Key . "\"' + (type == '" . $Key . "' ? ' selected=\"selected\"' : '') + '>" . $Value . "</option>'";
		}
		?>
						+ '</select>';

						td.innerHTML = html;
						return td;
					}



					function getLanguageColumn(word, lang) {
						var td = document.createElement('td');
						td.innerHTML = '<select class="defaultfont" name="item[' + word + '][lang]" size="1" id="lang_' + counter + '" disabled=\"disabled\" style="width: 100px">' +
										'<option value="' + lang + '">' + lang + '</option>' +
										'<optgroup label="<?php echo g_l('modules_glossary', '[change_to]'); ?>">' +
										'<option value="">-- <?php echo g_l('modules_glossary', '[input]'); ?> --</option>' +
										'</optgroup>' +
										'<optgroup label="<?php echo g_l('modules_glossary', '[languages]'); ?>">' +
		<?php
		foreach($Languages as $Key => $Value){
			echo "			'<option value=\"" . $Key . "\">" . $Value . "</option>'+";
		}
		?>
						'</optgroup>' +
										'</select>';

						return td;
					}



					function activateButtons() {
						if (counter === 0) {
							var tr = document.createElement('tr');

							tr.appendChild(getTextColumn(g_l.all_words_identified, 7));
							table.appendChild(tr);
							WE().layout.button.hide(document, 'execute');
		<?php
		if($cmd3 != "checkOnly"){
			?>
								WE().layout.button.enable(document, 'publish');
								WE().layout.button.show(document, 'publish');
			<?php
		}
		?>

						} else {
							WE().layout.button.enable(document, 'execute');
						}

					}

					function noJava() {
						var tr = document.createElement('tr');

						tr.appendChild(getTextColumn(g_l.no_java, 7));
						table.appendChild(tr);
						WE().layout.button.hide(document, 'execute');
		<?php
		if($cmd3 != "checkOnly"){
			?>
							document.getElementById('execute').innerHTML = '<?php echo str_replace("'", "\'", we_html_button::create_button(we_html_button::PUBLISH, "javascript:top.we_save_document();", true, 120, 22, "", "", true, false)); ?>';
							WE().layout.button.enable(document, 'publish');
			<?php
		}
		?>
					}

					function checkForm() {
						for (i = 0; i < counter; i++) {
							type = document.getElementById('type_' + i).value;
							title = document.getElementById('title_' + i).value;
							lang = document.getElementById('lang_' + i).value;
							switch (type) {
								case consts.TYPE_ABBREVATION:
								case consts.TYPE_ACRONYM:
									if (title === '') {
										document.getElementById('title_' + i).focus();
		<?php echo we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[please_insert_title]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
										return false;
									}
									if (lang === '') {
										document.getElementById('lang_' + i).focus();
		<?php echo we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[please_insert_language]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
										return false;
									}
									break;
								case consts.TYPE_FOREIGNWORD:
									if (lang === '') {
										document.getElementById('lang_' + i).focus();
		<?php echo we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[please_insert_language]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
										return false;
									}
									break;
								case 'ignore':
								case 'exception':
								case 'dictionary':
									break;
								case 'correct':
									document.getElementById('title_' + i).value = document.getElementById('suggest_' + i).value;
									title = document.getElementById('title_' + i).value;
									if (title === '') {
										document.getElementById('title_' + i).focus();
		<?php echo we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[please_insert_correct_word]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
										return false;
									}
									break;
								default:
									document.getElementById('type_' + i).focus();
		<?php echo we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[please_choose_action]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
									return false;
									break;
							}
						}
						document.forms[0].submit();
					}
					//-->
				</script>
			</head>
			<body class="weDialogBody" onload="init();">

				<div id="spinner">
					<div id="statusImage"><i class="fa fa-2x fa-spinner fa-pulse"></i></div>
					<div id="statusText" class="small" style="color: black;"><?php echo g_l('modules_glossary', '[download]'); ?></div>
				</div>


				<form name="we_form" action="<?php echo WEBEDITION_DIR; ?>we_cmd.php" method="post" target="glossarycheck"><?php
					echo we_html_element::htmlHiddens(array(
						'ItemsToPublish' => '',
						'we_cmd[0]' => we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0),
						'we_cmd[1]' => 'finish',
						'we_cmd[2]' => $Transaction,
						($cmd3 ? 'we_cmd[3]' : '') => $cmd3
					));

					$Content = '
	<table width="650" class="default defaultfont">
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
	<table width="650" class="default defaultfont">
	<tbody id="unknown">
	<colgroup><col style="width:150px;"/><col style="width:140px;"/><col style="width:200px;"/><col style="width:100px;"/></colgroup>
	</tbody>
	</table>';


					// Only glossary check
					if($cmd3 === "checkOnly"){
						$CancelButton = we_html_button::create_button(we_html_button::CLOSE, "javascript:top.close();", true, 120, 22, "", "", false, false);
						$PublishButton = "";

						// glossary check and publishing
					} else {
						$CancelButton = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();", true, 120, 22, "", "", false, false);
						$PublishButton = we_html_button::create_button(we_html_button::PUBLISH, "javascript:top.we_save_document();", true, 120, 22, "", "", true, false);
					}
					$ExecuteButton = we_html_button::create_button("execute", "javascript:checkForm();", true, 120, 22, "", "", true, false);


					$Buttons = we_html_button::position_yes_no_cancel($PublishButton . $ExecuteButton, "", $CancelButton);
					if($cmd3 != "checkOnly"){
						$Buttons .= we_html_element::jsElement("WE().layout.button.hide(document, 'publish');");
					}

					$Parts = array();
					$Part = array(
						"headline" => "",
						"html" => $Content,
						"space" => 0
					);
					$Parts[] = $Part;

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
						we_message_reporting::getShowMessageCall(
							g_l('modules_glossary', ($cmd4 === 'checkOnly' ?
									'[check_successful]' :
									// glossary check with publishing
									'[check_successful_and_publish]')), we_message_reporting::WE_MESSAGE_NOTICE, false, true) .
						"top.close();");
					?>
					</head>
					<body class="weDialogBody">
						<form name="we_form" action="<?php echo WEBEDITION_DIR; ?>we_cmd.php" method="post"><?php
					}
					?>
				</form>
				</center>
			</body>

			</html>
