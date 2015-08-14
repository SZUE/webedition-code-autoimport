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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect(array('BROWSE_SERVER', 'SITE_IMPORT', 'ADMINISTRATOR'));

$supportDebuggingFile = WEBEDITION_PATH . 'we_sselector_inc.php';
$GLOBALS['supportDebugging'] = false;
if(file_exists($supportDebuggingFile)){
	include($supportDebuggingFile);
	if(defined('SUPPORT_IP') && defined('SUPPORT_DURATION') && defined('SUPPORT_START')){
		if(SUPPORT_IP == $_SERVER['REMOTE_ADDR'] && (time() - SUPPORT_DURATION) < SUPPORT_START){
			$GLOBALS['supportDebugging'] = true;
		}
	}
}

echo we_html_tools::getHtmlTop() . STYLESHEET;
$nf = we_base_request::_(we_base_request::RAW, 'nf');
$sid = we_base_request::_(we_base_request::RAW, "sid");
?>
<script><!--
	function setScrollTo() {
		parent.scrollToVal = pageYOffset;
	}
//-->
</script><?php
echo we_html_element::jsScript(JS_DIR . 'selectors/we_sselector_body.js') .
 we_html_element::cssLink(CSS_DIR . 'selectors.css');
?>

</head>
<body style="background-color:white" LINK="#000000" ALINK="#000000" VLINK="#000000" onload="doScrollTo();">
	<form name="we_form" target="fscmd" action="we_sselector_cmd.php" method="post" onsubmit="return false;">
		<table class="default"><?php

			function getDataType($dat){
				$ct = getContentTypeFromFile($dat);
				return (($ct = g_l('contentTypes', '[' . $ct . ']', true)) !== false ?
						$ct : '');
			}

			$arDir = $arFile = $ordDir = $ordFile = $final = array();

			$org = we_base_request::_(we_base_request::FILE, "dir", '/');


			$dir = rtrim($_SERVER['DOCUMENT_ROOT'] . $org, '/');
			@chdir($dir);
			$dir_obj = @dir($dir);

			$ord = we_base_request::_(we_base_request::INT, "ord", 10);

			if($dir_obj){
				while(false !== ($entry = $dir_obj->read())){
					if($entry != '.' && $entry != '..'){
						if(is_link($dir . '/' . $entry) || is_dir($dir . '/' . $entry)){
							$arDir[] = $entry;
							switch($ord){
								case 10:
								case 11:
									$ordDir[] = $entry;
									break;
								case 20:
								case 21:
									$ordDir[] = getDataType($dir . '/' . $entry);
									break;
								case 30:
								case 31:
									$ordDir[] = filectime($dir . '/' . $entry);
									break;
								case 40:
								case 41:
									$ordDir[] = filesize($dir . '/' . $entry);
									break;
							}
						} else {
							$arFile[] = $entry;
							switch($ord){
								case 10:
								case 11:
									$ordFile[] = $entry;
									break;
								case 20:
								case 21:
									$ordFile[] = getDataType($dir . '/' . $entry);
									break;
								case 30:
								case 31:
									$ordFile[] = filectime($dir . '/' . $entry);
									break;
								case 40:
								case 41:
									$ordFile[] = filesize($dir . '/' . $entry);
									break;
							}
						}
					}
				}
				$dir_obj->close();
			} else {
				echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR)) . '<div class="middlefontgray"  style="padding-top:2em;text-align:center">-- ' . g_l('alert', '[access_denied]') . ' --</div>';
			}

			switch($ord){
				case 10:
				case 20:
				case 30:
				case 40:
					asort($ordDir);
					asort($ordFile);
					break;
				case 11:
				case 21:
				case 31:
				case 41:
					arsort($ordDir);
					arsort($ordFile);
					break;
			}

			foreach($ordDir as $key => $value){
				$final[] = $arDir[$key];
			}
			foreach($ordFile as $key => $value){
				$final[] = $arFile[$key];
			}

			$js = 'top.allentries = [];
