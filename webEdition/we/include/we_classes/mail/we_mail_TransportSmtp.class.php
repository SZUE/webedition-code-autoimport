<?php
/**
 * Zend Framework
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
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * SMTP connection object
 *
 * Loads an instance of Mail_Protocol_Smtp and forwards smtp transactions
 *
 * @category   Zend
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class we_mail_TransportSmtp extends we_mail_TransportAbstract{
	/**
	 * EOL character string used by transport
	 * @var string
	 * @access public
	 */
	public $EOL = "\n";

	/**
	 * Remote smtp hostname or i.p.
	 *
	 * @var string
	 */
	protected $_host;

	/**
	 * Port number
	 *
	 * @var integer|null
	 */
	protected $_port;

	/**
	 * Local client hostname or i.p.
	 *
	 * @var string
	 */
	protected $_name = 'localhost';

	/**
	 * Authentication type OPTIONAL
	 *
	 * @var string
	 */
	protected $_auth;

	/**
	 * Config options for authentication
	 *
	 * @var array
	 */
	protected $_config;

	/**
	 * Instance of Mail_Protocol_Smtp
	 *
	 * @var Mail_Protocol_Smtp
	 */
	protected $_connection;

	/**
	 * Constructor.
	 *
	 * @param  string $host OPTIONAL (Default: 127.0.0.1)
	 * @param  array|null $config OPTIONAL (Default: null)
	 * @return void
	 *
	 * @todo Someone please make this compatible
	 *       with the SendMail transport class.
	 */
	public function __construct($host = '127.0.0.1', Array $config = array()){
		if(isset($config['name'])){
			$this->_name = $config['name'];
		}
		if(isset($config['port'])){
			$this->_port = $config['port'];
		}
		if(isset($config['auth'])){
			$this->_auth = $config['auth'];
		}

		$this->_host = $host;
		$this->_config = $config;
	}

	/**
	 * Class destructor to ensure all open connections are closed
	 *
	 * @return void
	 */
	public function __destruct(){
		if($this->_connection instanceof we_mail_ProtocolSmtp){
			try{
				$this->_connection->quit();
			} catch (we_mail_exception $e){
				// ignore
			}
			$this->_connection->disconnect();
		}
	}

	/**
	 * Sets the connection protocol instance
	 *
	 * @param we_mail_ProtocolAbstract $client
	 *
	 * @return void
	 */
	public function setConnection(we_mail_ProtocolAbstract $connection){
		$this->_connection = $connection;
	}

	/**
	 * Gets the connection protocol instance
	 *
	 * @return Mail_Protocol|null
	 */
	public function getConnection(){
		return $this->_connection;
	}

	/**
	 * Send an email via the SMTP connection protocol
	 *
	 * The connection via the protocol adapter is made just-in-time to allow a
	 * developer to add a custom adapter if required before mail is sent.
	 *
	 * @return void
	 * @todo Rename this to sendMail, it's a public method...
	 */
	public function _sendMail(){
		// If sending multiple messages per session use existing adapter
		if(!($this->_connection instanceof we_mail_ProtocolSmtp)){
			// Check if authentication is required and determine required class
			switch($this->_auth){
				case 'login':
					$con = new we_mail_ProtocolSmtpLogin($this->_host, $this->_port, $this->_config);
					break;
				case 'plain':
					$con = new we_mail_ProtocolSmtpPlain($this->_host, $this->_port, $this->_config);
					break;
				case 'crammd5':
					$con = new we_mail_ProtocolSmtpCrammd5($this->_host, $this->_port, $this->_config);
					break;
				default:
					$con = new we_mail_ProtocolSmtp($this->_host, $this->_port, $this->_config);
			}
			$this->setConnection($con);
			$this->_connection->connect();
			$this->_connection->helo($this->_name);
		} else {
			// Reset connection to ensure reliable transaction
			$this->_connection->rset();
		}

		// Set sender email address
		$this->_connection->mail($this->_mail->getReturnPath());

		// Set recipient forward paths
		foreach($this->_mail->getRecipients() as $recipient){
			$this->_connection->rcpt($recipient);
		}

		// Issue DATA command to client
		$this->_connection->data($this->header . we_mail_mime::LINEEND . $this->body);
	}

	/**
	 * Format and fix headers
	 *
	 * Some SMTP servers do not strip BCC headers. Most clients do it themselves as do we.
	 *
	 * @access  protected
	 * @param   array $headers
	 * @return  void
	 * @throws  Transport_Exception
	 */
	protected function _prepareHeaders($headers){
		if(!$this->_mail){
			throw new we_mail_exception('_prepareHeaders requires a registered Mail object');
		}

		unset($headers['Bcc']);

		// Prepare headers
		parent::_prepareHeaders($headers);
	}

}
