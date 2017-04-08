<?php
/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package none
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * PHP email transport class
 *
 */
//FIXME: this class is used in WE & uses Zend!!!
class we_mail_mail extends we_mail_znd{

	/**
	 * Type of Message, either text/html or text/plain
	 *
	 * @var String
	 */
	protected $ContentType = we_mail_mime::TYPE_HTML;

	/**
	 * Flag for embed images
	 *
	 * @var Bool
	 */
	protected $isEmbedImages = false;

	/**
	 *
	 * @var String
	 */
	protected $basedir = '';

	/**
	 * Flag for using <base href
	 *
	 * @var Bool
	 */
	protected $isUseBaseHref = true;

	/**
	 * MessageBody (html)
	 *
	 * @var String
	 */
	protected $Body = '';

	/**
	 * MessageBody (text)
	 *
	 * @var String
	 */
	protected $AltBody = '';

	/**
	 * Flag if message is finally constructed and ready to send
	 *
	 * @var Bool
	 */
	protected $messageBuilt = false;

	/**
	 *
	 * @var array
	 */
	protected $embedImages = ['gif', 'jpg', 'jpeg', 'jpe', 'bmp', 'png', 'tif', 'tiff', 'swf', 'GIF', 'JPG', 'JPEG', 'JPE', 'BMP', 'PNG', 'TIF', 'TIFF', 'SWF'];

	/**
	 *
	 * @var array
	 */
	protected $inlineAtt = [];

	/**
	 * Internal storage for the subject to survive change of charset
	 *
	 * @var string
	 */
	protected $internal_subject = '';

	/**
	 *
	 * @param String || Array $to use Array for a list of users
	 * @param String $subject
	 * @param String $sender
	 * @param String $reply
	 * @param Bool $isEmbedImages
	 */
	public function __construct($to = '', $subject = '', $sender = '', $reply = '', $isEmbedImages = 0){
		$this->setCharSet($GLOBALS['WE_BACKENDCHARSET']);

		switch(WE_MAILER){
			case 'smtp' :
				if(SMTP_SERVER != ''){
					$smtp_config = [];
					if(SMTP_PORT != ''){
						$smtp_config['port'] = SMTP_PORT;
					}
					if(SMTP_AUTH){
						$smtp_config['auth'] = 'login'; // das ist die vom phpMailer unterst�tzte Version - Zend kann auch plain und crammd5
						if(SMTP_USERNAME != ''){
							$smtp_config['username'] = SMTP_USERNAME;
						}
						if(SMTP_PASSWORD != ''){
							$smtp_config['password'] = SMTP_PASSWORD;
						}
					}
					if((SMTP_ENCRYPTION != 0 ) || SMTP_ENCRYPTION != ''){
						$smtp_config['ssl'] = SMTP_ENCRYPTION;
					}
					$tr = new we_mail_TransportSmtp(SMTP_SERVER, $smtp_config);
					$this->setDefaultTransport($tr);
				}
				break;

			default:
			case 'php':
				//this should set return-path
				$suhosin = extension_loaded('suhosin');
				$_sender = $sender ? $this->parseEmailUser($sender) : '';
				$tr = ($_sender && !empty($_sender['email']) && !$suhosin ?
					new we_mail_TransportSendmail('-f' . $_sender['email']) :
					new we_mail_TransportSendmail());

				we_mail_znd::setDefaultTransport($tr);
				break;
		}


		if(is_array($to) && !empty($to)){
			foreach($to as $_to){
				$_to = $this->parseEmailUser($_to);
				$this->addTo($_to['email'], $_to['name']);
			}
		} else if($to){
			$_to = $this->parseEmailUser($to);
			$this->addTo($_to['email'], $_to['name']);
		}

		if(is_array($reply) && !empty($reply)){
			foreach($reply as $_reply){
				$_reply = $this->parseEmailUser($_reply);
				$this->setReplyTo($_reply['email'], $_reply['name']);
			}
		} else if($reply){
			$_reply = $this->parseEmailUser($reply);
			$this->setReplyTo($_reply['email'], $_reply['name']);
		}
		if($sender){
			$_sender = $this->parseEmailUser($sender);
			$this->setFrom($_sender['email'], $_sender['name']);
		} else {
			$this->setFrom(WE_DEFAULT_EMAIL, WE_DEFAULT_EMAIL);
		}

		$this->setSubject($subject);
		$this->setIsEmbedImages($isEmbedImages);
		$this->setIsUseBaseHref(true);
	}

