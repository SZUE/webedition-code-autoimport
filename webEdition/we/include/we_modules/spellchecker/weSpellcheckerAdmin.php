<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
include_once(WE_SPELLCHECKER_MODULE_PATH . '/spellchecker.conf.inc.php');
$protect = we_base_moduleInfo::isActive(we_base_moduleInfo::GLOSSARY) && we_users_util::canEditModule(we_base_moduleInfo::GLOSSARY) ? null : [false];
we_html_tools::protect($protect);

if(!permissionhandler::hasPerm('SPELLCHECKER_ADMIN')){
	$cmd = new we_base_jsCmd();
	$cmd->addMsg(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR);
	$cmd->addCmd('close');
	echo $cmd->getCmds();
	exit();
}

echo we_html_tools::getHtmlTop();

$_width = 600;
$space = 5;

$l_param = ['l_dictAdmin' => g_l('modules_spellchecker', '[dictAdmin]'),
	'l_userDictAdmin' => g_l('modules_spellchecker', '[userDictAdmin]'),
	'l_select' => g_l('modules_spellchecker', '[select]'),
	'l_select_words' => g_l('modules_spellchecker', '[select_words]'),
	'l_select_phonetic' => g_l('modules_spellchecker', '[select_phonetic]'),
	'l_build' => g_l('modules_spellchecker', '[build]'),
	'l_close' => g_l('modules_spellchecker', '[close]'),
	'l_encoding' => g_l('modules_spellchecker', '[encoding]'),
	'l_dictname' => g_l('modules_spellchecker', '[dictname]'),
	'l_enc_warning' => g_l('modules_spellchecker', '[enc_warning]'),
	'l_filename_nok' => g_l('modules_spellchecker', '[filename_nok]'),
	'l_filename_warning' => g_l('modules_spellchecker', '[filename_warning]'),
	'l_phonetic_nok' => g_l('modules_spellchecker', '[phonetic_nok]'),
	'l_phonetic_warning' => g_l('modules_spellchecker', '[phonetic_warning]'),
	'l_enc_warning' => g_l('modules_spellchecker', '[enc_warning]'),
	'l_name_warning' => g_l('modules_spellchecker', '[name_warning]'),
	'l_building' => g_l('modules_spellchecker', '[building]'),
	'l_packing' => g_l('modules_spellchecker', '[packing]'),
	'l_uploading' => g_l('modules_spellchecker', '[uploading]'),
	'l_finished' => g_l('modules_spellchecker', '[end]'),
	'upload_size' => getUploadMaxFilesize(),
	'upload_url' => getServerUrl(true) . WE_SPELLCHECKER_MODULE_DIR . 'weSpellcheckerCmd.php',
	'scid' => ''
];

$l_params = '';

foreach($l_param as $key => $value){
	$l_params .= '<param name="' . $key . '" value="' . addslashes($value) . '">';
}



$we_tabs = new we_tabs();

$we_tabs->addTab(g_l('modules_spellchecker', '[dictAdmin]'), false, 1, ["id" => "tab_1"]);
$we_tabs->addTab(g_l('modules_spellchecker', '[userDictAdmin]'), false, 2, ["id" => "tab_2"]);


$js = we_html_element::cssLink(CSS_DIR . 'we_tab.css') . we_html_element::jsScript(JS_DIR . 'initTabs.js');

$table = new we_html_table(['width' => 380, 'style' => 'margin: 5px;'], 3, 5);

$table->setRow(0, ['class' => 'bold', 'style' => 'background-color: silver;'], 5);
$table->setCol(0, 0, ['class' => 'small', 'style' => 'vertical-align:top;color: white;'], g_l('modules_spellchecker', '[default]'));
$table->setCol(0, 1, ['style' => 'vertical-align:top', 'class' => 'small'], g_l('modules_spellchecker', '[dictionary]'));
$table->setCol(0, 2, ['style' => 'vertical-align:top', 'class' => 'small'], g_l('modules_spellchecker', '[active]'));
$table->setCol(0, 3, ['style' => 'vertical-align:top', 'class' => 'small'], g_l('modules_spellchecker', '[refresh]'));
$table->setCol(0, 4, ['style' => 'vertical-align:top', 'class' => 'small'], g_l('modules_spellchecker', '[delete]'));

$_dir = dir(WE_SPELLCHECKER_MODULE_PATH . 'dict');

