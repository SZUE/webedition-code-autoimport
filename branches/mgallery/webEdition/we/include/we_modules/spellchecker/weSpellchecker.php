<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
include_once(WE_SPELLCHECKER_MODULE_PATH . '/spellchecker.conf.inc.php');
$protect = we_base_moduleInfo::isActive('glossary') && we_users_util::canEditModule('glossary') ? null : array(false);
we_html_tools::protect($protect);

$editname = we_base_request::_(we_base_request::STRING, 'we_dialog_args', false, 'editname');
echo we_html_tools::getHtmlTop() .
 we_html_element::jsScript(TINYMCE_SRC_DIR . 'tiny_mce_popup.js') .
 we_html_element::jsScript(TINYMCE_SRC_DIR . 'utils/mctabs.js') .
 we_html_element::jsScript(TINYMCE_SRC_DIR . 'utils/form_utils.js') .
 we_html_element::jsScript(TINYMCE_SRC_DIR . 'utils/validate.js') .
 we_html_element::jsScript(TINYMCE_SRC_DIR . 'utils/editable_selects.js') .
 STYLESHEET;

if(!isset($_SESSION['weS']['dictLang'])){
	$_SESSION['weS']['dictLang'] = $spellcheckerConf['default'];
}

$_username = $_SESSION['user']['Username'];
$_replacement = array('\\', '/', ':', '*', '?', '<', '>', '|', '"');
for($_i = 0; $_i < count($_replacement); $_i++){
	$_username = str_replace($_replacement[$_i], 'MASK' . $_i, $_username);
}

$_user_dict = WE_SPELLCHECKER_MODULE_PATH . '/dict/' . $_username . '@' . $_SERVER['SERVER_NAME'] . '.dict';

if(!file_exists($_user_dict) && file_exists(WE_SPELLCHECKER_MODULE_PATH . '/dict/default.inc.php')){
	copy(WE_SPELLCHECKER_MODULE_PATH . '/dict/default.inc.php', $_user_dict);
}

$_width = 600;
$space = 5;

$_mode = 'normal';

$_applet_code = we_html_element::htmlApplet(array(
		'name' => "spellchecker",
		'code' => "LeSpellchecker.class",
		'archive' => "lespellchecker.jar",
		'codebase' => getServerUrl(true) . WE_SPELLCHECKER_MODULE_DIR,
		'width' => 20,
		'height' => 20,
		), '
<param name="scriptable" value="true"/>
<param name="mayscript" value="true"/>
<param name="code" value="LeSpellchecker.class"/>
<param name="archive" value="lespellchecker.jar"/>
<param name="type" value="application/x-java-applet;version=1.1"/>
<param name="dictBase" value="' . getServerUrl(true) . WE_SPELLCHECKER_MODULE_DIR . '/dict/"/>
<param name="dictionary" value="' . (isset($_SESSION['weS']['dictLang']) ? $_SESSION['weS']['dictLang'] : 'Deutsch') . '"/>
<param name="debug" value="off"><param name="user" value="' . $_username . '@' . $_SERVER['SERVER_NAME'] . '"/>
<param name="udSize" value="' . (is_file($_user_dict) ? filesize($_user_dict) : '0') . '"/>	'
);


if($editname !== false){
	$_mode = 'tinyMce';
}
?>

<style type="text/css">
	#mainPanel {
		width: 100%;
		height: 380px;
	}

	#previewPanel {
		width: 450px;
		height: 100px;
		margin-bottom: 10px;
	}

	#spellcheckPanel{
		width: 460px;
		height: 150px;
		padding: 5px;
		margin-bottom: 5px;
	}

	#preview {
		overflow: auto !important;
		border: 1px solid #AAAAAA;
		background: #EEEEEE;
		width: 450px;
		height: 120px;
		margin-bottom: 5px;
	}

	#searchPanel {
		float: left;
		width: 340px;
		height: 150px;
	}

	#search {
		float: left;
		width: 330px;
	}

	#suggestion {
		width: 330px;
		height: 100px;
		border: 1px solid grey;
		z-index: 3;
	}

	#buttonPanel {
		float:right;
	}

	#appletPanel {
		display: block;
	}

	#dictPanel {
		width: 464px;
		height: 40px;
		padding: 3px;
	}

	#dictSelect {
		width: 350px;
	}

	#spinner {
		width: 500px;
		height: 450px;
		z-index: 1;
		position: absolute;
		left:0px;
		top:0px;
		background: white;
		filter: alpha(opacity=80);
		opacity: .8;
	}

	.highlight {
		color:blue;
		background-color:yellow;
	}

</style>

