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

we_html_tools::protect();
$transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction');
if(!$transaction){
	exit();
}

$messaging = new we_messaging_messaging($_SESSION['weS']['we_data'][$transaction]);
$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
$messaging->init($_SESSION['weS']['we_data'][$transaction]);
echo we_html_tools::getHtmlTop(g_l('modules_messaging', '[search_advanced]')) .
 STYLESHEET;
if(we_base_request::_(we_base_request::BOOL, 'save')){
	$messaging->set_search_settings(we_base_request::_(we_base_request::STRING, 'search_fields'), we_base_request::_(we_base_request::INT, 'search_folders', array()));
	$messaging->saveInSession($_SESSION['weS']['we_data'][$transaction]);
	?>
	</head>
	<body onload="self.close();">
	</body>
	</html>
	<?php
	exit();
}
?>
<script><!--
	function save_settings() {
		document.search_adv.submit();
	}
	//-->
</script>
</head>
<body class="weDialogBody">
	<form action="<?php echo WE_MESSAGING_MODULE_DIR; ?>messaging_search_advanced.php" name="search_adv" >
		<?php
		echo we_html_element::htmlHiddens(array(
			"we_transaction" => we_base_request::_(we_base_request::TRANSACTION, 'we_transaction'),
			"save" => 1));

		$table = '<table cellpadding="10">
<tr>
    <td style="vertical-align:top" class="defaultfont lowContrast">' . g_l('modules_messaging', '[to_search_fields]') . '</td>
    <td><select name="search_fields[]" size="3" multiple>
    ' . $messaging->print_select_search_fields() . '
        </select></td>
</tr>
<tr>
    <td style="vertical-align:top" class="defaultfont lowContrast">' . g_l('modules_messaging', '[to_search_folders]') . '</td>
    <td><select name="search_folders[]" size="5" multiple>
    ' . $messaging->print_select_search_folders() . '
        </select>
    </td>
</table>';

		$buttontable = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:save_settings();"), null, we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close()"));

		echo we_html_tools::htmlDialogLayout($table, "", $buttontable, "90%");
		?>

	</form>
</body>
</html>