<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
include_once(WE_SPELLCHECKER_MODULE_PATH . '/spellchecker.conf.inc.php');
$protect = we_base_moduleInfo::isActive(we_base_moduleInfo::GLOSSARY) && we_users_util::canEditModule(we_base_moduleInfo::GLOSSARY) ? null : [false];
we_html_tools::protect($protect);

$editname = we_base_request::_(we_base_request::STRING, 'we_dialog_args', false, 'editname');

if(!isset($_SESSION['weS']['dictLang'])){
	$_SESSION['weS']['dictLang'] = $spellcheckerConf['default'];
}

$_username = $_SESSION['user']['Username'];
$_replacement = ['\\', '/', ':', '*', '?', '<', '>', '|', '"'];
for($i = 0; $i < count($_replacement); $i++){
	$_username = str_replace($_replacement[$i], 'MASK' . $i, $_username);
}

$_user_dict = WE_SPELLCHECKER_MODULE_PATH . '/dict/' . $_username . '@' . $_SERVER['SERVER_NAME'] . '.dict';

if(!file_exists($_user_dict) && file_exists(WE_SPELLCHECKER_MODULE_PATH . '/dict/default.inc.php')){
	copy(WE_SPELLCHECKER_MODULE_PATH . '/dict/default.inc.php', $_user_dict);
}

$_width = 600;
$space = 5;

$_mode = 'normal';

if($editname !== false){
	$_mode = 'tinyMce';
}

/* JS:
  var mode = "<?= $_mode; ?>";
  var editname = "<?= $editname; ?>";
 */
echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(TINYMCE_SRC_DIR . 'tiny_mce_popup.js') .
		we_html_element::jsScript(TINYMCE_SRC_DIR . 'utils/mctabs.js') .
		we_html_element::jsScript(TINYMCE_SRC_DIR . 'utils/form_utils.js') .
		we_html_element::jsScript(TINYMCE_SRC_DIR . 'utils/validate.js') .
		we_html_element::jsScript(TINYMCE_SRC_DIR . 'utils/editable_selects.js') .
		we_html_element::cssLink(CSS_DIR . 'weSpellchecker.css') .
		we_html_element::jsScript(WE_JS_MODULES_DIR . 'spellchecker/weSpellchecker.js'));
?>
<body class="weDialogBody" onload="setDialog()"><?php
$_preview = '<div id="preview" class="defaultfont"></div>';


$_leftPanel = '<div id="searchPanel">
		<input class="wetextinput" name="search" id="search" />
		<br />
		<label for="suggestion" class="defaultfont">' . g_l('modules_spellchecker', '[suggestion]') . '</label>
		<select name="suggestion" id="suggestion" size="5" class="wetextinput" onchange="document.we_form.search.value=this.value;">
		</select>
	</div>';


$_buttonsleft = [we_html_button::create_button('ignore', "javascript:findNext();", '', 0, 0, '', '', true, false),
	we_html_button::create_button('change', "javascript:changeWord();", '', 0, 0, '', '', true, false),
	we_html_button::create_button(we_html_button::ADD, "javascript:add();", '', 0, 0, '', '', true, false),
	we_html_button::create_button('check', "javascript:WE().layout.button.disable(document, \"check\");setTimeout(spellcheck,100);", '', 0, 0, '', '', true, false)
];


$buttons = [we_html_button::create_button('apply', "javascript:apply();self.close();"),
	we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();")
];
$_buttons_bottom = we_html_button::position_yes_no_cancel($buttons[0], null, $buttons[1]);

$_selectCode = '<select name="dictSelect" id="dictSelect" onchange="selectDict(this.value)">';

$_dir = dir(WE_SPELLCHECKER_MODULE_PATH . 'dict');
$i = 0;
while(false !== ($entry = $_dir->read())){
	if($entry != '.' && $entry != '..' && strpos($entry, '.zip') !== false){
		$_name = str_replace('.zip', '', $entry);
		if(in_array($_name, $spellcheckerConf['active'])){
			$_selectCode .= '<option value="' . $_name . '" ' . ((isset($_SESSION['weS']['dictLang']) && $_SESSION['weS']['dictLang'] == $_name) ? 'selected' : '') . '>' . $_name . '</option>';
		}
	}
}
$_dir->close();

$_selectCode .= '</select>';

$parts = [['headline' => '',
 'html' => $_preview,
	],
	['headline' => '',
		'html' => $_leftPanel . implode('<div style="margin:5px;"></div>', $_buttonsleft),
	],
	['headline' => g_l('modules_spellchecker', '[dictionary]'),
		'html' => $_selectCode,
		'space' => we_html_multiIconBox::SPACE_MED
	]
];


echo '<div id="spinner" style="text-align:center">
			<div style="padding-top: 30%;">
				<i class="fa fa-2x fa-spinner fa-pulse"></i><br />
				<div id="statusText" class="small" style="color: black;">' . g_l('modules_spellchecker', '[download]') . '</div>
			</div>
	</div>

	<form name="we_form" action="' . WE_SPELLCHECKER_MODULE_DIR . '/weSpellchecker.php" method="post" target="_self">

	<input name="' . ($_mode === 'wysiwyg' ? 'we_dialog_args[editname]' : 'editname') . '" value="' . $editname . '" type="hidden" />
	<div id="mainPanel">' .
 we_html_multiIconBox::getHTML('', $parts, 30, $_buttons_bottom, -1, '', '', false, g_l('modules_spellchecker', '[spellchecker]')) . '
	</div>
	</form>';
?>
</body>
</html>