<script type="text/javascript"><!--
	var mode = "<?php echo $_mode; ?>";
	var editname="<?php echo $editname;?>";
	var orginal;
	var editPanel;
	var to;
	var found;
	var editorObj;
	var rangeSelection = false;
	var currentWord = "";
	var retry = 0;
	var retryjava = 0;

	function spellcheck() {
		retry = 0;
		if (document.spellchecker.isReady()) {
			document.getElementById("statusText").innerHTML = "<?php echo g_l('modules_spellchecker', '[checking]'); ?>";
			var text = getTextOnly(orginal);
			document.spellchecker.check(text);
			setTimeout(findNext, 2000);
		} else {
			if (retryjava < 5) {
				setTimeout(spellcheck, 1000);
				retryjava++;
			} else {
<?php echo we_message_reporting::getShowMessageCall(g_l('modules_spellchecker', '[no_java]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
				self.close();
			}
		}
	}


	function findNext() {
		if (document.spellchecker.isReady()) {
			if (document.spellchecker.isReady()) {
				if (document.spellchecker.nextSuggestion()) {

					fadeout("spinner", 80, 10, 10);

					document.we_form.search.value = document.spellchecker.getMisspelledWord();
					currentWord = document.we_form.search.value;
					markWord(document.we_form.search.value);
					found = document.we_form.search.value;
					var suggs = document.spellchecker.getSuggestions();
					suggs = suggs + "";
					var suggA = suggs.split("|");
					document.we_form.suggestion.options.length = 0;
					if (suggA.length > 1) {
						for (var i = 0; i < suggA.length; i++) {
							document.we_form.suggestion.options[document.we_form.suggestion.options.length] = new Option(suggA[i], suggA[i]);
							if (i === 0) {
								document.we_form.suggestion.options.selectedIndex = 0;
								document.we_form.search.value = suggA[i];
							}
						}
					}
					enableButtons();
				} else {
					disableButtons();

					if (document.spellchecker.isWorking()) {
						clearTimeout(to);
						to = setTimeout(findNext, 500);
					} else {
						removeHighlight();
						if (document.getElementById("spinner").style.display != "none") {
							fadeout("spinner", 80, 10, 10);
						}
						weButton.enable("check");
<?php echo we_message_reporting::getShowMessageCall(g_l('modules_spellchecker', '[finished]'), we_message_reporting::WE_MESSAGE_NOTICE); ?>
					}
				}
			} else {
				setTimeout(spellcheck, 500);
			}
		}
	}

	function add() {
		if (document.spellchecker.isReady) {
			document.spellchecker.addWord(currentWord);
			hiddenCmd.dispatch("addWord", currentWord);
			findNext();
		} else {
<?php echo we_message_reporting::getShowMessageCall("A fatal error occured", we_message_reporting::WE_MESSAGE_ERROR); ?>
		}
	}

	function setAppletCode() {
		retryjava = 0;
		document.getElementById('appletPanel').innerHTML = '<?php echo addcslashes(str_replace("\n", '', $_applet_code), '\'') ?>';
		setTimeout(spellcheck, 1000);
	}

//-->
</script>
<?php
echo we_html_element::jsScript(JS_DIR . 'we_modules/spellchecker/weSpellchecker.js');
?>
</head>

<body class="weDialogBody" onload="setDialog()"><?php
	$_preview = '<div id="preview" class="defaultfont"></div>';


	$_leftPanel = '<div id="searchPanel">
		<input class="wetextinput" name="search" id="search" />
		<br />
		<label for="suggestion" class="defaultfont">' . g_l('modules_spellchecker', '[suggestion]') . '</label>
		<select name="suggestion" id="suggestion" size="5" class="wetextinput" onchange="document.we_form.search.value=this.value;">
		</select>
	</div>';


	$_buttonsleft = array(
		we_html_button::create_button("ignore", "javascript:findNext();", true, 100, 22, '', '', true, false),
		we_html_button::create_button("change", "javascript:changeWord();", true, 100, 22, '', '', true, false),
		we_html_button::create_button("add", "javascript:add();", true, 100, 22, '', '', true, false),
		we_html_button::create_button("check", "javascript:weButton.disable(\"check\");setTimeout(spellcheck,100);", true, 100, 22, '', '', true, false)
	);

	$_applet = '<div id="appletPanel" style="position: absolute; left:0px; top:900px; display: block; border: 0px; width: 0px; height: 0px;"></div>';

	$_buttons = array(
		we_html_button::create_button("apply", "javascript:apply();self.close();"),
		we_html_button::create_button("cancel", "javascript:self.close();")
	);
	$_buttons_bottom = we_html_button::position_yes_no_cancel($_buttons[0], null, $_buttons[1]);

	$_selectCode = '<select name="dictSelect" id="dictSelect" size="1" onchange="selectDict(this.value)">';

	$_dir = dir(WE_SPELLCHECKER_MODULE_PATH . 'dict');
	$_i = 0;
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

	$_parts = array(
		array(
			'headline' => '',
			'html' => $_preview,
			'space' => 0
		),
		array(
			'headline' => '',
			'html' => $_leftPanel . implode('<div style="margin:5px;"></div>', $_buttonsleft),
			'space' => 0
		),
		array(
			'headline' => g_l('modules_spellchecker', '[dictionary]'),
			'html' => $_selectCode,
			'space' => 100
		)
	);


	echo '<div id="spinner">
		<center>
			<div style="padding-top: 30%;">
				<img src="' . IMAGE_DIR . 'spinner.gif"/><br />
				<div id="statusText" class="small" style="color: black;">' . g_l('modules_spellchecker', '[download]') . '</div>
			</div>
		</center>
	</div>

	<form name="we_form" action="' . WE_SPELLCHECKER_MODULE_DIR . '/weSpellchecker.php" method="post" target="_self">

	<input name="' . ($_mode === 'wysiwyg' ? 'we_dialog_args[editname]' : 'editname') . '" value="' . $editname . '" type="hidden" />
	<div id="mainPanel">' .
	we_html_multiIconBox::getHTML('', "100%", $_parts, 30, $_buttons_bottom, -1, '', '', false, g_l('modules_spellchecker', '[spellchecker]')) . '
	</div>
	</form>' .
	$_applet .
	'<iframe name="hiddenCmd" id="hiddenCmd" style="position: absolute; left:0px; top:800px; display: block; border: 0px; width: 0px; height: 0px;" src="' . WE_SPELLCHECKER_MODULE_DIR . 'weSpellcheckerCmd.php"></iframe>';
	?>
</body>
</html>