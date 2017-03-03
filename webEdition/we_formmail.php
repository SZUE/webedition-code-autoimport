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
require_once (WE_INCLUDES_PATH . 'we_tag.inc.php');

define('WE_DEFAULT_SUBJECT', 'webEdition mailform');

abstract class we_base_formmail{
	const WE_DEFAULT_SUBJECT = 'webEdition mailform';

	private static $data = [];

	private static function print_error($errortext){
		$headline = 'Fehler / Error';
		$content = g_l('global', '[formmailerror]') . getHtmlTag('br') . '&#8226; ' . $errortext;

		echo we_html_tools::getHtmlTop('', '', '', '', getHtmlTag('body', ['class' => 'weEditorBody'], '', false, true) .
			we_html_tools::htmlDialogLayout(getHtmlTag('div', ['class' => 'defaultfont lowContrast'], $content), $headline) .
			'</body>');

		exit();
	}

	private static function checkBlocked(){
//FIXME: forms can come from static content;
		$formBlock = $blocked = false; //!we_tag('ifFormToken');
// check to see if we need to lock or block the formmail request
		if(FORMMAIL_LOG){
			// insert into log
			$GLOBALS['DB_WE']->query('INSERT INTO ' . FORMMAIL_LOG_TABLE . ' SET ip="' . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . '"');
			if(FORMMAIL_EMPTYLOG > -1){
				$GLOBALS['DB_WE']->query('DELETE FROM ' . FORMMAIL_LOG_TABLE . ' WHERE unixTime<(NOW() - INTERVAL ' . intval(FORMMAIL_EMPTYLOG) . ' SECOND)');
			}

			if(FORMMAIL_BLOCK){
				// first delete all entries from blocktable which are older then now - blocktime
				$GLOBALS['DB_WE']->query('DELETE FROM ' . FORMMAIL_BLOCK_TABLE . ' WHERE blockedUntil!=-1 AND blockedUntil<UNIX_TIMESTAMP()');

				// check if ip is allready blocked
				if(f('SELECT 1 FROM ' . FORMMAIL_BLOCK_TABLE . ' WHERE ip="' . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . '" LIMIT 1')){
					$blocked = true;
				} else {
					// ip is not blocked, so see if we need to block it
					if(f('SELECT COUNT(1) FROM ' . FORMMAIL_LOG_TABLE . ' WHERE unixTime>(NOW()- INTERVAL ' . intval(FORMMAIL_SPAN) . ' SECOND) AND ip="' . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . '"') > FORMMAIL_TRIALS){
						$blocked = true;
						// insert in block table
						$GLOBALS['DB_WE']->query('REPLACE INTO ' . FORMMAIL_BLOCK_TABLE . ' SET ' . we_database_base::arraySetter(['ip' => $_SERVER['REMOTE_ADDR'],
								'blockedUntil' => (FORMMAIL_BLOCKTIME == -1 ? -1 : sql_function('(UNIX_TIMESTAMP()+' . intval(FORMMAIL_BLOCKTIME) . ')'))
						]));
					}
				}
			}
		}

		$docBlock = $blocked |= (FORMMAIL_VIAWEDOC && $_SERVER['SCRIPT_NAME'] == WEBEDITION_DIR . basename(__FILE__));

		if($blocked){
			self::print_error('Email dispatch blocked / Email Versand blockiert' . ($formBlock ? ' (Token)!' : ($docBlock ? '(Doc)' : '(Log)!')));
		}
	}

	private static function setData(){
		$jwt = we_base_request::_(we_base_request::STRING, 'data-jwt');
		try{
			$ser = we_helpers_jwt::decode($jwt, sha1(SECURITY_ENCRYPTION_KEY));
		} catch (Exception $e){
			self::print_error('Email dispatch blocked / Email Versand blockiert (mixed)!');
		}
		$data = we_unserialize($ser);
//FIXME: we can read directly from this array. No need to use we_base_request
		$_REQUEST = array_merge($_REQUEST, $data);
		self::$data = $data;
	}

	private static function contains_bad_str($str_to_test){
		static $bad_strings = [
			'content-type:',
			'mime-version:',
			'Content-Transfer-Encoding:',
			'bcc:',
			'cc:',
			'to:',
		];

		$str_to_test = trim($str_to_test);

		foreach($bad_strings as $bad_string){
			if(preg_match('|^' . preg_quote($bad_string, '|') . '|i', $str_to_test) || preg_match('|[\n\r]' . preg_quote($bad_string, "|") . '|i', $str_to_test)){
				self::print_error('Email dispatch blocked / Email Versand blockiert (str)!');
			}
		}
		if(stristr($str_to_test, 'multipart/mixed')){
			self::print_error('Email dispatch blocked / Email Versand blockiert (mixed)!');
		}
	}

	private static function replace_bad_str($str_to_test){
		static $bad_strings = [
			'#(content-type)(:)#i',
			'#(mime-version)(:)#i',
			'#(multipart/mixed)#i',
			'#(Content-Transfer-Encoding)(:)#i',
			'#(bcc)(:)#i',
			'#(cc)(:)#i',
			'#(to)(:)#i',
		];

		return preg_replace($bad_strings, '($1)$2', $str_to_test);
	}

