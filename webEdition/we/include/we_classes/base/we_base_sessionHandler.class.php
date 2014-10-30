<?php

class we_base_sessionHandler{//implements SessionHandlerInterface => 5.4
	//prevent crashed or killed sessions to stay

	private $execTime;
	private $sessionName;
	private $DB;
	private $id = 0;
	private $crypt = false;
	private $hash = '';

	function __construct(){
		if(defined('SYSTEM_WE_SESSION') && SYSTEM_WE_SESSION && !$this->id){
			ini_set('session.gc_probability', 1);
			ini_set('session.gc_divisor', 100);
			ini_set('session.hash_function', 1); //set sha-1 which will generate 40 bytes of session_id
			ini_set('session.hash_bits_per_character', 4);
			session_set_save_handler(array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 'write'), array($this, 'destroy'), array($this, 'gc'));
			$this->DB = new DB_WE();
			$this->execTime = get_cfg_var('max_execution_time');
			$this->execTime = ($this->execTime > 60 ? 60 : $this->execTime); //time might be wrong (1&1)
			$this->id = uniqid('', true);
			if(!(extension_loaded('suhosin') && ini_get('suhosin.session.encrypt')) && defined('SYSTEM_WE_SESSION_CRYPT') && SYSTEM_WE_SESSION_CRYPT){
				$key = $_SERVER['DOCUMENT_ROOT'] . ($_SERVER['HTTP_USER_AGENT'] ? $_SERVER['HTTP_USER_AGENT'] : 'HTTP_USER_AGENT');
				if(SYSTEM_WE_SESSION_CRYPT === 2){
					if(!isset($_COOKIE['secure'])){
						$_COOKIE['secure'] = we_users_user::getHashIV(30);
						setcookie('secure', $_COOKIE['secure'], 0, '/');
					}
					$key.=$_COOKIE['secure'];
				}
				// due to IE we can't use HTTP_ACCEPT_LANGUAGE, HTTP_ACCEPT_ENCODING - they change the string on each request
				$this->crypt = hash('haval224,4', $key);
				//double key size is needed
				$this->crypt .=$this->crypt;
			}
		}
		session_start();
	}

	function __destruct(){
		if(defined('SYSTEM_WE_SESSION') && SYSTEM_WE_SESSION && isset($_SESSION)){
			session_write_close();
		}
	}

	function open($savePath, $sessName){
		$this->sessionName = $sessName;
		return true;
	}

	function close(){
		//make sure every access will be an error after close
		//unset($_SESSION); //navigate tree will not load in phpmyadmin - they use bad code for that...
		return true;
	}

	function read($sessID){
		$sessID = $this->DB->escape(str_pad(self::getSessionID($sessID), 40, '0'));

		while(!(($data = f('SELECT session_data FROM ' . SESSION_TABLE . ' WHERE session_id=x\'' . $sessID . '\' AND sessionName="' . $this->sessionName . '" AND touch+INTERVAL ' . SYSTEM_WE_SESSION_TIME . ' second>NOW()', '', $this->DB)) &&
		$this->DB->query('UPDATE ' . SESSION_TABLE . ' SET lockid="' . $this->id . '",lockTime=NOW() WHERE session_id=x\'' . $sessID . '\' AND sessionName="' . $this->sessionName . '" AND (lockid="" OR lockid="' . $this->id . '" OR lockTime+INTERVAL ' . $this->execTime . ' second<NOW())') &&
		$this->DB->affected_rows()
		) && $data){
			usleep(100000);
		}
		if($data){
			$data = ($data[0] === '$' && $this->crypt ? we_customer_customer::decryptData($data, $this->crypt) : $data);
			if($data && $data[0] === 'x'){
				//valid gzip
				$data = gzuncompress($data);
				$this->hash = md5($sessID . $data);
				return $data;
			}//else we need a new sessionid; if decrypt failed we might else destroy an existing valid session
		}

		//if we don't find valid data, generate a new ID because of session stealing
		$this->write(self::getSessionID(0), $data, true); //we need a new locked session
		return '';
	}

	function write($sessID, $sessData, $lock = false){
		if(!$sessData && !$lock){
			return $this->destroy($sessID);
		}
		if(md5($sessID . $sessData) == $this->hash){//if nothing changed,we don't have to bother the db
			$this->DB->query('UPDATE ' . SESSION_TABLE . ' SET ' . we_database_base::arraySetter(array(
						'lockid' => $lock ? $this->id : '',
						'lockTime' => sql_function($lock ? 'NOW()' : 'NULL'),
					)) . ' WHERE session_id=x\'' . $sessID . '\' AND sessionName="' . $this->sessionName . '"');

			if($this->DB->affected_rows()){//make sure we had an successfull update
				return true;
			}
		}


		$sessData = SYSTEM_WE_SESSION_CRYPT && $this->crypt ? we_customer_customer::cryptData(gzcompress($sessData, 4), $this->crypt, true) : gzcompress($sessData, 4);
		$sessID = self::getSessionID($sessID);

		$this->DB->query('REPLACE INTO ' . SESSION_TABLE . ' SET ' . we_database_base::arraySetter(array(
					'sessionName' => $this->sessionName,
					'session_id' => sql_function('x\'' . $sessID . '\''),
					'session_data' => sql_function('x\'' . bin2hex($sessData) . '\''),
					'lockid' => $lock ? $this->id : '',
					'lockTime' => sql_function($lock ? 'NOW()' : 'NULL'),
						/* 'uid' => isset($_SESSION['webuser']['ID']) ? $_SESSION['webuser']['ID'] : (isset($_SESSION['user']['ID']) ? $_SESSION['user']['ID'] : 0),
						  -				'tmp' => serialize($_SESSION), */
		)));
		return true;
	}

	function destroy($sessID){
		unset($_SESSION);
		$sessID = $this->DB->escape(str_pad(self::getSessionID($sessID), 40, '0'));
		$this->DB->query('DELETE FROM ' . SESSION_TABLE . ' WHERE session_id=x\'' . $this->DB->escape($sessID) . '\' AND sessionName="' . $this->sessionName . '"');
		return true;
	}

	function gc($sessMaxLifeTime){
		$this->DB->query('DELETE FROM ' . SESSION_TABLE . ' WHERE touch<NOW()-INTERVAL ' . SYSTEM_WE_SESSION_TIME . ' second');
		$this->DB->query('OPTIMIZE TABLE ' . SESSION_TABLE);
		return true;
	}

	private static function getSessionID($sessID){
		if($sessID){
			if(preg_match('|^([a-f0-9]){32,40}$|', $sessID)){
				return $sessID;
			}
		} else {
			session_regenerate_id();
			$sessID = session_id();
			if(preg_match('|^([a-f0-9]){32,40}$|', $sessID)){
				return $sessID;
			}
		}
		//if we had fallen here we have bad session settings, determine by string length
		switch(strlen($sessID)){
			case 40://160/4
				$cnt = 4;
			case 32://160/5 => 4(hex) must have mathed above
				$cnt = 5;
			case 27:
				$cnt = 6;
				break;
			case 26://128/5
				$cnt = 5;
				break;
			case 22://128/6
				$cnt = 6;
				break;
			default:
				session_regenerate_id();
				return session_id();
		}

		//we have to deal with bad php settings
		static $sessStr = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-,';
		$newID = '';
		$tmp = 0;
		for($pos = 0; $pos < strlen($sessID); $pos++){
			$tmp = ($tmp << $cnt) | strpos($sessStr, $sessID[$pos]);
			if(($pos + 1) * $cnt % 4 == 0){
				$newID.=dechex($tmp);
				$tmp = 0;
			}
		}
		if($tmp){//remaining part
			$newID.=dechex($tmp);
		}

		session_id(str_pad($newID, 40, '0'));
		//note: id in cookie will still be delivered in 5/6 bits!
		return session_id();
	}

	static function makeNewID($destroy = false){
		session_regenerate_id(true);
		if($destroy){
			session_destroy();
			$_SESSION = array();
		} else {
			//we need a new lock on the generated id, since partial data is sent to the browser, subsequent calls with the new sessionid might happen
			session_write_close();
		}
		session_start();
	}

}