	public function setCC($toCC){
		if(is_array($toCC) && !empty($toCC)){
			foreach($toCC as $_toCC){
				$_toCC = $this->parseEmailUser($_toCC);
				$this->addCc($_toCC['email'], $_toCC['name']);
			}
		} else if($toCC){
			$_toCC = $this->parseEmailUser($toCC);
			$this->addCc($_toCC['email'], $_toCC['name']);
		}
	}

	public function setBCC($toBCC){
		if(!$toBCC){
			return;
		}
		if(is_array($toBCC)){
			foreach($toBCC as $_toBCC){
				$_toBCC = $this->parseEmailUser($_toBCC);
				$this->addBcc($_toBCC['email'], $_toBCC['name']);
			}
		} else {
			$_toBCC = $this->parseEmailUser($toBCC);
			$this->addBcc($_toBCC['email'], $_toBCC['name']);
		}
	}

	public function parseEmailUser($user){
		if(is_array($user) && isset($user['email'])){
			$email = trim($user['email']);
			$name = (isset($user['name']) ? $user['name'] : '');
		} else {
			$_user = [];
			if(preg_match('/<(.)*>/', $user, $_user)){
				$email = substr($_user[0], 1, strpos($_user[0], ">") - 1);
				$name = substr($user, 0, strpos($user, "<"));
			} else {
				$email = $user;
				$name = '';
			}
		}
		return ['email' => trim($email), "name" => trim($name)];
	}

	public function formatEMail($email, $name){
		return $this->_formatAddress($email, $name);
	}

	public function addHTMLPart($val){
		$this->ContentType = we_mail_mime::TYPE_HTML;
		$this->Body = $val;
	}

	public function addTextPart($val){
		$this->AltBody = str_replace(["\r\n", "\r", "\n"], ["\n", "\n", "\r\n"], $val);
	}

	public function addAddressList($list){
		if(is_array($list) && !empty($list)){
			foreach($list as $_to){
				$_to = $this->parseEmailUser($_to);
				$this->addTo($_to['email'], $_to['name']);
			}
		}
	}

	public function buildMessage(){
		if($this->Body){
			if($this->isEmbedImages){
				$binParts = $images = [];
				preg_match_all('/(src|background)="(.*)"/Ui', $this->Body, $images);
				$images[2] = array_unique($images[2]); //entfernt doppelte Bildereinfügungen #3725

				foreach($images[2] as $i => $url){
					$isBinaryData = preg_match('/image\/(.*);base64,(.*)"/Ui', $url . '"', $binParts);

					if($isBinaryData){
						if(!in_array($binParts[1], $this->embedImages)){
							continue;
						}
						$cid = 'cid:' . $this->doaddAttachmentInline($binParts[2], true, $binParts[1]);
					} elseif(preg_match('/^[A-z][A-z]*:\/\/' . $_SERVER['SERVER_NAME'] . '/', $url) || !preg_match('/^[A-z][A-z]*:\/\//', $url)){
						$filename = (explode('?', basename($url))[0]);
						$fileParts = pathinfo($filename);
						$ext = $fileParts['extension'];

						if(!in_array($ext, $this->embedImages)){
							continue;
						}
						$directory = str_replace('..', '', dirname($url) . '/');
						$directory = ($directory === '.' ? '' : $directory);
						if(($pos = stripos($directory, $_SERVER['SERVER_NAME']))){
							$directory = substr($directory, (strlen($_SERVER['SERVER_NAME']) + $pos), strlen($directory));
						}
						$this->basedir = ($this->basedir ?: $_SERVER['DOCUMENT_ROOT']) .
							((strlen($this->basedir) > 1 && substr($this->basedir, -1) != '/') ? '/' : '') .
							((strlen($directory) > 1 && substr($directory, -1) != '/') ? '/' : '');
						$attachmentpath = str_replace('//', '/', $this->basedir . $directory . (is_array($filename) ? $filename[0] : $filename));
						$cid = 'cid:' . $this->doaddAttachmentInline($attachmentpath);
					}
					$this->Body = preg_replace('/' . $images[1][$i] . '="' . preg_quote($url, '/') . '"/Ui', $images[1][$i] . '="' . $cid . '"', $this->Body);
				}
			}

			if($this->isUseBaseHref){//Bug #3735
				if($this->ContentType === we_mail_mime::TYPE_HTML && !strpos($this->Body, "<base")){
					$this->Body = str_replace('</head>', "<base href='" . getServerUrl() . "' />\n</head>", $this->Body);
				}
			}

			/* if($this->AltBody == ""){ // nur ersetzen wenn nicht schon eine Textversion gesetzt wurde, wie z.B. im Newsletter häufig der Fall
			  $this->parseHtml2TextPart($this->Body);
			  } */
		}
		/**
		 * Problem ist mit Zend Mail eine E-Mail Nachricht hinzubekommen, die den Regeln entspricht
		 * Erledigt: Reine Textnachricht (text/plain)
		 * Erledigt: Reine HTML-Nachricht (text/html)
		 * Erledigt: Text und HTML ohne Inline-Bilder (multipart/alternative)
		 * Erledigt: Reine HTML-Nachricht mit Inline-Bildern (multipart/related), jedoch ohne Text-Part
		 * Problem: HTML mit Inline-Bildern und Textpart, also multipart/mixed, darin multipart/alternative mit a) text/plain und b) multipart/related mit darin b1) text/html und b2) image/*
		 * Für das notwendige Konstruct siehe http://www.phpeveryday.com/articles/PHP-Email-Using-Embedded-Images-in-HTML-Email-P113.html
		 * Das was Zend Mail da produziert entspricht nicht ganz diesen Vorgaben, scheint aber zu funktionieren
		 */
		if($this->Body){ // es gibt einen HTML-Part
			if($this->inlineAtt){ // es gibt Inline-Bilder
				//$this->setType(Mime::MULTIPART_RELATED); // dann brauchen wir diesen Typ - wird in aktuellen Zend Versionen nicht mehr benötigt
				foreach($this->inlineAtt as $at){
					$this->addAttachment($at);
				}
			}
			$urlReplace = we_folder::getUrlReplacements($GLOBALS['DB_WE']);
			$content = trim($this->Body);
			$this->setBodyHtml(($urlReplace ?
					preg_replace($urlReplace, array_keys($urlReplace), $content) :
					$content));
		}
		if(!$this->AltBody){ //Es gibt keinen Text-Part
			$this->parseHtml2TextPart($this->Body);
		}
		$this->setBodyText(trim($this->AltBody));

		$this->messageBuilt = true;
	}

