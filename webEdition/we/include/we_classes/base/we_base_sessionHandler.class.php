<?php

class we_base_sessionHandler implements SessionHandlerInterface{
	//prevent crashed or killed sessions to stay
	private $execTime;
	private $sessionName;
	private $DB;
	private $id = 0;
	private $crypt = false;

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
			if(!(extension_loaded('suhosin') && ini_get('suhosin.session.encrypt'))){//make it possible to keep users when switching
				$this->crypt = hash('haval224,4', $_SERVER['DOCUMENT_ROOT'] . $_SERVER['HTTP_USER_AGENT'] . (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '') . (isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : ''));
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
		unset($_SESSION);
		return true;
	}

	function read($sessID){
		$sessID = $this->DB->escape(str_pad(self::getSessionID($sessID), 40, '0'));

		while(!(($data = f('SELECT session_data FROM ' . SESSION_TABLE . ' WHERE session_id=x\'' . $sessID . '\' AND touch+INTERVAL ' . SYSTEM_WE_SESSION_TIME . ' second>NOW()', '', $this->DB)) &&
		$this->DB->query('UPDATE ' . SESSION_TABLE . ' SET lockid="' . $this->id . '",lockTime=NOW() WHERE session_id=x\'' . $sessID . '\' AND (lockid="" OR lockid="' . $this->id . '" OR lockTime+INTERVAL ' . $this->execTime . ' second<NOW())') &&
		$this->DB->affected_rows()
		) && $data){
			usleep(100000);
		}
		if($data){
			$data = ($data[0] == '$' && $this->crypt ? we_customer_customer::decryptData($data, $this->crypt) : $data);
			if($data){
				$tmp = gzuncompress($data);
				if(!$tmp){
					t_e($data);
				}
				return $tmp;
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

		$sessData = SYSTEM_WE_SESSION_CRYPT && $this->crypt ? we_customer_customer::cryptData(gzcompress($sessData, 4), $this->crypt, true) : gzcompress($sessData, 4);
		$sessID = self::getSessionID($sessID);

		$this->DB->query('REPLACE INTO ' . SESSION_TABLE . ' SET ' . we_database_base::arraySetter(array(
				'session_id' => sql_function('x\'' . $sessID . '\''),
				'session_data' => $sessData,
				'sessionName' => $this->sessionName,
				'lockid' => $lock ? $this->id : '',
				'lockTime' => sql_function($lock ? 'NOW()' : 'NULL'),
				/*
				'uid' => isset($_SESSION['webuser']['ID']) ? $_SESSION['webuser']['ID'] : (isset($_SESSION['user']['ID']) ? $_SESSION['user']['ID'] : 0),
				'tmp' => serialize($_SESSION),*/
		)));
		return true;
	}

	function destroy($sessID){
		$sessID = $this->DB->escape(str_pad(self::getSessionID($sessID), 40, '0'));
		$this->DB->query('DELETE FROM ' . SESSION_TABLE . ' WHERE session_id=x\'' . $this->DB->escape($sessID) . '\'');
		return true;
	}

	function gc($sessMaxLifeTime){
		$this->DB->query('DELETE FROM ' . SESSION_TABLE . ' WHERE touch<NOW()-INTERVAL ' . SYSTEM_WE_SESSION_TIME . ' second');
		$this->DB->query('OPTIMIZE TABLE ' . SESSION_TABLE);
		return true;
	}

	private static function getSessionID($sessID){
		if($sessID && preg_match('|^([a-f0-9]){32,40}$|i', $sessID)){
			return $sessID;
		}
		session_regenerate_id();
		$cnt = ini_get('session.hash_bits_per_character');
		if($cnt == 4){//this is easy, since this is set as hex
			//a 4 bit value didn't match, we neeed a new id
			return session_id();
		}
		//we have to deal with bad php settings
		static $sessStr = array(
			5 => '0123456789abcdefghijklmnopqrstuv',
			6 => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-,',
		);
		$sessID = session_id();
		$newID = '';
		$tmp = '';
		for($pos = 0; $pos < strlen($sessID); $pos++){
			$tmp = $tmp << 4 | strpos($sessStr[$cnt], $sessID[$pos]);
			if(($pos + 1) * $cnt % 4 == 0){
				$newID.=dechex($tmp);
				$tmp = 0;
			}
		}
		session_id(str_pad($newID, 40, $newID));
		//note: id in cookie will still be delivered in 5/6 bits!
		return session_id();
	}

	static function makeNewID($destroy = false){
		session_regenerate_id(true);
		if($destroy){
			session_destroy();
		} else {
			//we need a new lock on the generated id, since partial data is sent to the browser, subsequent calls with the new sessionid might happen
			session_write_close();
		}
		session_start();
	}

}
