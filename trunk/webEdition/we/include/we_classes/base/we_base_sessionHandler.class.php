<?php

class we_base_sessionHandler{

	private $enabled = false;
	// session-lifetime
	private $lifeTime;
	private $execTime;
	private $sessName;
	private $DB;
	private $id = 0;
	private $crypt = false;

	function __construct(){
		if($this->enabled && !$this->id){
			ini_set('session.gc_probability', 1);
			ini_set('session.gc_divisor', 1000);
			session_set_save_handler(array($this, "open"), array($this, "close"), array($this, "read"), array($this, "write"), array($this, "destroy"), array($this, "gc"));
			$this->DB = new DB_WE();
			$this->lifeTime = get_cfg_var('session.gc_maxlifetime');
			$this->execTime = get_cfg_var('max_execution_time');
			$this->id = uniqid('', true);
			if(!(in_array('suhosin', get_loaded_extensions()) && ini_get('suhosin.session.encrypt'))){
				$this->crypt = hash('haval224,4', $_SERVER['DOCUMENT_ROOT'] . $_SERVER["HTTP_USER_AGENT"] . $_SERVER["HTTP_ACCEPT_LANGUAGE"] . $_SERVER["HTTP_ACCEPT_ENCODING"], true);
				$this->crypt .=$this->crypt;
			}
		}
		session_start();
	}

	function __destruct(){
		if($this->enabled && $_SESSION){
			session_write_close();
		}
	}

	function open($savePath, $sessName){
		$this->sessName = $sessName;
		return true;
	}

	function close(){
		return true;
	}

	function read($sessID){
		$sessID = $this->DB->escape($sessID);

		while(!(($data = f('SELECT session_data FROM tblSessions WHERE session_id="' . $sessID . '" AND touch+INTERVAL ' . $this->lifeTime . ' second>NOW()', '', $this->DB)) &&
		$this->DB->query('UPDATE tblSessions SET lockid="' . $this->id . '",lockTime=NOW() WHERE session_id="' . $sessID . '" AND (lockid="" OR lockid="' . $this->id . '" OR lockTime+INTERVAL ' . $this->execTime . ' second<NOW())') &&
		$this->DB->affected_rows()
		) && $data){
			usleep(100000);
		}
		if($data){
			$data = gzuncompress($data);
			$data = $data && $this->crypt ? we_customer_customer::decryptData($data, $this->crypt) : $data;
			return $data;
		}
		return '';
	}

	function write($sessID, $sessData){
		if(!$sessData){
			return $this->destroy($sessID);
		}
		$sessData = $this->crypt ? we_customer_customer::cryptData($sessData, $this->crypt) : $sessData;
		//crypt data!!
		$this->DB->query('REPLACE INTO tblSessions SET ' . we_database_base::arraySetter(array(
				'session_id' => $sessID,
				'session_data' => gzcompress($sessData, 9),
		)));
		return true;
	}

	function destroy($sessID){
		$this->DB->query('DELETE FROM tblSessions WHERE session_id="' . $this->DB->escape($sessID) . '"');
		return true;
	}

	function gc($sessMaxLifeTime){
		$this->DB->query('DELETE FROM tblSessions WHERE touch<NOW()-INTERVAL ' . $sessMaxLifeTime . ' second');
		return true;
	}

}
