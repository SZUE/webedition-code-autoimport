<?php


// Available Languages
// Default encoding: UTF-8
$AvailableLanguages = array(
	'de' => 'Deutsch',
	'Deutsch' => 'Deutsch',
	'de_utf8' => 'Deutsch',
	'Deutsch_UTF-8' => 'Deutsch_UTF-8',
	'en' => 'English',
	'English' => 'English',
	'en_utf8' => 'English_UTF-8',
	'English_UTF-8' => 'English_UTF-8',
	
);


// get the requested language
$DefaultLanguage = "English_UTF-8";
$useLng = isset($_REQUEST['clientLng']) ? $_REQUEST['clientLng'] : '';
$useLng = isset($_SESSION['clientLng']) ? $_SESSION['clientLng'] : $useLng;

if(in_array($useLng, $AvailableLanguages)) {
	$Language = $useLng;
	
} else if(key_exists($useLng, $AvailableLanguages)) {
	$Language = $AvailableLanguages[$useLng];
	
} else {
	$Language = $DefaultLanguage;
	
}

define("SHARED_LANGUAGE", $Language);

?>