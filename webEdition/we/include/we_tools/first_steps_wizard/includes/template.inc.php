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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


// print HTML head
we_html_tools::htmlTop($GLOBALS['lang']['Template']['title']);

// generate needed buttons
$ButtonBack = we_button::create_button('back', 'javascript:leWizardForm.back();', true, 100, 22, "", "", true, false);
$ButtonNext = we_button::create_button('next', 'javascript:leWizardForm.next();', true, 100, 22, "", "", false, false);
$ButtonReload = we_button::create_button(
		'image:function_reload',
		'javascript:leWizardForm.reload();',
		true,
		40,
		22,
		"",
		"",
		true,
		false);

// Preview
$ButtonClose = we_button::create_button(
		'close',
		'javascript:top.frames[\'leLoadFrame\'].hidePreview();',
		true,
		100,
		22,
		"",
		"",
		false,
		false);
$ButtonBackPreview = we_button::create_button(
		'image:direction_left',
		'javascript:top.frames[\'leLoadFrame\'].backPreview();',
		true,
		40,
		22,
		"",
		"",
		false,
		false);
$ButtonNextPreview = we_button::create_button(
		'image:direction_right',
		'javascript:top.frames[\'leLoadFrame\'].nextPreview();',
		true,
		40,
		22,
		"",
		"",
		false,
		false);

print we_html_element::cssLink(CSS_DIR.'global.php').
we_html_element::cssLink(CSS_DIR.'first_steps_wizard.css.php').
we_html_element::cssLink(CSS_DIR.'we_button.css').

we_html_element::jsScript(JS_DIR.'weButton.js').
we_html_element::jsScript(JS_DIR.'leWizard/leWizardForm.js').
we_html_element::jsScript(JS_DIR.'windows.js').

we_html_element::jsElement('
		var nextUrl = "";
		var backUrl = "";
		var repeatUrl = "";
	');

// Status Bar
$Status = new leWizardStatus();
echo $Status->getCSS();
echo $Status->getJSCode();

// ProgressBar
$Progress = new leWizardProgress();
echo $Progress->getCSS();
echo $Progress->getJSCode();

// Content
$Content = new leWizardContent();
echo $Content->getCSS();
echo $Content->getJSCode();

?>

</head>

<body>

<form action="<?php
print WEBEDITION_DIR . 'we_cmd.php'?>"
	target="leLoadFrame" method="post" name="leWebForm"><input
	type="hidden" name="we_cmd[0]"
	value="<?php
	echo $_REQUEST['we_cmd'][0];
	?>" /> <input type="hidden"
	name="leWizard" value="" /> <input type="hidden" name="leStep" value="" />
<input type="hidden" name="liveUpdateSession" value="" />

<div id="leWizardTitle">
	<?php
	echo $GLOBALS['lang']['Template']['headline'];
	?>
</div>

<div id="leWizardStatus"><!--<?php
echo $Status->get($WizardCollection, false, null, null);
?>-->
</div>

<div id="leWizard">
<div id="leWizardBorderLeft"></div>
<div id="leWizardContentLeft">
<div id="leWizardHeadline"></div>
		<?php
		echo $Content->get();
		?>
		<div id="leWizardPostContent">
		<?php
		echo $ButtonReload;
		echo $Progress->get();
		?>
		</div>
</div>
<div id="leWizardContentRight">
<div id="leWizardEmoticon"></div>
		<?php
		echo $Content->getDescription();
		echo $ButtonBack;
		echo $ButtonNext;
		?>
	</div>
<div id="leWizardBorderRight"></div>

</div>

<div id="leWizardPreviewContainer"></div>

<div id="leWizardPreview">
<div id="leWizardPreviewImageContainer"><img src="#"
	id="leWizardPreviewImage" width="1" height="1" border="0" alt="" /></div>
<div id="leWizardPreviewText" class="defaultfont"></div>
	<?php
	echo $ButtonBackPreview;
	echo $ButtonNextPreview;
	echo $ButtonClose;
	?>
</div>

<div id="debug" style="visibility: <?php
echo (isset($_REQUEST['debug']) ? "block" : "hidden");
?>">
<iframe src="<?php
print $WizardCollection->getFirstStepUrl();
?>"
	name="leLoadFrame" width="620" height="100" frameborder="0"></iframe></div>


<script type="text/javascript">
	document.onkeypress = leWizardForm.checkSubmit;
</script>

</body>
</html>