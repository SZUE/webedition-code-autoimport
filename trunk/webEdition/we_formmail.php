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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

define('WE_DEFAULT_EMAIL', 'mailserver@' . $_SERVER['SERVER_NAME']);
define('WE_DEFAULT_SUBJECT', 'webEdition mailform');

$_blocked = false;


// check to see if we need to lock or block the formmail request

if(FORMMAIL_LOG){
	// insert into log
	$GLOBALS['DB_WE']->query('INSERT INTO ' . FORMMAIL_LOG_TABLE . ' (ip, unixTime) VALUES("' . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . '", UNIX_TIMESTAMP())');
	if(FORMMAIL_EMPTYLOG > -1){
		$GLOBALS['DB_WE']->query('DELETE FROM ' . FORMMAIL_LOG_TABLE . ' WHERE unixTime<(UNIX_TIMESTAMP()-' . FORMMAIL_EMPTYLOG . ')');
	}

	if(FORMMAIL_BLOCK){
		// first delete all entries from blocktable which are older then now - blocktime
		$GLOBALS['DB_WE']->query('DELETE FROM ' . FORMMAIL_BLOCK_TABLE . ' WHERE blockedUntil!=-1 AND blockedUntil<UNIX_TIMESTAMP()');

		// check if ip is allready blocked
		if(f('SELECT id FROM ' . FORMMAIL_BLOCK_TABLE . ' WHERE ip="' . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . '"')){
			$_blocked = true;
		} else {
			// ip is not blocked, so see if we need to block it
			if(f('SELECT COUNT(1) FROM ' . FORMMAIL_LOG_TABLE . ' WHERE unixTime>(UNIX_TIMESTAMP()-' . intval(FORMMAIL_SPAN) . ') AND ip="' . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . '"') > FORMMAIL_TRIALS){
				$_blocked = true;
				// insert in block table
				$GLOBALS['DB_WE']->query('REPLACE INTO ' . FORMMAIL_BLOCK_TABLE . ' SET ' . we_database_base::arraySetter(array(
						'ip' => $_SERVER['REMOTE_ADDR'],
						'blockedUntil' => (FORMMAIL_BLOCKTIME == -1 ? -1 : sql_function('(UNIX_TIMESTAMP()+' . intval(FORMMAIL_BLOCKTIME) . ')'))
				)));
			}
		}
	}
}

$_blocked |= (FORMMAIL_VIAWEDOC && $_SERVER['SCRIPT_NAME'] == WEBEDITION_DIR . basename(__FILE__));

if($_blocked){
	print_error('Email dispatch blocked / Email Versand blockiert!');
}

function is_valid_email($email){
	return we_check_email($email);
}

function contains_bad_str($str_to_test){
	$str_to_test = trim($str_to_test);
	$bad_strings = array(
		'content-type:',
		'mime-version:',
		'Content-Transfer-Encoding:',
		'bcc:',
		'cc:',
		'to:',
	);

	foreach($bad_strings as $bad_string){
		if(preg_match('|^' . preg_quote($bad_string, '|') . '|i', $str_to_test) || preg_match('|[\n\r]' . preg_quote($bad_string, "|") . '|i', $str_to_test)){
			print_error('Email dispatch blocked / Email Versand blockiert!');
		}
	}
	if(preg_match('|multipart/mixed|i', $str_to_test)){
		print_error('Email dispatch blocked / Email Versand blockiert!');
	}
}

function replace_bad_str($str_to_test){
	$bad_strings = array(
		'#(content-type)(:)#i',
		'#(mime-version)(:)#i',
		'#(multipart/mixed)#i',
		'#(Content-Transfer-Encoding)(:)#i',
		'#(bcc)(:)#i',
		'#(cc)(:)#i',
		'#(to)(:)#i',
	);

	return preg_replace($bad_strings, '($1)$2', $str_to_test);
}

function contains_newlines($str_to_test){
	if(preg_match("/(\\n+|\\r+)/", $str_to_test) != 0){
		print_error('newline found in ' . $str_to_test . '. Suspected injection attempt - mail not being sent.');
	}
}

function print_error($errortext){
	$headline = 'Fehler / Error';
	$content = g_l('global', '[formmailerror]') . getHtmlTag('br') . '&#8226; ' . $errortext;

	echo we_html_tools::getHtmlTop() .
	we_html_element::cssLink(WEBEDITION_DIR . 'css/global.php') .
	'</head>' .
	getHtmlTag('body', array('class' => 'weEditorBody'), '', false, true) .
	we_html_tools::htmlDialogLayout(getHtmlTag('div', array('class' => 'defaultgray'), $content), $headline) .
	'</body></html>';

	exit;
}

function check_required($required){
	if($required){
		$we_requiredarray = explode(',', $required);
		for($i = 0; $i < count($we_requiredarray); $i++){
			if(!$_REQUEST[$we_requiredarray[$i]]){
				return false;
			}
		}
	}
	return true;
}

function error_page(){
	if($_REQUEST['error_page']){
		$errorpage = (get_magic_quotes_gpc() == 1) ? stripslashes($_REQUEST['error_page']) : $_REQUEST['error_page'];
		redirect($errorpage);
	} else {
		print_error(g_l('global', '[email_notallfields]'));
	}
}