	public function parseHtml2TextPart($html){
		$this->AltBody = self::getTextContent($html);
	}

	public static function getTextContent($html){
		return trim(strip_tags(preg_replace([
			'-<br[^>]*>-s',
			'-<(ul|ol)[^>]*>-s',
			'-<(head|title|style|script)[^>]*>.*?</\1>-s'
					], [
			"\n",
			"\n\n",
			''
					], strtr($html, [
			"\n" => '',
			"\r" => '',
			'</tr>' => "\n",
			'</td>' => '  ',
			'</th>' => '  ',
			'</h1>' => "\n\n",
			'</h2>' => "\n\n",
			'</h3>' => "\n\n",
			'</h4>' => "\n\n",
			'</h5>' => "\n\n",
			'</h6>' => "\n\n",
			'</p>' => "\n\n",
			'</div>' => "\n",
			'</li>' => "\n",
			'&lt;' => '<',
			'&gt;' => '>',
						]
				))
		));
	}

	public function doaddAttachmentInline($attachment, $isBinData = false, $ext = ''){
		if(!$attachment){
			return;
		}
		if($isBinData){
			$at = new we_mail_mimePart(base64_decode($attachment));
			$at->id = $at->filename = str_replace('.', '', uniqid('', true));
			$at->type = self::get_mime_type($ext, '');
		} else {
			$filename = basename($attachment);
			$rep = str_replace($_SERVER['DOCUMENT_ROOT'], '', $attachment);
			$attachment = str_replace(rtrim($_SERVER['DOCUMENT_ROOT'], '/'), WEBEDITION_PATH . '..', $attachment);
			$at = new we_mail_mimePart(we_base_file::load($attachment));
			$at->id = md5($filename);
			$at->filename = $filename;
			$fileParts = pathinfo($filename);
			$ext = $fileParts['extension'];
			$at->type = self::get_mime_type($ext, $filename, $attachment);
			$loc = getServerUrl() . $rep;
			$at->location = $loc;
		}
		$at->disposition = we_mail_mime::DISPOSITION_INLINE;
		$at->encoding = we_mail_mime::ENCODING_BASE64;
		$this->inlineAtt[] = $at;
		return $at->id;
	}

	/**
	 * Extends Zend Mail addAttachment to be compatible with phpMailer
	 * @access public
	 * @return mime type of ext
	 */
	public function doaddAttachment($attachmentpath){
		if(!$attachmentpath){
			return;
		}
		$attachmentpath = str_replace(rtrim($_SERVER['DOCUMENT_ROOT'], '/'), WEBEDITION_PATH . '..', $attachmentpath);
		$filename = basename($attachmentpath);
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$binarydata = we_base_file::load($attachmentpath);
		$at = new we_mail_mimePart($binarydata);
		$at->disposition = we_mail_mime::DISPOSITION_ATTACHMENT;
		$at->encoding = we_mail_mime::ENCODING_BASE64;
		$at->id = md5($filename);
		$at->filename = $filename;
		$at->type = self::get_mime_type($ext, $filename, $attachmentpath);
		$this->addAttachment($at);
	}

