<?php
/**

 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @category   Zend
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class we_mail_mimeMessage{
	/**
	 * The Mime_Parts of the message
	 *
	 * @var array
	 */
	protected $_parts = array();

	/**
	 * The Mime object for the message
	 *
	 * @var we_mail_mime|null
	 */
	protected $_mime = null;

	/**
	 * Returns the list of all Mime_Parts in the message
	 *
	 * @return array of Mime_Part
	 */
	public function getParts(){
		return $this->_parts;
	}

	/**
	 * Sets the given array of Mime_Parts as the array for the message
	 *
	 * @param array $parts
	 */
	public function setParts($parts){
		$this->_parts = $parts;
	}

	/**
	 * Append a new Mime_Part to the current message
	 *
	 * @param we_mail_mimePart $part
	 */
	public function addPart(we_mail_mimePart $part){
		/**
		 * @todo check for duplicate object handle
		 */
		$this->_parts[] = $part;
	}

	/**
	 * Check if message needs to be sent as multipart
	 * MIME message or if it has only one part.
	 *
	 * @return boolean
	 */
	public function isMultiPart(){
		return (count($this->_parts) > 1);
	}

	/**
	 * Set Mime object for the message
	 *
	 * This can be used to set the boundary specifically or to use a subclass of
	 * Mime for generating the boundary.
	 *
	 * @param we_mail_mime $mime
	 */
	public function setMime(we_mail_mime $mime){
		$this->_mime = $mime;
	}

	/**
	 * Returns the Mime object in use by the message
	 *
	 * If the object was not present, it is created and returned. Can be used to
	 * determine the boundary used in this message.
	 *
	 * @return we_mail_mime
	 */
	public function getMime(){
		if($this->_mime === null){
			$this->_mime = new we_mail_mime();
		}

		return $this->_mime;
	}

	/**
	 * Generate MIME-compliant message from the current configuration
	 *
	 * This can be a multipart message if more than one MIME part was added. If
	 * only one part is present, the content of this part is returned. If no
	 * part had been added, an empty string is returned.
	 *
	 * Parts are seperated by the mime boundary as defined in Mime. If
	 * {@link setMime()} has been called before this method, the Mime
	 * object set by this call will be used. Otherwise, a new Mime object
	 * is generated and used.
	 *
	 * @param string $EOL EOL string; defaults to
	 * @return string
	 */
	public function generateMessage($EOL = we_mail_mime::LINEEND){
		if(!$this->isMultiPart()){
			$body = array_shift($this->_parts);
			$body = $body->getContent($EOL);
		} else {
			$mime = $this->getMime();

			$boundaryLine = $mime->boundaryLine($EOL);
			$body = 'This is a message in Mime Format.  If you see this, '
				. "your mail reader does not support this format." . $EOL;

			foreach(array_keys($this->_parts) as $p){
				$body .= $boundaryLine
					. $this->getPartHeaders($p, $EOL)
					. $EOL
					. $this->getPartContent($p, $EOL);
			}

			$body .= $mime->mimeEnd($EOL);
		}

		return trim($body);
	}

	/**
	 * Get the headers of a given part as an array
	 *
	 * @param int $partnum
	 * @return array
	 */
	public function getPartHeadersArray($partnum){
		return $this->_parts[$partnum]->getHeadersArray();
	}

	/**
	 * Get the headers of a given part as a string
	 *
	 * @param  int    $partnum
	 * @param  string $EOL
	 * @return string
	 */
	public function getPartHeaders($partnum, $EOL = we_mail_mime::LINEEND){
		return $this->_parts[$partnum]->getHeaders($EOL);
	}

	/**
	 * Get the (encoded) content of a given part as a string
	 *
	 * @param  int    $partnum
	 * @param  string $EOL
	 * @return string
	 */
	public function getPartContent($partnum, $EOL = we_mail_mime::LINEEND){
		return $this->_parts[$partnum]->getContent($EOL);
	}

	/**
	 * Explode MIME multipart string into seperate parts
	 *
	 * Parts consist of the header and the body of each MIME part.
	 *
	 * @param  string $body
	 * @param  string $boundary
	 * @throws we_mail_exception
	 * @return array
	 */
	protected static function _disassembleMime($body, $boundary){
		$start = 0;
		$res = array();
		// find every mime part limiter and cut out the
		// string before it.
		// the part before the first boundary string is discarded:
		$p = strpos($body, '--' . $boundary . "\n", $start);
		if($p === false){
			// no parts found!
			return array();
		}

		// position after first boundary line
		$start = $p + 3 + strlen($boundary);

		while(($p = strpos($body, '--' . $boundary . "\n", $start)) !== false){
			$res[] = substr($body, $start, $p - $start);
			$start = $p + 3 + strlen($boundary);
		}

		// no more parts, find end boundary
		$p = strpos($body, '--' . $boundary . '--', $start);
		if($p === false){
			throw new we_mail_exception('Not a valid Mime Message: End Missing');
		}

		// the remaining part also needs to be parsed:
		$res[] = substr($body, $start, $p - $start);

		return $res;
	}

	/**
	 * Decodes a MIME encoded string and returns a Mime_Message object with
	 * all the MIME parts set according to the given string
	 *
	 * @param  string $message
	 * @param  string $boundary
	 * @param  string $EOL EOL string; defaults to
	 * @throws we_mail_exception
	 * @return we_mail_mimeMessage
	 */
	public static function createFromMessage($message, $boundary, $EOL = we_mail_mime::LINEEND){
		$parts = we_mail_MimeDecode::splitMessageStruct($message, $boundary, $EOL);

		$res = new self();
		foreach($parts as $part){
			// now we build a new MimePart for the current Message Part:
			$newPart = new we_mail_mimePart($part['body']);
			foreach($part['header'] as $key => $value){
				/**
				 * @todo check for characterset and filename
				 */
				switch(strtolower($key)){
					case 'content-type':
						$newPart->type = $value;
						break;
					case 'content-transfer-encoding':
						$newPart->encoding = $value;
						break;
					case 'content-id':
						$newPart->id = trim($value, '<>');
						break;
					case 'content-disposition':
						$newPart->disposition = $value;
						break;
					case 'content-description':
						$newPart->description = $value;
						break;
					case 'content-location':
						$newPart->location = $value;
						break;
					case 'content-language':
						$newPart->language = $value;
						break;
					default:
						throw new we_mail_exception('Unknown header ignored for MimePart:' . $key);
				}
			}
			$res->addPart($newPart);
		}

		return $res;
	}

}