function ok_page(){
	if($_REQUEST['ok_page']){
		$ok_page = (get_magic_quotes_gpc() == 1) ? stripslashes($_REQUEST['ok_page']) : $_REQUEST['ok_page'];
		redirect($ok_page);
	} else {
		echo 'Vielen Dank, Ihre Formulardaten sind bei uns angekommen! / Thank you, we received your form data!';
		exit;
	}
}

function redirect($url, $_emosScontact = ''){
	if($_emosScontact != ''){
		$url = $url . (strpos($url, '?') ? '&' : '?') . 'emosScontact=' . urlencode($_emosScontact);
	}
	header('Location: ' . getServerUrl() . $url);
	exit;
}

function check_recipient($email){
	return (f('SELECT 1 FROM ' . RECIPIENTS_TABLE . ' WHERE Email="' . $GLOBALS['DB_WE']->escape($email) . '"') ? true : false);
}

function check_captcha(){
	return ($name = weRequest('string', weRequest('string', 'captchaname', '__NOT_SET__')) ?
		we_captcha_captcha::check($name) :
		false);
}

$_req = weRequest('raw', 'required', '');

if(!check_required($_req)){
	error_page();
}

if(isset($_REQUEST['email']) && $_REQUEST['email']){
	if(!we_check_email($_REQUEST['email'])){
		if($_REQUEST['mail_error_page']){
			$foo = (get_magic_quotes_gpc() == 1) ? stripslashes($_REQUEST['mail_error_page']) : $_REQUEST['mail_error_page'];
			redirect($foo);
		} else {
			print_error(g_l('global', '[email_invalid]'));
		}
	}
}

$output = array();

$we_reserved = array('from', 'we_remove', 'captchaname', 'we_mode', 'charset', 'required', 'order', 'ok_page', 'error_page', 'captcha_error_page', 'mail_error_page', 'recipient', 'subject', 'mimetype', 'confirm_mail', 'pre_confirm', 'post_confirm', 'MAX_FILE_SIZE', session_name(), 'cookie', 'recipient_error_page', 'forcefrom');

if(isset($_REQUEST['we_remove'])){
	$removeArr = makeArrayFromCSV($_REQUEST['we_remove']);
	foreach($removeArr as $val){
		$we_reserved[] = $val;
	}
}

$we_txt = '';
$we_html = '<table>';

$_order = weRequest('raw', 'order', '');
$we_orderarray = array();
if($_order){
	$we_orderarray = explode(',', $_order);
	for($i = 0; $i < count($we_orderarray); $i++){
		if(!in_array($we_orderarray[$i], $we_reserved)){
			$output[$we_orderarray[$i]] = $_REQUEST[$we_orderarray[$i]];
		}
	}
}

if(isset($_GET)){
	foreach($_GET as $n => $v){
		if((!in_array($n, $we_reserved)) && (!in_array($n, $we_orderarray)) && (!is_array($v))){
			$output[$n] = $v;
		}
	}
}

if(isset($_POST)){
	foreach($_POST as $n => $v){
		if((!in_array($n, $we_reserved)) && (!in_array($n, $we_orderarray)) && (!is_array($v))){
			$output[$n] = $v;
		}
	}
}

foreach($output as $n => $v){
	if(is_array($v)){
		foreach($v as $n2 => $v2){
			if(!is_array($v2)){
				$foo = replace_bad_str((get_magic_quotes_gpc() == 1) ? stripslashes($v2) : $v2);
				$n = replace_bad_str($n);
				$n2 = replace_bad_str($n2);
				$we_txt .= $n . '[' . $n2 . ']: ' . $foo . "\n" . ($foo ? '' : "\n");
				$we_html .= '<tr><td align="right"><b>' . $n . '[' . $n2 . ']:</b></td><td>' . $foo . '</td></tr>';
			}
		}
	} else {
		$foo = replace_bad_str((get_magic_quotes_gpc() == 1) ? stripslashes($v) : $v);
		$n = replace_bad_str($n);
		$we_txt .= $n . ': ' . $foo . "\n" . ($foo ? '' : "\n");
		$we_html .= '<tr><td valign="top" align="right"><b>' . $n . ':</b></td><td>' . ($n == 'email' ? '<a href="mailto:' . $foo . '">' . $foo . '</a>' : nl2br($foo)) . '</td></tr>';
	}
}

$we_html .= '</table>';


$we_html_confirm = '';
$we_txt_confirm = '';

if(isset($_REQUEST['email']) && $_REQUEST['email']){
	if(isset($_REQUEST['confirm_mail']) && $_REQUEST['confirm_mail']){
		$we_html_confirm = $we_html;
		$we_txt_confirm = $we_txt;
		if(isset($_REQUEST['pre_confirm']) && $_REQUEST['pre_confirm']){
			contains_bad_str($_REQUEST['pre_confirm']);
			$we_html_confirm = $_REQUEST['pre_confirm'] . getHtmlTag('br') . $we_html_confirm;
			$we_txt_confirm = $_REQUEST['pre_confirm'] . "\n\n" . $we_txt_confirm;
		}
		if(isset($_REQUEST['post_confirm']) && $_REQUEST['post_confirm']){
			contains_bad_str($_REQUEST['post_confirm']);
			$we_html_confirm = $we_html_confirm . getHtmlTag('br') . $_REQUEST['post_confirm'];
			$we_txt_confirm = $we_txt_confirm . "\n\n" . $_REQUEST['post_confirm'];
		}
	}
}

