<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
include_once(WE_SPELLCHECKER_MODULE_PATH . '/spellchecker.conf.inc.php');
$protect = we_base_moduleInfo::isActive(we_base_moduleInfo::GLOSSARY) && we_users_util::canEditModule(we_base_moduleInfo::GLOSSARY) ? null : array(false);
we_html_tools::protect($protect);

echo we_html_tools::getHtmlTop();

function saveSettings($default, $active, $langs = array()){
	//FIXME: move to tblsettings
	$_lang = '';

	foreach($langs as $k => $v){
		$_lang .= '\'' . $k . '\'=>\'' . addslashes($v) . '\',';
	}

	if(!empty($_lang)){
		$_lang = '$spellcheckerConf[\'lang\']=array(' . $_lang . ');';
	}

	$_construct = '<?php
				$spellcheckerConf = array(
				\'default\' => \'' . $default . '\',
				\'active\' => array(\'' . implode('\',\'', $active) . '\'),
				);
				' . $_lang;

	we_base_file::save(WE_SPELLCHECKER_MODULE_PATH . 'spellchecker.conf.inc.php', $_construct);

	$_SESSION['weS']['dictLang'] = $default;
}

$cmd1 = we_base_request::_(we_base_request::STRING, 'cmd', false, 1);
switch(we_base_request::_(we_base_request::STRING, 'cmd', '', 0)){
	case 'addWord' :
		if($cmd1 !== false){

			$_username = $_SESSION['user']['Username'];
			$_replacement = array('\\', '/', ':', '*', '?', '<', '>', '|', '"');
			for($_i = 0; $_i < count($_replacement); $_i++){
				$_username = str_replace($_replacement[$_i], 'MASK' . $_i, $_username);
			}

			$_userDict = WE_SPELLCHECKER_MODULE_PATH . '/dict/' . $_username . '@' . $_SERVER['SERVER_NAME'] . '.dict';
			we_base_file::save($_userDict, $cmd1 . "\n", 'ab');
		}
		break;
	case 'addWords' :
		if($cmd1 !== false){

			$_words = array();
			$_str = explode(',', $cmd1);
			foreach($_str as $_s){
				if(!empty($_s)){
					$_words[] = $_s;
				}
			}

			$_username = $_SESSION['user']['Username'];
			$_replacement = array('\\', '/', ':', '*', '?', '<', '>', '|', '"');
			for($_i = 0; $_i < count($_replacement); $_i++){
				$_username = str_replace($_replacement[$_i], 'MASK' . $_i, $_username);
			}

			$_userDict = WE_SPELLCHECKER_MODULE_PATH . '/dict/' . $_username . '@' . $_SERVER['SERVER_NAME'] . '.dict';
			we_base_file::save($_userDict, implode("\n", $_words) . "\n", 'ab');
		}
		break;
	case 'setLangDict' :
		if($cmd1 !== false){
			$_SESSION['weS']['dictLang'] = $cmd1;
		}
		echo we_html_element::jsElement('
				top.document.we_form.submit();
			');
		break;

	case 'removeDictFile':
		if(strpos($cmd1, '..') === false){

			@unlink(WE_SPELLCHECKER_MODULE_PATH . 'dict/' . $cmd1);
		}
		break;
	case 'uploadPart':
		$_content = '';
		if(isset($_FILES['chunk'])){

			move_uploaded_file($_FILES['chunk']['tmp_name'], WE_SPELLCHECKER_MODULE_PATH . 'chunk');

			$_content = we_base_file::load(WE_SPELLCHECKER_MODULE_PATH . 'chunk');
			$_checksum = crc32($_content);

			if(sprintf("%u", $_checksum) != we_base_request::_(we_base_request::STRING, 'cmd', '', 2)){
				t_e('Corrupt!!!');
			}

			we_base_file::save(WE_SPELLCHECKER_MODULE_PATH . 'dict/' . $cmd1, $_content, 'ab');

			unlink(WE_SPELLCHECKER_MODULE_PATH . 'chunk');
		} else {

		}
		exit();

	case 'saveSettings':

		$_default = we_base_request::_(we_base_request::STRING, 'default');
		$_active = array();
		foreach($_REQUEST as $_key => $_value){
			if(strpos($_key, 'enable_') === 0 && $_value == 1){
				$_active[] = str_replace('enable_', '', $_key);
			}
		}

		if(!in_array($_default, $_active)){
			$_active[] = $_default;
		}


		$_langs = we_base_request::_(we_base_request::STRING, 'lang');

		saveSettings($_default, array_unique($_active), is_array($_langs) ? $_langs : array());

		we_base_file::save(WE_SPELLCHECKER_MODULE_PATH . 'dict/default.inc.php', we_base_request::_(we_base_request::STRING, 'defaultDict'));

		echo we_html_element::jsElement(
			we_message_reporting::getShowMessageCall(g_l('modules_spellchecker', '[save_settings]'), we_message_reporting::WE_MESSAGE_NOTICE)
		);

		break;

	case 'deleteDict':
		if(strpos($cmd1, "..") === false){
			unlink(WE_SPELLCHECKER_MODULE_PATH . 'dict/' . $cmd1 . '.zip');
			$_mess = g_l('modules_spellchecker', '[dict_removed]');
			$_messType = we_message_reporting::WE_MESSAGE_NOTICE;

			if($GLOBALS['spellcheckerConf']['default'] == $cmd1){ // if the default dict has been deleted
				if(!empty($GLOBALS['spellcheckerConf']['active']) && isset($GLOBALS['spellcheckerConf']['active'][0])){
					// take the firts active dictionary
					$_new_ac = array();
					foreach($GLOBALS['spellcheckerConf']['active'] as $ac){
						if($ac != $cmd1){
							$_new_ac[] = $ac;
						}
					}
					saveSettings(isset($_new_ac[0]) ? $_new_ac[0] : '', $_new_ac);
				}
			}
		} else {
			$_mess = g_l('modules_spellchecker', '[name_invalid]');
			$_messType = we_message_reporting::WE_MESSAGE_ERROR;
		}

		echo we_html_element::jsElement(
			we_message_reporting::getShowMessageCall($_mess, $_messType) .
			'parent.loadTable();
				');
		break;

	case 'refresh':
		$table = new we_html_table(array('width' => 380, 'style' => 'margin: 5px;'), 1, 6);

		$table->setRow(0, array('class' => 'bold', 'style' => 'background-color: silver;'), 6);
		$table->setCol(0, 0, array('class' => 'small', 'style' => 'vertical-align:top;color: white;'), g_l('modules_spellchecker', '[default]'));
		$table->setCol(0, 1, array('class' => 'small', 'style' => 'vertical-align:top;color: white;'), g_l('modules_spellchecker', '[dictionary]'));
		$table->setCol(0, 2, array('class' => 'small', 'style' => 'vertical-align:top;color: white;'), g_l('modules_spellchecker', '[language]'));
		$table->setCol(0, 3, array('class' => 'small', 'style' => 'vertical-align:top;color: white;'), g_l('modules_spellchecker', '[active]'));
		$table->setCol(0, 4, array('class' => 'small', 'style' => 'vertical-align:top;color: white;'), g_l('modules_spellchecker', '[refresh]'));
		$table->setCol(0, 5, array('class' => 'small', 'style' => 'vertical-align:top;color: white;'), g_l('modules_spellchecker', '[delete]'));

		$_lanSelect = new we_html_select(array('style' => 'width: 100px;', 'class' => 'weSelect'));
		foreach(getWeFrontendLanguagesForBackend() as $klan => $vlan){
			$_lanSelect->addOption($klan, $vlan);
		}

		$_langs = (isset($spellcheckerConf['lang']) && is_array($spellcheckerConf['lang'])) ? $spellcheckerConf['lang'] : array();

		$_dir = dir(WE_SPELLCHECKER_MODULE_PATH . 'dict');

		$_i = 0;
		while(false !== ($entry = $_dir->read())){
			if($entry != '.' && $entry != '..' && strpos($entry, '.zip') !== false){
				$_i++;
				$table->addRow();

				$_name = str_replace('.zip', '', $entry);
				$_display = (strlen($_name) > 10) ? (substr($_name, 0, 10) . '...') : $_name;

				$table->setCol($_i, 0, array('style' => 'vertical-align:top'), we_html_forms::radiobutton($_name, (($spellcheckerConf['default'] == $_name) ? true : false), 'default', '', true, 'defaultfont', 'document.we_form.enable_' . $_name . '.value=1;document.we_form._enable_' . $_name . '.checked=true;'));
				$table->setCol($_i, 1, array('style' => 'vertical-align:top', 'class' => 'defaultfont'), $_display);

				$_lanSelect->setAttribute('name', 'lang[' . $_name . ']');
				$_lanSelect->selectOption((isset($_langs[$_name]) ? $_langs[$_name] : $GLOBALS['weDefaultFrontendLanguage']));

				$table->setCol($_i, 2, array('style' => 'vertical-align:top', 'class' => 'defaultfont'), $_lanSelect->getHtml());

				$table->setCol($_i, 3, array('style' => 'vertical-align:top;text-align:center'), we_html_forms::checkboxWithHidden(in_array($_name, $spellcheckerConf['active']), 'enable_' . $_name, '', false, 'defaultfont', ''));
				$table->setCol($_i, 4, array('style' => 'vertical-align:top;text-align:center'), '<div style="display: none;" id="updateIcon_' . $_name . '"><i class="fa fa-2x fa-spinner fa-pulse"></i></div><div style="display: block;" id="updateBut_' . $_name . '">' . we_html_button::create_button(we_html_button::RELOAD, 'javascript: updateDict("' . $_name . '");') . '</div>');
				$table->setCol($_i, 5, array('style' => 'vertical-align:top;text-align:center'), we_html_button::create_button(we_html_button::TRASH, 'javascript: deleteDict("' . $_name . '");'));
			}
		}
		$_dir->close();

		echo we_html_element::jsElement('

				parent.document.getElementById("selector").innerHTML = "' . addslashes(preg_replace("|\r?\n|", '', $table->getHtml())) . '";

			');
		break;

	default:
}
?>
<script><!--
	function dispatch(cmd) {
		document.dispatcherForm.elements["cmd[0]"].value = cmd;
		for (var i = 1; i < arguments.length; i++) {
			document.dispatcherForm.elements["cmd[" + i + "]"].value = arguments[i];
		}
		document.dispatcherForm.submit();
	}
//-->
</script>
</head>

<body>
	<form name="dispatcherForm" method="post" target="_self" action="<?php echo WE_SPELLCHECKER_MODULE_DIR ?>weSpellcheckerCmd.php">
		<input type="hidden" name="cmd[0]" value="" />
		<input type="hidden" name="cmd[1]" value="" />
	</form>
</body>
</html>