$i = 0;
while(false !== ($entry = $_dir->read())){
	if($entry != '.' && $entry != '..' && strpos($entry, '.zip') !== false){
		$i++;
		$table->addRow();

		$_name = str_replace('.zip', '', $entry);

		$table->setCol($i, 0, ['style' => 'vertical-align:top'], we_html_forms::radiobutton($_name, (($spellcheckerConf['default'] == $_name) ? true : false), 'default', '', true, 'defaultfont', 'document.we_form.enable_' . $_name . '.value=1;document.we_form._enable_' . $_name . '.checked=true;'));
		$table->setCol($i, 1, ['style' => 'vertical-align:top', 'class' => 'defaultfont'], $_name);
		$table->setCol($i, 2, ['style' => 'vertical-align:top;text-align:right'], we_html_forms::checkboxWithHidden(in_array($_name, $spellcheckerConf['active']), 'enable_' . $_name, '', false, 'defaultfont', ''));
		$table->setCol($i, 3, ['style' => 'vertical-align:top;text-align:right'], we_html_button::create_button(we_html_button::RELOAD, 'javascript: updateDict("' . $_name . '");'));
		$table->setCol($i, 4, ['style' => 'vertical-align:top;text-align:right'], we_html_button::create_button(we_html_button::TRASH, 'javascript: deleteDict("' . $_name . '");'));
	}
}
$_dir->close();

$_button = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");
$tabsBody = $we_tabs->getHTML();