	/**
	 * Gets the mime type of attachments
	 * @access public
	 * @return mime type of ext
	 * Replacement for mime_content_type (deprecated in PHP 5.3, and not available on some older systems
	 * Replacement for  finfo_file, available only for >= PHP 5.3
	 * Da Zend Mail keinen name="yxz" übergibt, kann man den hier einfach anhängen
	 */
	public static function get_mime_type($ext, $name = '', $filepath = ''/* , $useLegacy = false */){
		return (we_base_util::getMimeType($ext, $filepath, we_base_util::MIME_BY_HEAD_THEN_EXTENSION) ?: 'application/octet-stream') . '; name="' . $name . '"';
	}

	/*	 * ******************************************
	 *                  SETTER                  *
	 * ****************************************** */

	/**
	 * Setter for more class vars at once
	 * The array keys represents the names of the class vars
	 *
	 * @param Array $vars
	 */
	public function setClassVars($vars){
		if(is_array($vars) && !empty($vars)){
			foreach($vars as $var => $val){
				$this->set($var, $val);
			}
		}
	}

	public function setCharSet($val = 'UTF-8'){
		$this->_charset = $val;
		$this->setSubject($this->internal_subject);
	}

	public function setContentType($val = we_mail_mime::TYPE_TEXT){
		$this->ContentType = $val;
	}

	public function setEncoding($val = '8bit'){
		$this->Encoding = $val;
	}

	public function setSender($val){
		$this->Sender = $val;
	}

	public function setSubject($val){
		$this->internal_subject = $val;
		$this->clearSubject();
		parent::setSubject($this->internal_subject);
	}

	public function setBaseDir($val){
		$this->basedir = $val;
	}

	public function setIsEmbedImages($val = false){
		$this->isEmbedImages = $val;
	}

	public function setIsUseBaseHref($val = true){
		$this->isUseBaseHref = $val;
	}

	public function setBody($val){
		$this->Body = $val;
	}

	public function Send($transport = NULL){
		try{
			$t = parent::send();
		} catch (Exception $e){
			t_e('warning', 'Error while sending mail: ', $e);
			return false;
		}
		return true;
	}

	/**
	  public function setBodyHtml
	  Quelle: http://www.zfsnippets.com/snippets/view/id/64/zendmail-inline-picture-attachments
	  Ersatz / Erweiterung mit interessantem Ansatz für inline Bilder, funktioniert mit webEdition exterenen Bildern aus fremden Domains (sonst entfernt eine textarea den URL-Teil)
	 */
	public function setBodyHtml2($html, $charset = null, $encoding = we_mail_mime::ENCODING_QUOTEDPRINTABLE, $preload_images = true){
		if($preload_images){
			$this->setType(we_mail_mime::MULTIPART_RELATED);

			$dom = new DOMDocument(null, $this->getCharset());
			@$dom->loadHTML($html);

			$images = $dom->getElementsByTagName('img');
			$status = 0;
			for($i = 0; $i < $images->length; $i++){
				$img = $images->item($i);
				$url = $img->getAttribute('src');
				//FIXME: do we really have to get all files by http?
				$image_content = getHTTP($url, '', $status);

				if($status == 200){
					$pathinfo = pathinfo($url);
					$mime = new we_mail_mimePart($image_content);
					$mime->id = $url;
					$mime->location = $url;
					$mime->type = we_base_util::getMimeType($pathinfo['extension'], '', we_base_util::MIME_BY_EXTENSION);
					$mime->disposition = we_mail_mime::DISPOSITION_INLINE;
					$mime->encoding = we_mail_mime::ENCODING_BASE64;
					$mime->filename = $pathinfo['basename'];

					$this->addAttachment($mime);
				}
			}
		}

		return parent::setBodyHtml($html, $charset, $encoding);
	}

	public function setTextPartOutOfHTML($html){
		//remove css/js code
		$html = preg_replace('|<script.*</script>|', '', preg_replace('|<style.*/[ ]*style>|', '', $html));
		$this->addTextPart(trim(strip_tags(strtr($html, ['&nbsp;' => ' ',
			'<br />' => "\n",
			'<br/>' => "\n"]
					)
				)
			)
		);
	}

}