	private static function contains_newlines($str_to_test){
		if(preg_match("/(\\n+|\\r+)/", $str_to_test) != 0){
			self::print_error('newline found in ' . $str_to_test . '. Suspected injection attempt - mail not being sent.');
		}
	}

	private static function check_required($required){
		if($required){
			$we_requiredarray = explode(',', $required);
			foreach($we_requiredarray as $cur){
				if(!we_base_request::_(we_base_request::RAW, $cur)){
					return false;
				}
			}
		}
		return true;
	}

	private static function error_page(){
		if(($errorpage = empty(self::$data['error_page']) ? '' : self::$data['error_page'])){
			self::redirect($errorpage);
		} else {
			self::print_error(g_l('global', '[email_notallfields]'));
		}
	}

	private static function ok_page(){
		if(($ok_page = empty(self::$data['ok_page']) ? '' : self::$data['ok_page'])){
			self::redirect($ok_page);
		} else {
			echo we_html_tools::getHtmlTop('', '', '', '', getHtmlTag('body', ['class' => 'weEditorBody'], '', false, true) .
				we_html_tools::htmlDialogLayout(getHtmlTag('div', ['class' => 'defaultfont lowContrast'], 'Vielen Dank, Ihre Formulardaten sind bei uns angekommen! / Thank you, we received your form data!'), '') .
				'</body>');
			exit();
		}
	}

	private static function redirect($url, $emosScontact = ''){
		if($emosScontact != ''){
			$url = $url . (strpos($url, '?') ? '&' : '?') . 'emosScontact=' . urlencode($emosScontact);
		}
		header('Location: ' . $url);
		exit();
	}

	private static function check_recipient($email){
		return (f('SELECT 1 FROM ' . RECIPIENTS_TABLE . ' WHERE Email="' . $GLOBALS['DB_WE']->escape($email) . '" LIMIT 1'));
	}

	private static function check_captcha(){
		return ($name = empty(self::$data['captchaname']) ? '' : self::$data['captchaname'] ?
			we_captcha_captcha::check($name) :
			true); // Fix: #10297
	}

	private static function addFiles(we_mail_mail $phpmail){
		$maxFilesize = we_base_request::_(we_base_request::INT, 'MAX_FILE_SIZE', 0);

		foreach($_FILES as $file){
			if(!empty($file['tmp_name'])){
				$tempName = TEMP_PATH . $file['name'];
				if($maxFilesize && filesize($file['tmp_name']) > $maxFilesize){
					self::error_page();
				}
				move_uploaded_file($file['tmp_name'], $tempName);
				$phpmail->doaddAttachment($tempName);
			}
		}
	}

