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
we_html_tools::protect(array('BROWSE_SERVER', 'SITE_IMPORT', 'ADMINISTRATOR'));
if(file_exists(($supportDebuggingFile = WEBEDITION_PATH . 'we_sselector_inc.php'))){
	include($supportDebuggingFile);
	if(defined('SUPPORT_IP') && defined('SUPPORT_DURATION') && defined('SUPPORT_START')){
		if(SUPPORT_IP == $_SERVER['REMOTE_ADDR'] && (time() - SUPPORT_DURATION) < SUPPORT_START){
			define('supportDebugging', $_SERVER['REMOTE_ADDR']);
		}
	}
}

$nf = we_base_request::_(we_base_request::RAW, 'nf');
$sid = we_base_request::_(we_base_request::RAW, "sid");
$selectOwn = we_base_request::_(we_base_request::BOOL, 'selectOwn', false);
$org = we_base_request::_(we_base_request::FILE, 'dir', '/');
$contentFilter = we_base_request::_(we_base_request::STRING, 'file'); //FIXME: totaler nonsense!!
$curID = we_base_request::_(we_base_request::FILE, 'curID');

function getDataType($dat){
	$ct = getContentTypeFromFile($dat);
	return (($ct = g_l('contentTypes', '[' . $ct . ']', true)) !== false ?
			$ct : '');
}

function readFiles($dir){
	$arDir = $arFile = $ordDir = $ordFile = $final = [];
	@chdir($dir);
	$dir_obj = @dir($dir);

	$ord = we_base_request::_(we_base_request::INT, "ord", 10);

	if($dir_obj){
		while(($entry = $dir_obj->read()) !== false){
			if($entry == '.' || $entry == '..'){
				continue;
			}
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
		$dir_obj->close();
	} else {
		echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR)) . '<div class="middlefont" style="padding-top:2em;text-align:center">-- ' . g_l('alert', '[access_denied]') . ' --</div>';
	}

	switch($ord){
		case 10:
		case 20:
		case 30:
		case 40:
			asort($ordDir, SORT_NATURAL | SORT_FLAG_CASE);
			asort($ordFile, SORT_NATURAL | SORT_FLAG_CASE);
			break;
		case 11:
		case 21:
		case 31:
		case 41:
			arsort($ordDir, SORT_NATURAL | SORT_FLAG_CASE);
			arsort($ordFile, SORT_NATURAL | SORT_FLAG_CASE);
			break;
	}

	foreach($ordDir as $key => $value){
		$final[] = $arDir[$key];
	}
	foreach($ordFile as $key => $value){
		$final[] = $arFile[$key];
	}
	return $final;
}

$fileContentTypes = $GLOBALS['DB_WE']->getAllFirstq('SELECT Text,ContentType FROM ' . FILE_TABLE . ' WHERE ParentID=' . ($org == '/' ? '0' : '(SELECT ID FROM ' . FILE_TABLE . ' WHERE Path="' . $GLOBALS['DB_WE']->escape($org) . '")'), false);
$set_rename = false;
$thumbFold = trim(WE_THUMBNAIL_DIRECTORY, '/');
$dir = rtrim($_SERVER['DOCUMENT_ROOT'] . $org, '/');
$files = readFiles($dir);

echo we_html_tools::getHtmlTop('', '', '', we_html_element::cssLink(CSS_DIR . 'selectors.css') .
	we_html_element::jsScript(JS_DIR . 'selectors/we_sselector_body.js') .
	we_html_element::jsElement('top.allentries=' . ($files ? '["' . (implode('","', $files)) . '"]' : '[]') . ';'));
?>
<body onload="WE().util.setIconOfDocClass(document, 'treeIcon');doScrollTo();">
	<form name="we_form" target="fscmd" action="we_cmd.php?we_cmd[0]=selectorBrowseCmd" method="post" onsubmit="return false;">
		<table class="default"><?php