$email = (isset($_REQUEST['email']) && $_REQUEST['email']) ?
	$_REQUEST['email'] :
	((isset($_REQUEST['from']) && $_REQUEST['from']) ?
		$_REQUEST['from'] :
		WE_DEFAULT_EMAIL);

$subject = strip_tags((isset($_REQUEST['subject']) && $_REQUEST['subject']) ?
		$_REQUEST['subject'] :
		WE_DEFAULT_SUBJECT);
$charset = (isset($_REQUEST['charset']) && $_REQUEST['charset']) ? str_replace(array("\n", "\r"), '', $_REQUEST['charset']) : $GLOBALS['WE_BACKENDCHARSET'];
$recipient = (isset($_REQUEST['recipient']) && $_REQUEST['recipient']) ? $_REQUEST['recipient'] : '';
$from = (isset($_REQUEST['from']) && $_REQUEST['from']) ? $_REQUEST['from'] : WE_DEFAULT_EMAIL;

$mimetype = (isset($_REQUEST['mimetype']) && $_REQUEST['mimetype']) ? $_REQUEST['mimetype'] : '';

$wasSent = false;

if($recipient){
	$subject = preg_replace("/(\\n+|\\r+)/", '', $subject);
	$charset = preg_replace("/(\\n+|\\r+)/", '', $charset);
	$fromMail = preg_replace("/(\\n+|\\r+)/", '', (isset($_REQUEST['forcefrom']) && $_REQUEST['forcefrom'] == 'true' ? $from : $email));
	$email = preg_replace("/(\\n+|\\r+)/", '', $email);
	$from = preg_replace("/(\\n+|\\r+)/", '', $from);

	contains_bad_str($email);
	contains_bad_str($from);
	contains_bad_str($fromMail);
	contains_bad_str($subject);
	contains_bad_str($charset);

	if(!is_valid_email($fromMail)){
		print_error(g_l('global', '[email_invalid]'));
	}

	$recipients = makeArrayFromCSV($recipient);
	$senderForename = isset($_REQUEST['forename']) && $_REQUEST['forename'] ? $_REQUEST['forename'] : '';
	$senderSurname = isset($_REQUEST['surname']) && $_REQUEST['surname'] ? $_REQUEST['surname'] : '';
	$sender = ($senderForename != '' || $senderSurname ? $senderForename . ' ' . $senderSurname . '<' . $fromMail . '>' : $fromMail);

	$phpmail = new we_util_Mailer('', $subject, $sender);
	$phpmail->setCharSet($charset);

	$recipientsList = array();

	foreach($recipients as $recipientID){

		$recipient = preg_replace("/(\\n+|\\r+)/", '', (is_numeric($recipientID) ?
				f('SELECT Email FROM ' . RECIPIENTS_TABLE . ' WHERE ID=' . intval($recipientID), 'Email', $GLOBALS['DB_WE']) :
				// backward compatible
				$recipientID)
		);

		if(!$recipient){
			print_error(g_l('global', '[email_no_recipient]'));
		}
		if(!is_valid_email($recipient)){
			print_error(g_l('global', '[email_invalid]'));
		}

		if(we_check_email($recipient) && check_recipient($recipient)){
			$recipientsList[] = $recipient;
		} else {
			print_error(g_l('global', '[email_recipient_invalid]'));
		}
	}

	if(!empty($recipientsList)){
		foreach($_FILES as $file){
			if(isset($file['tmp_name']) && $file['tmp_name']){
				$tempName = TEMP_PATH . '/' . $file['name'];
				move_uploaded_file($file['tmp_name'], $tempName);
				$phpmail->doaddAttachment($tempName);
			}
		}
		$phpmail->addAddressList($recipientsList);
		if($mimetype == 'text/html'){
			$phpmail->addHTMLPart($we_html);
		} else {
			$phpmail->addTextPart($we_txt);
		}
		$phpmail->buildMessage();
		if($phpmail->Send()){
			$wasSent = true;
		}
	}

	if((isset($_REQUEST['confirm_mail']) && $_REQUEST['confirm_mail']) && FORMMAIL_CONFIRM){
		if($wasSent){
			// validation
			if(!is_valid_email($email)){
				print_error(g_l('global', '[email_invalid]'));
			}
			$phpmail = new we_util_Mailer($email, $subject, $from);
			$phpmail->setCharSet($charset);
			if($mimetype == 'text/html'){
				$phpmail->addHTMLPart($we_html_confirm);
			} else {
				$phpmail->addTextPart($we_txt_confirm);
			}
			$phpmail->buildMessage();
			$phpmail->Send();
		}
	}
} else {
	print_error(g_l('global', '[email_no_recipient]'));
}

ok_page($subject);