var i = 0;';
			foreach($final as $key => $entry){
				$js.='top.allentries[i++] = "' . $entry . '";';
			}
			echo we_html_element::jsElement($js);
			$set_rename = false;

			if($nf === 'new_folder'){
				?>
				<tr style="background-color:#DFE9F5;">
					<td class="selector treeIcon"><?php echo we_html_element::jsElement('document.write(getTreeIcon("folder"));') ?></td>
					<td class="selector filename"><?php echo we_html_tools::htmlTextInput("txt", 20, g_l('fileselector', '[new_folder_name]'), "", 'id="txt" onblur="setScrollTo();we_form.submit();" onkeypress="keypressed(event)"', "text", "100%"); ?></td>
					<td class="selector filetype"><?php echo g_l('fileselector', '[folder]') ?></td>
					<td class="selector moddate"><?php echo date("d.m.Y H:i:s") ?></td>
					<td class="selector filesize"></td>
				</tr>
				<?php
			}
			$thumbFold = trim(WE_THUMBNAIL_DIRECTORY, '/');
			$selectOwn = we_base_request::_(we_base_request::BOOL, 'selectOwn', false);
			foreach($final as $key => $entry){
				$name = str_replace('//', '/', $org . '/' . $entry);


				$islink = is_link($dir . '/' . $entry);
				$isfolder = is_dir($dir . '/' . $entry) && !$islink;

				$type = $isfolder ? g_l('contentTypes', '[folder]') : getDataType($dir . '/' . $entry);

				$indb = f('SELECT ContentType FROM ' . FILE_TABLE . ' WHERE Path="' . $DB_WE->escape($name) . '"');
				switch($entry){
					case 'webEdition':
					case WE_THUMBNAIL_DIRECTORY:
					case $thumbFold:
						$indb = 'folder';
					default:
						if((preg_match('|^' . $_SERVER['DOCUMENT_ROOT'] . '/?webEdition/|', $dir) || preg_match('|^' . $_SERVER['DOCUMENT_ROOT'] . '/?webEdition$|', $dir)) && (!preg_match('|^' . $_SERVER['DOCUMENT_ROOT'] . '/?webEdition/we_backup|', $dir) || $entry === "download" || $entry === 'tmp')){
							$indb = 'folder';
						}
				}
				if($supportDebugging){
					$indb = false;
				}
				$file = we_base_request::_(we_base_request::STRING, 'file'); //FIXME: totaler nonsense!!
				$show = ($entry != '.') && ($entry != '..') && (($file == g_l('contentTypes', '[all_Types]')) || ($type == g_l('contentTypes', '[folder]')) || ($type == $file || $file == ''));
				$bgcol = (we_base_request::_(we_base_request::FILE, 'curID') == ($dir . '/' . $entry) && !( $nf === 'new_folder')) ? '#DFE9F5' : 'white';
				$onclick = $ondblclick = '';
				$_cursor = 'cursor:default;';
				if(!(( $nf === 'rename_folder' || $nf === 'rename_file') && ($entry == $sid) && ($isfolder))){
					if($indb){
						if($isfolder){
							$onclick = ' onclick="tout=setTimeout(\'if(!wasdblclick){doClick(\\\'' . $entry . '\\\',1,' . ($indb ? 1 : 0) . ');}else{wasdblclick=false;}\',300);return true;"';
							$ondblclick = 'onDblClick="wasdblclick=true;clearTimeout(tout);doClick(\'' . $entry . '\',1,' . ($indb ? 1 : 0) . ');return true;"';
							$_cursor = 'cursor:pointer;';
						} elseif($selectOwn){
							$onclick = 'onclick="if(old==\'' . $entry . '\') mk=setTimeout(\'if(!wasdblclick){ clickEditFile(old);}\',500); old=\'' . $entry . '\';doClick(\'' . $entry . '\',0,0);return true;"';
							$_cursor = 'cursor:pointer;';
						}
					} else if(!$indb){
						if($isfolder){
							$onclick = 'onclick="if(old==\'' . $entry . '\') mk=setTimeout(\'if(!wasdblclick){clickEdit(old);}\',500); old=\'' . $entry . '\';doSelectFolder(\'' . $entry . '\',' . ($indb ? 1 : 0) . ');"';
							$ondblclick = 'onDblClick="wasdblclick=true;clearTimeout(tout);clearTimeout(mk);doClick(\'' . $entry . '\',1,0);return true;"';
						} else {
							$onclick = 'onclick="if(old==\'' . $entry . '\') mk=setTimeout(\'if(!wasdblclick){ clickEditFile(old);}\',500); old=\'' . $entry . '\';doClick(\'' . $entry . '\',0,0);return true;"';
						}
						$_cursor = 'cursor:pointer;';
					}
				}

				$filesize = file_exists($dir . '/' . $entry) ? filesize($dir . '/' . $entry) : 0;

				$_size = ($isfolder ?
						'' :
						($islink ?
							'-> ' . readlink($dir . '/' . $entry) :
							'<span' . ($indb ? ' style="color:#006699"' : '') . ' title="' . oldHtmlspecialchars($filesize) . '">' . we_base_file::getHumanFileSize($filesize) . '</span>'));

				switch((($entry == $sid) && (!$indb) ? $nf : '')){
					case "rename_folder":
						if($isfolder){
							$_text_to_show = we_html_tools::htmlTextInput("txt", 20, $entry, "", 'onblur="setScrollTo();we_form.submit();" onkeypress="keypressed(event)"', "text", "100%");
							$set_rename = true;
							$_type = g_l('contentTypes', '[folder]');
							$_date = date("d.m.Y H:i:s");
						}
						break;
					case "rename_file":
						$_text_to_show = we_html_tools::htmlTextInput("txt", 20, $entry, "", 'onblur="setScrollTo();we_form.submit();" onkeypress="keypressed(event)"', "text", "100%");
						$set_rename = true;
						$_type = '<div class="cutText" title="' . oldHtmlspecialchars($type) . '">' . oldHtmlspecialchars($type) . '</div>';
						$_date = date("d.m.Y H:i:s");
						break;
					default:
						$_text_to_show = '<div class="cutText" title="' . oldHtmlspecialchars($entry) . '">' .
							((strlen($entry) > 24) ? oldHtmlspecialchars($entry) : oldHtmlspecialchars($entry)) .
							'</div>';
						$_type = '<div class="cutText" title="' . oldHtmlspecialchars($type) . '">' . oldHtmlspecialchars($type) . '</div>';
						$_date = (file_exists($dir . "/" . $entry) ? date("d.m.Y H:i:s", filectime($dir . '/' . $entry)) : 'n/a');
				}

				if($show){
					echo '<tr ' . ($indb ? 'class="WEFile"' : '') . ' id="' . oldHtmlspecialchars($entry) . '"' . $ondblclick . $onclick . ' style="background-color:' . $bgcol . ';' . $_cursor . ($set_rename ? "" : "") . '"' . ($set_rename ? '' : '') . '>
	<td class="selector treeIcon">' . we_html_element::jsElement('document.write(getTreeIcon("' . ($indb? : ($islink ? 'symlink' : ($isfolder ? 'folder' : 'application/*'))) . '"));') . '</td>
	<td class="selector filename">' . $_text_to_show . '</td>
	<td class="selector filetype">' . $_type . '</td>
	<td class="selector moddate">' . $_date . '</td>
	<td class="selector filesize">' . $_size . '</td>
 </tr>';
				}
			}
			?>

		</table>
		<?php
		if(( $nf === "new_folder") || (( $nf === "rename_folder" || $nf === "rename_file") && $set_rename)){
			?>
			<input type="hidden" name="cmd" value="<?php echo $nf; ?>" />
			<?php if($nf === "rename_folder" || $nf === "rename_file"){ ?><input type="hidden" name="sid" value="<?php echo $sid; ?>" />
				<input type="hidden" name="oldtxt" value="" /><?php } ?>
			<input type="hidden" name="pat" value="<?php echo we_base_request::_(we_base_request::RAW, "pat", ""); ?>" />
		<?php } ?>
	</form>

	<?php
	if($nf === "new_folder" || (( $nf === "rename_folder" || $nf === "rename_file") && $set_rename)){
		?>
		<script><!--
		document.we_form.elements.txt.focus();
			document.we_form.elements.txt.select();
	<?php if($nf === "rename_folder" || $nf === "rename_file"){ ?>
				document.we_form.elements.oldtxt.value = document.we_form.elements.txt.value;
	<?php } ?>
			document.we_form.elements.pat.value = top.currentDir;
			//-->
		</script>
		<?php
	}
	?>
</body>
</html>