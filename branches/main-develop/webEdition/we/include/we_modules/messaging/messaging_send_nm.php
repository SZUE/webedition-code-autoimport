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

include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_html_tools.inc.php");
include_once(WE_MESSAGING_MODULE_DIR . "we_messaging.inc.php");
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/html/we_button.inc.php");
protect();
$_REQUEST['we_transaction'] = (preg_match("/^([a-f0-9]){32}$/i", $_REQUEST['we_transaction']) ? $_REQUEST['we_transaction'] : 0);
if (is_array($_SESSION["we_data"][$_REQUEST['we_transaction']])) {

	$messaging = new we_messaging($_SESSION["user"]["ID"]);
	$messaging = new we_messaging($_SESSION["we_data"][$_REQUEST['we_transaction']]);
	$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
	$messaging->init($_SESSION["we_data"][$_REQUEST['we_transaction']]);

	$arr = array('rcpts_string' => $_REQUEST['rcpts_string'], 'subject' => $_REQUEST['mn_subject'], 'body' => $_REQUEST['mn_body']);

	$res = $messaging->send($arr);
} else {
	include_once(WE_MESSAGING_MODULE_DIR."messaging_interfaces.inc.php");
	$errs = array();
	$rcpts = array(urldecode($_REQUEST['rcpts_string'])); /* user names */
	$res = msg_new_message($rcpts,$_REQUEST['mn_subject'],$_REQUEST['mn_body'],$errs);
}
?>
<html>
	<head>
		<title><?php echo g_l('modules_messaging','[message_send]')?></title>
		<?php
			print STYLESHEET;

			$we_button = new we_button();
		?>
		<?php

		    if (!empty($res['ok'])) {
                if (substr($_REQUEST["mode"], 0, 2) != 'u_') {
                    echo '
                        <script language="javascript">
                            if (opener && opener.top && opener.top.content) {
                                opener.top.content.update_messaging();
                                opener.top.content.update_msg_quick_view();
                            }
                        </script>
                    ';
                } else {
                    echo '
                        <script language="javascript">
                            if (opener && opener.top && opener.top.content) {
                                  opener.top.content.update_msg_quick_view();
                            }
                        </script>
                    ';
                }
		    }
		?>
	</head>

	<body class="weDialogBody">
        <?php
        $tbl = '
            <table align="center" cellpadding="7" cellspacing="3" width="100%">
        ';
        if ($res['ok']) {
            $tbl .= '
                <tr>
                    <td class="defaultfont" valign="top">' . g_l('messaging','[s_sent_to]') . ':</td>
                    <td class="defaultfont">
                        <ul>
            ';
            
            foreach ($res['ok'] as $ok) {
                $tbl .= '<li>' . htmlspecialchars($ok) . '</li>';
            }
                        
            $tbl .= '
                        </ul>
                    </td>
                </tr>
            ';
        }
                
        if ($res['failed']) {
            $tbl .= '
                <tr>
                    <td class="defaultfont" valign="top">' . g_l('messaging','[n_sent_to]') . ':</td>
                    <td class="defaultfont">
                        <ul>    
            ';

            foreach ($res['failed'] as $failed) {
                $tbl .= '<li>' . htmlspecialchars($failed) . '</li>';
            }

            $tbl .= '
                        </ul>
                    </td>
                </tr>
            ';
        }

        if ($res['err']) {
            $tbl .= '
                <tr>
                    <td class="defaultfont" valign="top">' . g_l('messaging','[occured_errs]') . ':</td>
                    <td class="defaultfont">
                        <ul>
            ';

            foreach ($res['err'] as $error) {
                $tbl .= '<li>' . $error . '</li>';
            }

            $tbl .= '
                        </ul>
                    </td>
                </tr>    
            ';
        }

		$tbl .= '</table>';
        echo htmlDialogLayout($tbl, g_l('messaging','[message_send]') . '...', $we_button->create_button("ok", "javascript:window.close()"), "100%", "20", "", "hidden");
		?>
	</body>

</html>