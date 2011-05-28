<?php
/**
 * webEdition CMS
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

		require_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");

		protect();
		
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we.inc.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."we_html_tools.inc.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_button.inc.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_multibox.inc.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/sysinfo.inc.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/global.inc.php");
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/lib/we/core/autoload.php");
		

		function getInfoTable($_infoArr,$name) {
			
			$_table = new we_htmlTable(array("width" => "500", "style" => "width: 500px;", "spellspacing"=>"2"), 1, 2);	
			$_i = 0;
			
			foreach ($_infoArr as $_k=>$_v) {
				
				if ($_i % 2) { 
					$_style =  "";
				} else {
					$_style =  "background: #D4DBFA;";
				}
				
				$_table->addRow(1);
				$_table->setRow($_i,array("class"=>"defaultfont","style" => $_style."height:20px;"));
				$_table->setCol($_i,0,array("style" => "width: 200px; height: 20px;font-weight: bold; padding-left: 10px;"),$_k);		
				$_table->setCol($_i,1,array("style" => "width: 250px; height: 20px; padding-left: 10px;"),parseValue($_k,$_v));		
				$_i++;
				
				// highlight some values:
				if($name == "PHP") {
					if($_i == 3 && ini_get_bool('register_globals')) {
						$_table->setColAttributes(2,1,array("style" => "border:1px solid red;"));
					}
					if($_i == 6 && ini_get_bool('register_globals')) {
						$_table->setColAttributes(5,1,array("style" => "border:1px solid red;"));
					}
					if($_i == 9 && ini_get_bool('safe_mode'))
						$_table->setColAttributes(8,1,array("style" => "border:1px solid grey;"));
				}
				
			}
			return $_table->getHtmlCode();
		}
		
		function ini_get_bool($val) {
		    $bool = ini_get($val);
			if($val == "1") {
				return true;
			}
			if($val == "0") {
				return false;
			}
			switch (strtolower($bool)) {
		    	case '1':
		    	case 'on':
				case 'yes':
				case 'true':
					return true;
		    	default:
		    		return false;
		    }
		    return false;
		}
		
		function parseValue($name,$value) {
			global $_types;
			
			if(in_array($name,array_keys($_types))) {
				if($_types[$name]=='bytes' && $value) {
					$value = we_convertIniSizes($value);
					return convertToMb($value) . ' (' . $value . ' Bytes)';
				}
				
			}
			
			return $value;
			
		}
		
		function convertToMb($value) {
			return round($value / (1024*1024),3) . ' MB';
		}
		
		function getConnectionTypes() {
			$_connectionTypes = array();
			if(ini_get("allow_url_fopen") == "1") {
				$_connectionTypes[] = "fopen";
				$_connectionTypeUsed = "fopen";
			}
			if(is_callable("curl_exec")) {
				$_connectionTypes[] = "curl";
				if(sizeof($_connectionTypes) == "1") {
					$_connectionTypeUsed = "curl";
				}
			}
			for($i=0;$i<sizeof($_connectionTypes);$i++) {
				if($_connectionTypes[$i] == $_connectionTypeUsed) {
					$_connectionTypes[$i] = "<u>".$_connectionTypes[$i]."</u>";
				}
			}
			return $_connectionTypes;
		}
		
		function getWarning($message, $value) {
			return '<div style="cursor:pointer; padding-right:20px; padding-left:8px; background:url('.IMAGE_DIR . 'alert_tiny.gif) center right no-repeat;" title="'.$message.'">'.$value.'</div>'; 
		}
		function getInfo($message, $value) {
			return '<div style="cursor:pointer; padding-right:20px; padding-left:8px; background:url('.IMAGE_DIR . 'info_tiny.gif) center right no-repeat;" title="'.$message.'">'.$value.'</div>'; 
		}
		
		$_install_dir = $_SERVER['DOCUMENT_ROOT']. WEBEDITION_DIR;
		
		if(strlen($_install_dir)>35){
			$_install_dir = substr($_install_dir,0,25) . '<acronym title="' . $_install_dir . '">...</acronym>' . substr($_install_dir,-10);
		}
		
		$weVersion  = WE_VERSION;
		if (defined("WE_SVNREV") &&  WE_SVNREV!='0000'){
			
			$weVersion  .= ' (SVN-Revision: '.WE_SVNREV.((defined("WE_VERSION_BRANCH") && WE_VERSION_BRANCH!= 'trunk') ? '|'.WE_VERSION_BRANCH : '').')';
		}
		if(defined("WE_VERSION_SUPP") && WE_VERSION_SUPP!='') $weVersion .= ' '.$l_global[WE_VERSION_SUPP];
		if(defined("WE_VERSION_SUPP_VERSION") && WE_VERSION_SUPP_VERSION!='0' ) $weVersion .= WE_VERSION_SUPP_VERSION;
		
		// GD_VERSION is more precise but only available in PHP 5.2.4 or newer
		if(is_callable("gd_info")) {
			if(defined("GD_VERSION")) {
				$gdVersion = GD_VERSION;
			} else {
				$gdinfoArray = gd_info();
				$gdVersion = $gdinfoArray["GD Version"];
				unset($gdinfoArray);
			}
		} else {
			$gdVersion = "";
		}

		$phpExtensionsDetectable = true;
		
		$phpextensions = get_loaded_extensions();
		foreach ($phpextensions as &$extens){
			$extens= strtolower($extens);
		}
		$phpextensionsMissing = array();
		$phpextensionsMin = array('ctype','date','dom','filter','iconv','libxml','mysql','pcre','Reflection','session','SimpleXML','SPL','standard','tokenizer','xml','zlib');

		if (count($phpextensions)> 3) {
			foreach ($phpextensionsMin as $exten){
				if(!in_array(strtolower($exten),$phpextensions,true) ){$phpextensionsMissing[]=$exten;}
			}
			
			if ( in_array(strtolower('PDO'),$phpextensions) && in_array(strtolower('pdo_mysql'),$phpextensions) ){//sp�ter ODER mysqli
				$phpextensionsSDK_DB = 'PDO &amp; PDO_mysql';	
			} else { $phpextensionsSDK_DB= getWarning($_sysinfo["sdk_db warning"],'-');	}
		} else {
			$phpExtensionsDetectable = false;
			$phpextensionsSDK_DB = 'unkown';
		} 
		$_info = array(
			'webEdition' => array (
				$_sysinfo['we_version'] => $weVersion,
				$_sysinfo['server_name'] => SERVER_NAME,
				$_sysinfo['port'] => defined("HTTP_PORT") ? HTTP_PORT : 80,
				$_sysinfo['protocol'] => getServerProtocol(),
				$_sysinfo['installation_folder'] => $_install_dir,
				$_sysinfo['we_max_upload_size'] => getUploadMaxFilesize()
			),

			'<a href="javascript:showPhpInfo();">PHP</a>' => array(
				$_sysinfo['php_version'] => phpversion(),
				$_sysinfo['zendframework_version'] => (Zend_Version::VERSION != WE_ZFVERSION) ? getWarning($_sysinfo["zend_framework warning"],Zend_Version::VERSION) : Zend_Version::VERSION,
				'register_globals' => (ini_get_bool('register_globals')) ? getWarning($_sysinfo["register_globals warning"],ini_get('register_globals')) : ini_get('register_globals'),
				'max_execution_time' => ini_get('max_execution_time'),
				'memory_limit'  => we_convertIniSizes(ini_get('memory_limit')),
				'short_open_tag' => (ini_get_bool('short_open_tag')) ? getWarning($_sysinfo["short_open_tag warning"],ini_get('short_open_tag')) : ini_get('short_open_tag'),
				'allow_url_fopen' => ini_get('allow_url_fopen'),
				'open_basedir' => ini_get('open_basedir'),
				'safe_mode' => (ini_get_bool('safe_mode')) ? getInfo($_sysinfo["safe_mode warning"],ini_get('safe_mode')) : ini_get('safe_mode'),
				'safe_mode_exec_dir' => ini_get('safe_mode_exec_dir'),
				'safe_mode_gid' => ini_get('safe_mode_gid'),
				'safe_mode_include_dir' => ini_get('safe_mode_include_dir'),
				'upload_max_filesize' => we_convertIniSizes(ini_get('upload_max_filesize')),
				'Suhosin' => (in_array('suhosin',get_loaded_extensions()) ) ? getWarning($_sysinfo["suhosin warning"],in_array('suhosin',get_loaded_extensions())) : ''
			),

			'MySql' => array (
				$_sysinfo['mysql_version'] => (version_compare("5.0.0", getMysqlVer(false)) > 1) ?  getWarning(sprintf($_sysinfo["dbversion warning"],getMysqlVer(false)),getMysqlVer(false) ) :  getMysqlVer(false),
				'max_allowed_packet' => getMaxAllowedPacket()
			),
			
			'System' => array (
				$_sysinfo['connection_types'] => implode(", ", getConnectionTypes()),
				$_sysinfo['mbstring'] => (is_callable("mb_get_info") ? $_sysinfo['available'] : "-"),
				$_sysinfo['gdlib'] => (!empty($gdVersion) ? $_sysinfo['version']." ".$gdVersion : "-"),
				$_sysinfo['exif'] => (is_callable("exif_imagetype") ? $_sysinfo['available'] : getWarning($_sysinfo["exif warning"],'-')),
				$_sysinfo['pcre'] => ((defined("PCRE_VERSION")) ? ( (substr(PCRE_VERSION,0,1)<7)? getWarning($_sysinfo["pcre warning"],$_sysinfo['version'].' '.PCRE_VERSION):$_sysinfo['version'].' '.PCRE_VERSION  ) : getWarning($_sysinfo['available'],$_sysinfo["pcre_unkown"])) ,
				$_sysinfo['sdk_db'] => $phpextensionsSDK_DB,
				$_sysinfo['phpext'] => (!empty($phpextensionsMissing) ? getWarning($_sysinfo["phpext warning2"],$_sysinfo["phpext warning"]. implode(', ', $phpextensionsMissing))  : ($phpExtensionsDetectable ? $_sysinfo['available'] : $_sysinfo['detectable warning']) ),
			),
				
		);
		
		
		$_types = array(
			'upload_max_filesize'=>'bytes',
			'memory_limit'=>'bytes',
			'max_allowed_packet'=>'bytes',
			$_sysinfo['we_max_upload_size']=>'bytes'
		);

		$we_button = new we_button();

		$buttons = $we_button->position_yes_no_cancel(
				$we_button->create_button("close", "javascript:self.close()"),
				'',
				''
		);


		$_space_size = 150;
		$_parts = array();
			foreach ($_info as $_k=>$_v) {
				$_parts[] = array(
					'headline'=> $_k,
					'html'=> getInfoTable($_v, strip_tags($_k)),
					'space'=>$_space_size
			);
		}

		$_parts[] = array(
					'headline'=> '',
					'html'=> '<a href="javascript:showPhpInfo();">'.$_sysinfo['more_info'].'...</a>',
					'space'=>10
		);
		
		
?>
<html>
<head>
 
<title><?php print $_sysinfo['sysinfo']?></title>
<script type="text/javascript" src="<?php print JS_DIR; ?>attachKeyListener.js"></script>
<script type="text/javascript" src="<?php print JS_DIR; ?>keyListener.js"></script>
<script type="text/javascript">
	function closeOnEscape() {
		return true;
	}
	
	function showPhpInfo() {
		document.getElementById("info").style.display="none";
		document.getElementById("more").style.display="block";
		document.getElementById("phpinfo").src = "phpinfo.php";
	}
	
	function showInfoTable() {
		document.getElementById("info").style.display="block";
		document.getElementById("more").style.display="none";
	}

</script>

<?php
		print STYLESHEET;
?>

</head>

<body class="weDialogBody" style="overflow:hidden;" onLoad="self.focus();">
<div id="info" style="display: block;">
<?php		
		print we_multiIconBox::getJS();
		print we_multiIconBox::getHTML('',700, $_parts,30,$buttons,-1,'','',false, "", "", 620, "auto");
		
?>
</div>
<div id="more" style="display:none;">
<?php

		$_parts = array();
		
		$_parts[] = array(
					'headline'=> '',
					'html'=> '<iframe id="phpinfo" style="width:660px;height:530px;">'.$_sysinfo['more_info'].'...</iframe>',
					'space'=>$_space_size
		);
		
		$_parts[] = array(
					'headline'=> '',
					'html'=> '<a href="javascript:showInfoTable();">'.$_sysinfo['back'].'</a>',
					'space'=>10
		);
		
		print we_multiIconBox::getHTML('','100%', $_parts,30,$buttons,-1,'','',false);
		
?>
</div>
</body>
</html>