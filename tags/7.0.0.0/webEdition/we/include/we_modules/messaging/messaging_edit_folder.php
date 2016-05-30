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
$transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction');
if(!$transaction){
	exit();
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();

$messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$transaction]);
$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$transaction]);
echo we_html_tools::getHtmlTop(g_l('modules_messaging', '[folder_settings]')) .
 STYLESHEET;
?>
<script><!--
<?php
$mode = we_base_request::_(we_base_request::STRING, 'mode');
if(we_base_request::_(we_base_request::STRING, 'mcmd') === 'save_folder_settings'){
	$foldername = we_base_request::_(we_base_request::FILE, 'folder_name');
	$parentfolder = we_base_request::_(we_base_request::INT, 'parent_folder');
	$types = we_base_request::_(we_base_request::STRING, 'foldertypes');
	if($mode === 'new'){
		$res = $messaging->create_folder($foldername, $parentfolder, $types);
	} elseif($mode === 'edit'){
		$res = $messaging->modify_folder(we_base_request::_(we_base_request::INT, 'fid'), $foldername, $parentfolder);
	}
	$ID = array_shift($res);
	if($ID >= 0){

		$messaging->saveInSession($_SESSION['weS']['we_data'][$transaction]);
		?>
		top.content.cmd.location = WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=messaging&pnt=cmd&we_transaction=<?php echo $transaction ?>&mcmd=save_folder_settings&name=<?php echo $foldername; ?>&id=<?php echo $ID ?>&mode=<?php echo $mode; ?>&parent_id=<?php echo $parentfolder; ?>&type=<?php echo $types; ?>';
		top.content.we_cmd('messaging_start_view', '', '<?php echo we_base_request::_(we_base_request::TABLE, 'table', ""); ?>');
		//-->
		</script>
		</head>
		<body></body>
		</html>
		<?php
		exit;
	}
	echo we_message_reporting::getShowMessageCall($res[0], we_message_reporting::WE_MESSAGE_ERROR);
}
?>

function save() {
document.edit_folder.submit();
}
//-->
</script>

<body class="weDialogBody" style="border-top: 1px solid black;">
	<form name="edit_folder" action="<?php echo WE_MESSAGING_MODULE_DIR; ?>messaging_edit_folder.php" method="post">
		<?php
		echo we_html_tools::hidden('we_transaction', $transaction);
		echo we_html_tools::hidden('mcmd', 'save_folder_settings');
		echo we_html_tools::hidden('mode', $mode);

		if(($fid = we_base_request::_(we_base_request::INT, 'fid')) !== false){

			echo we_html_tools::hidden('fid', $fid);
		}

		switch($mode){
			case 'new':
				$heading = g_l('modules_messaging', '[new_folder]');
				$acc_html = we_html_tools::html_select('foldertypes', 1, $messaging->get_wesel_folder_types(), '', array('onchange' => "top.content.setHot();"));
				break;
			case 'edit':
				$heading = g_l('modules_messaging', '[change_folder_settings]');
				$finf = $messaging->get_folder_info($fid);
				$acc_html = we_html_tools::html_select('foldertypes', 1, $messaging->get_wesel_folder_types(), $finf['ClassName'], array('onchange' => "top.content.setHot();"));
		}

		$n = isset($finf) ? $finf['Name'] : '';
		$orgn = $n;
		$fooArray = array(
			"sent" => g_l('modules_messaging', '[folder_sent]'),
			"messages" => g_l('modules_messaging', '[folder_messages]'),
			"done" => g_l('modules_messaging', '[folder_done]'),
			"task" => g_l('modules_messaging', '[folder_todo]'),
			"rejected" => g_l('modules_messaging', '[folder_rejected]'),
			"todo" => g_l('modules_messaging', '[folder_todo]')
		);
		if(isset($fooArray[strtolower($n)])){
			$n = $fooArray[strtolower($n)];
			$specialfolder = true;
		} else {
			$specialfolder = false;
		}

		$input_tbl = '<table cellpadding="5" >
	<tr>
	  <td class="defaultfont">' . g_l('modules_messaging', '[folder_name]') . '</td>
	  <td class="defaultfont">' . ($specialfolder ? ($n . we_html_tools::hidden("folder_name", $orgn)) : we_html_tools::htmlTextInput('folder_name', 24, $n, 24, 'onchange="top.content.setHot();"')) . '</td>
	</tr>
	<tr>
	  <td class="defaultfont">' . g_l('modules_messaging', '[parent_folder]') . '</td>
	  <td>' . we_html_tools::html_select('parent_folder', 1, $messaging->get_wesel_available_folders(), isset($finf) ? $finf['ParentID'] : '', array('onchange' => "top.content.setHot();")) . '</td>
	</tr>
	<tr>
	  <td class="defaultfont">' . g_l('modules_messaging', '[type]') . '</td>
	  <td>' . $acc_html . '</td>
	</tr>
      </table>';

		$_btn_tbl = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, "javascript:save()"), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:top.content.we_cmd('messaging_start_view','', '" . we_base_request::_(we_base_request::TABLE, "table", "") . "')")
				)
		;
		echo we_html_tools::htmlDialogLayout($input_tbl, $heading, $_btn_tbl, "100%", 30, "", "none");
		?></td>
</form>
</body>
</html>