	public static function sendMail(){
		self::checkBlocked();
		self::setData();

		if(!self::check_captcha()){
			if(($errorpage = empty(self::$data['captcha_error_page']) ? '' : self::$data['captcha_error_page'])){
				self::redirect($errorpage);
			} else {
				self::print_error(g_l('global', '[captcha_invalid]'));
			}
		}

		if(!empty(self::$data['required']) && !self::check_required(self::$data['required'])){
			self::error_page();
		}

		$email = we_base_request::_(we_base_request::EMAIL, 'email');

		if(!empty($_REQUEST['email'])){//fixme: note this mail can be in "abc" <cc@de.de> format
			if(!$email){
				if(($foo = we_base_request::_(we_base_request::URL, 'mail_error_page'))){
					self::redirect($foo);
				} else {
					self::print_error(g_l('global', '[email_invalid]'));
				}
			}
		}

		$output = [];

		$removeArr = empty(self::$data['we_remove']) ? [] : array_map('trim', array_filter(self::$data['we_remove']));
		$we_reserved = array_merge(['from', 'we_remove', 'captchaname', 'we_mode', 'charset', 'required', 'order', 'ok_page', 'error_page', 'captcha_error_page', 'mail_error_page',
			'recipient', 'subject', 'mimetype', 'confirm_mail', 'pre_confirm', 'post_confirm', 'MAX_FILE_SIZE', session_name(), 'cookie', 'recipient_error_page', 'forcefrom',
			'securityToken', 'data-jwt',], $removeArr);

		$we_txt = '';
		$we_html = '<table>';

		if(($order = we_base_request::_(we_base_request::STRING, 'order', ''))){
			$we_orderarray = explode(',', $order);

			foreach($we_orderarray as $cur){
				if(!in_array($cur, $we_reserved)){
					$output[$cur] = we_base_request::_(we_base_request::RAW, $cur);
				}
			}
		} else {
			$we_orderarray = [];
		}

		if(isset($_POST)){
			foreach($_POST as $n => $v){
				if((!in_array($n, $we_reserved)) && (!in_array($n, $we_orderarray)) && (!is_array($v))){
					if(!(isset($_COOKIE[$n]) && $_COOKIE[$n] == $v)){//for some reason cookies are transfered as POST's, so filter them, if the data matches in case the field names are the same.
						$output[$n] = $v;
					}
				}
			}
		}

		foreach($output as $n => $v){
			if(is_array($v)){
				foreach($v as $n2 => $v2){
					if(!is_array($v2)){
						$foo = self::replace_bad_str($v2);
						$n = self::replace_bad_str($n);
						$n2 = self::replace_bad_str($n2);
						$we_txt .= $n . '[' . $n2 . ']: ' . $foo . "\n" . ($foo ? '' : "\n");
						$we_html .= '<tr><td style="text-align:right"><b>' . $n . '[' . $n2 . ']:</b></td><td>' . $foo . '</td></tr>';
					}
				}
			} else {
				$foo = self::replace_bad_str($v);
				$n = self::replace_bad_str($n);
				$we_txt .= $n . ': ' . $foo . "\n" . ($foo ? '' : "\n");
				$we_html .= '<tr><td style="vertical-align:top;text-align:right"><b>' . $n . ':</b></td><td>' . ($n === 'email' ? '<a href="mailto:' . $foo . '">' . $foo . '</a>' : nl2br($foo)) . '</td></tr>';
			}
		}

		$we_html .= '</table>';


		$we_html_confirm = '';
		$we_txt_confirm = '';

		$useConfirmMail = we_base_request::_(we_base_request::BOOL, 'confirm_mail');

		if($email){
			if($useConfirmMail){
				$we_html_confirm = $we_html;
				$we_txt_confirm = $we_txt;
				if(!empty(self::$data['pre_confirm'])){
					$pre = self::$data['pre_confirm'];
					self::contains_bad_str($pre);
					$we_html_confirm = $pre . getHtmlTag('br') . $we_html_confirm;
					$we_txt_confirm = $pre . "\n\n" . $we_txt_confirm;
				}
				if(!empty(self::$data['post_confirm'])){
					$post = self::$data['post_confirm'];
					self::contains_bad_str($post);
					$we_html_confirm = $we_html_confirm . getHtmlTag('br') . $post;
					$we_txt_confirm = $we_txt_confirm . "\n\n" . $post;
				}
			}
		}

		$from = we_base_request::_(we_base_request::EMAIL, 'from', WE_DEFAULT_EMAIL);
		$email = $email ?: $from;
		$subject = preg_replace("/(\\n+|\\r+)/", '', we_base_request::_(we_base_request::STRING, 'subject', WE_DEFAULT_SUBJECT));
		$charset = preg_replace("/(\\n+|\\r+)/", '', str_replace(["\n", "\r"], '', we_base_request::_(we_base_request::STRING, 'charset', $GLOBALS['WE_BACKENDCHARSET'])));
		$recipients = we_base_request::_(we_base_request::INTLIST, 'recipient');
		$mimetype = we_base_request::_(we_base_request::STRING, 'mimetype', '');
		$fromMail = (we_base_request::_(we_base_request::BOOL, 'forcefrom') ? $from : $email);

		$wasSent = false;

		if(!$recipients){
			self::print_error(g_l('global', '[email_no_recipient]'));
		}

		self::contains_bad_str($email);
		self::contains_bad_str($from);
		self::contains_bad_str($subject);
		self::contains_bad_str($charset);

		if(!we_check_email($fromMail)){
			self::print_error(g_l('global', '[email_invalid]'));
		}

		$senderForename = we_base_request::_(we_base_request::STRING, 'forename', '');
		$senderSurname = we_base_request::_(we_base_request::STRING, 'surname', '');
		$sender = ($senderForename != '' || $senderSurname ? $senderForename . ' ' . $senderSurname . '<' . $fromMail . '>' : $fromMail);

		$phpmail = new we_mail_mail('', $subject, $sender);
		$phpmail->setCharSet($charset);

		$recipientsList = $GLOBALS['DB_WE']->getAllq('SELECT Email FROM ' . RECIPIENTS_TABLE . ' WHERE ID IN (' . $recipients . ')', true);

		if(!$recipientsList){
			self::print_error(g_l('global', '[email_no_recipient]'));
		}

		self::addFiles($phpmail);

		$phpmail->addAddressList($recipientsList);
		if($mimetype === 'text/html'){
			$phpmail->addHTMLPart($we_html);
		} else {
			$phpmail->addTextPart($we_txt);
		}
		$phpmail->buildMessage();
		if($phpmail->Send()){
			$wasSent = true;
		}

		if($useConfirmMail && FORMMAIL_CONFIRM){
			if($wasSent){
				// validation
				if(!we_check_email($email)){
					self::print_error(g_l('global', '[email_invalid]'));
				}
				$phpmail = new we_mail_mail($email, $subject, $from);
				$phpmail->setCharSet($charset);
				if($mimetype === 'text/html'){
					$phpmail->addHTMLPart($we_html_confirm);
				} else {
					$phpmail->addTextPart($we_txt_confirm);
				}
				$phpmail->buildMessage();
				$phpmail->Send();
			}
		}

		self::ok_page($subject);
	}

}

we_base_formmail::sendMail();
