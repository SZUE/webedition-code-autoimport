<?php
$liveUpdateResponse['Type'] = 'executeOnline';
//error_log(print_r($_REQUEST,true));
$_params = @unserialize(base64_decode($_REQUEST["reqArray"]));
//error_log(print_r($_params["le_communityChoice"],true));
// generate country list:
require_once('I18Nv2/Country.php');
$i18n = new I18Nv2_Country();
$CountryList = $i18n->getAllCodes();
asort($CountryList);
$CountryListOutput = '"" => "-", ';
foreach($CountryList as $k => $v) {
	$CountryListOutput .= '"'.$k.'" => "'.$v.'", ';
}
unset($i18n,$CountryList);
require_once('I18Nv2/Language.php');
$i18n = new I18Nv2_Language();
$LanguageList = $i18n->getAllCodes();
asort($LanguageList);
$LanguageListOutput = '"" => "-", ';
foreach($LanguageList as $k => $v) {
	$LanguageListOutput .= '"'.$k.'" => "'.$v.'", ';
}
unset($i18n,$LanguageList);

$liveUpdateResponse['Code'] = '
<?php

// Community

$Output = "\n";
$Output = "<iframe style=\"width:300px; height:530px; overflow:hidden;\" scrolling=\"no\" frameborder=\"0\" src=\"http://sage3c.ali.intra/htdocs/main/register?client=onlineInstaller\"></iframe>";
	$Content = <<<EOF
{$this->Language[\'content\']}
{$Output}
<br />
{$this->Language[\'privacy\']}<br />
EOF;

	$this->setHeadline($this->Language[\'headline\']);

	$this->setContent($Content);

	//$Template->addError($Error);

	return LE_STEP_NEXT;

?>';	

?>