$tab_1 = we_html_tools::htmlDialogLayout('
	 <form name="we_form" target="hiddenCmd" method="post" action="' . WE_SPELLCHECKER_MODULE_DIR . 'weSpellcheckerCmd.php">
	 <input type="hidden" name="cmd[0]" value="saveSettings" />
	 <div id="dictTable">
	 	<div id="selector" class="blockWrapper" style="width: 400px; height: 320px; border: 1px solid #AFB0AF;margin-bottom: 5px;background-color:#f6f6f6 ! important;"></div>

		<div id="dictSelector" style="display: none; width: 400px; height: 220px;background-color: silver;">
			<div id="appletPanel"></div>
		</div>
		<div id="addButt">' . we_html_button::create_button(we_html_button::SAVE, "javascript:document.we_form.submit()") . we_html_button::create_button(we_html_button::ADD, "javascript:showDictSelector();") . '</div>
	</div>
	 ', '', '');


$tab_2 = we_html_tools::htmlDialogLayout('
					<textarea class="defaultfont" name="defaultDict" style="width: 400px; padding:5px;height: 320px; border: 1px solid #AFB0AF;margin-bottom: 5px;background-color:white ! important;">' . (file_exists(WE_SPELLCHECKER_MODULE_PATH . 'dict/default.inc.php') ? ((filesize(WE_SPELLCHECKER_MODULE_PATH . 'dict/default.inc.php') > 0) ? we_base_file::load(WE_SPELLCHECKER_MODULE_PATH . 'dict/default.inc.php') : '') : '') . '</textarea>
					<div>' . we_html_button::create_button(we_html_button::SAVE, "javascript:document.we_form.submit()") . '</div>
	</form>
	 ', '', '');


$_username = $_SESSION['user']['Username'];
foreach(['\\', '/', ':', '*', '?', '<', '>', '|', '"'] as $cur){
	$_username = str_replace($cur, 'MASK' . $i, $_username);
}

$_applet_code = we_html_element::htmlApplet(['name' => 'spellchecker',
		'code' => 'com/livinge/spellchecker/swing/DictEditor.class',
		'archive' => 'lespellchecker.jar',
		'codebase' => getServerUrl(true) . WE_SPELLCHECKER_MODULE_DIR,
		'width' => 400,
		'height' => 220,
		], '
<param name="code" value="com/livinge/spellchecker/swing/DictEditor.class"/>
<param name="archive" value="lespellchecker.jar"/>
<param name="type" value="application/x-java-applet;version=1.1"/>
<param name="dictBase" value="' . getServerUrl(true) . WE_SPELLCHECKER_MODULE_DIR . '/dict/"/>
<param name="dictionary" value="' . (isset($_SESSION['weS']['dictLang']) ? $_SESSION['weS']['dictLang'] : 'Deutsch') . '"/>
<param name="debug" value="off"><param name="user" value="' . $_username . '@' . $_SERVER['SERVER_NAME'] . '"/>
<param name="udSize" value="' . (is_file(WE_SPELLCHECKER_MODULE_PATH . '/dict/' . $_username . '.dict') ? filesize(WE_SPELLCHECKER_MODULE_PATH . '/dict/' . $_username . '.dict') : '0') . '"/>' .
		$l_params);
$_applet_code2 = we_html_element::htmlApplet(['name' => "spellcheckerCmd",
		'code' => "LeSpellchecker.class",
		'archive' => "lespellchecker.jar",
		'codebase' => getServerUrl(true) . WE_SPELLCHECKER_MODULE_DIR,
		'width' => 20,
		'height' => 20,], '
<param name="scriptable" value="true"/>
<param name="mayscript" value="true"/>
<param name="CODE" value="LeSpellchecker.class"/>
<param name="ARCHIVE" value="lespellchecker.jar"/>
<param name="type" value="application/x-java-applet;version=1.1"/>
<param name="dictBase" value="' . getServerUrl(true) . WE_SPELLCHECKER_MODULE_DIR . '/dict/"/>
<param name="dictionary" value="' . (isset($_SESSION['weS']['dictLang']) ? $_SESSION['weS']['dictLang'] : 'Deutsch') . '"/>
<param name="debug" value="off"><param name="user" value="' . $_username . '@' . $_SERVER['SERVER_NAME'] . '"/>
<param name="udSize" value="' . (is_file(WE_SPELLCHECKER_MODULE_PATH . '/dict/' . $_username . '@' . $_SERVER['SERVER_NAME'] . '.dict') ? filesize(WE_SPELLCHECKER_MODULE_PATH . '/dict/' . $_username . '@' . $_SERVER['SERVER_NAME'] . '.dict') : '0') . '"/>');
?>

	<script><!--
		var activ_tab = 1;
		var appletActiv = false;
		var g_l = {
			dict_saved: "<?= we_message_reporting::prepareMsgForJS(g_l('modules_spellchecker', '[dict_saved]')); ?>",
			ask_dict_del: "<?= g_l('modules_spellchecker', '[ask_dict_del]'); ?>"
		};

		function setAppletCode() {
			if (!appletActiv) {
				document.getElementById('appletPanel').innerHTML = '<?= addcslashes(str_replace("\n", '', $_applet_code), '\''); ?>';
			}
			appletActiv = true;
			setTimeout(checkApplet, 2000);
		}


		function updateDict(dict) {

			setVisible("updateBut_" + dict, false);
			setVisible("updateIcon_" + dict, true);
			document.getElementById('appletPanel2').innerHTML = '<?= addcslashes(str_replace("\n", '', $_applet_code2), '\''); ?>';
			setTimeout(selectDict, 1000, dict);
	}

	function updateDict(dict) {

			setVisible("updateBut_" + dict, false);
			setVisible("updateIcon_" + dict, true);
			document.getElementById('appletPanel2').innerHTML = '<?= addcslashes(str_replace("\n", '', $_applet_code2), '\''); ?>';
			setTimeout(selectDict, 1000, dict);
		}
	//-->
</script>
<?=
$js .
 we_html_element::jsScript(WE_JS_MODULES_DIR . 'spellchecker/weSpellcheckerAdmin.js');
?>
</head>

			<body onload="loadTable();
		if (!activ_tab) {
			activ_tab = 1;
		}
		document.getElementById('tab_' + activ_tab).className = 'tabActive';" class="weDialogBody">

			<?= $tabsBody; ?>

			<div id="content" style="margin: 10px; width: 450px;">
		<div id="tab1" style="display:block;">
			<?= $tab_1 ?>

		</div>
		<div id="tab2" style="display:none;">
			<?= $tab_2 ?>
		</div>

	</div>

	<div class="editfooter"><?= $_button; ?></div>

	<iframe name="hiddenCmd" id="hiddenCmd" style="position: absolute; left:0px; top:800px; display: block; border: 0px; width: 0px; height: 0px;" src="<?= WE_SPELLCHECKER_MODULE_DIR . 'weSpellcheckerCmd.php'; ?>"></iframe>

	<div id="appletPanel2" style="position: absolute; left:0px; top:900px; display: block; border: 0px; width: 0px; height: 0px;">
	</div>

</body>

</html>