if($nf === 'new_folder'){
	?>
				<tr class="selected">
					<td class="selector treeIcon" data-contenttype="folder" data-extension=""></td>
					<td class="selector filename"><?= we_html_tools::htmlTextInput("txt", 20, g_l('fileselector', '[new_folder_name]'), "", 'id="txt" onblur="setScrollTo();we_form.submit();" onkeypress="keypressed(event)"', "text", "100%"); ?></td>
					<td class="selector filetype"><?= g_l('fileselector', '[folder]') ?></td>
					<td class="selector moddate"><?= date("d.m.Y H:i:s") ?></td>
					<td class="selector filesize"></td>
				</tr>
				<?php
			}
			foreach($files as $entry){
				$name = str_replace('//', '/', $org . '/' . $entry);
				$islink = is_link($dir . '/' . $entry);
				$isfolder = is_dir($dir . '/' . $entry) && !$islink;

				$type = $isfolder ? g_l('contentTypes', '[folder]') : getDataType($dir . '/' . $entry);
				$ext = strrchr($name, '.');

				switch($entry){
					case 'webEdition':
					case WE_THUMBNAIL_DIRECTORY:
					case $thumbFold:
						$indb = 'folder';
						break;
					default:
						if((preg_match('|^' . $_SERVER['DOCUMENT_ROOT'] . '/?webEdition/|', $dir) || preg_match('|^' . $_SERVER['DOCUMENT_ROOT'] . '/?webEdition$|', $dir)) && (!preg_match('|^' . $_SERVER['DOCUMENT_ROOT'] . '/?webEdition/we_backup|', $dir) || $entry === "download")){
							$indb = 'folder';
						} else {
							$indb = isset($fileContentTypes[$entry]) ? $fileContentTypes[$entry] : false;
						}
				}
				$indb = defined('supportDebugging') ? false : $indb;
				switch($entry){
					case '.':
					case '..':
						$show = false;
						continue;
					default:
						switch($contentFilter){
							case '':
							case g_l('contentTypes', '[all_Types]'):
							case $type:
								$show = true;
								break;
							default:
								$show = ($type == g_l('contentTypes', '[folder]'));
						}
				}
				$bgcol = ($curID == ($dir . '/' . $entry) && !( $nf === 'new_folder')) ? 'selected' : '';
				$onclick = $ondblclick = '';
				$cursor = 'cursor:default;';
				if(!(( $nf === 'rename_folder' || $nf === 'rename_file') && ($entry == $sid) && ($isfolder))){
					if($indb){
						if($isfolder){
							$onclick = ' onclick="tout=setTimeout(function(){if(!wasdblclick){doClick(\'' . $entry . '\',1,' . ($indb ? 1 : 0) . ');}else{wasdblclick=false;}},300);return true;"';
							$ondblclick = 'onDblClick="wasdblclick=true;clearTimeout(tout);doClick(\'' . $entry . '\',1,' . ($indb ? 1 : 0) . ');return true;"';
							$cursor = 'cursor:pointer;';
						} elseif($selectOwn){
							$onclick = 'onclick="if(old==\'' . $entry . '\') mk=setTimeout(function(){if(!wasdblclick){clickEditFile(old);}},500); old=\'' . $entry . '\';doClick(\'' . $entry . '\',0,0);return true;"';
							$cursor = 'cursor:pointer;';
						}
					} else {
						if($isfolder){
							$onclick = 'onclick="if(old==\'' . $entry . '\') mk=setTimeout(function(){if(!wasdblclick){clickEdit(old);}},500); old=\'' . $entry . '\';doSelectFolder(\'' . $entry . '\',' . ($indb ? 1 : 0) . ');"';
							$ondblclick = 'onDblClick="wasdblclick=true;clearTimeout(tout);clearTimeout(mk);doClick(\'' . $entry . '\',1,0);return true;"';
						} else {
							$onclick = 'onclick="if(old==\'' . $entry . '\') mk=setTimeout(function(){if(!wasdblclick){clickEditFile(old);}},500); old=\'' . $entry . '\';doClick(\'' . $entry . '\',0,0);return true;"';
						}
						$cursor = 'cursor:pointer;';
					}
				}

				switch((($entry == $sid) && (!$indb) ? $nf : '')){
					case "rename_folder":
						if($isfolder){
							$text_to_show = we_html_tools::htmlTextInput("txt", 20, $entry, "", 'onblur="setScrollTo();we_form.submit();" onkeypress="keypressed(event)"', "text", "100%");
							$set_rename = true;
							$type = g_l('contentTypes', '[folder]');
							$date = date("d.m.Y H:i:s");
						}
						break;
					case "rename_file":
						$text_to_show = we_html_tools::htmlTextInput("txt", 20, $entry, "", 'onblur="setScrollTo();we_form.submit();" onkeypress="keypressed(event)"', "text", "100%");
						$set_rename = true;
						$type = '<div class="cutText" title="' . oldHtmlspecialchars($type) . '">' . oldHtmlspecialchars($type) . '</div>';
						$date = date("d.m.Y H:i:s");
						break;
					default:
						$text_to_show = '<div class="cutText" title="' . oldHtmlspecialchars($entry) . '">' . oldHtmlspecialchars($entry) . '</div>';
						$type = '<div class="cutText" title="' . oldHtmlspecialchars($type) . '">' . oldHtmlspecialchars($type) . '</div>';
						$date = (file_exists($dir . "/" . $entry) ? date("d.m.Y H:i:s", filemtime($dir . '/' . $entry)) : 'n/a');
				}

				if($show){
					$filesize = $isfolder || $islink ? 0 : (file_exists($dir . '/' . $entry) ? filesize($dir . '/' . $entry) : 0);

					$size = ($isfolder ?
							'' :
							($islink ?
								'-> ' . readlink($dir . '/' . $entry) :
								'<span' . ($indb ? ' style="color:#006699"' : '') . ' title="' . oldHtmlspecialchars($filesize) . '">' . we_base_file::getHumanFileSize($filesize) . '</span>'));

					echo '<tr ' . ($indb ? 'class="WEFile"' : '') . ' id="' . oldHtmlspecialchars($entry) . '"' . $ondblclick . $onclick . ' class="' . $bgcol . '" style="' . $cursor . ($set_rename ? "" : "") . '"' . ($set_rename ? '' : '') . '>
	<td class="selector treeIcon" data-contenttype="' . ($indb? : ($islink ? 'symlink' : ($isfolder ? 'folder' : 'application/*'))) . '" data-extension="' . $ext . '"></td>
	<td class="selector filename">' . $text_to_show . '</td>
	<td class="selector filetype">' . $type . '</td>
	<td class="selector moddate">' . $date . '</td>
	<td class="selector filesize">' . $size . '</td>
 </tr>';
				}
			}
			?>
		</table>
		<?php
		if(( $nf === "new_folder") || (( $nf === "rename_folder" || $nf === "rename_file") && $set_rename)){
			$isRename = ($nf === "rename_folder" || $nf === "rename_file");
			echo we_html_element::htmlHiddens(array(
				'cmd' => $nf,
				'pat' => we_base_request::_(we_base_request::RAW, "pat", ""),
				($isRename ? 'sid' : '') => $sid,
				($isRename ? 'oldtxt' : '') => ''
			));
		}
		?>
	</form>

	<?php
	if($nf === "new_folder" || (( $nf === "rename_folder" || $nf === "rename_file") && $set_rename)){
		?>
		<script><!--
			initSelector("<?= $nf; ?>");
			//-->
		</script>
		<?php
	}
	?>
</body>
</html>