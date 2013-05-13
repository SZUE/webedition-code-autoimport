<?php
$liveUpdateResponse['Type'] = 'executeOnline';

//error_log(print_r($_REQUEST,true));
$_params = @unserialize(base64_decode($_REQUEST["reqArray"]));
//error_log(print_r($_params["le_communityChoice"],true));
$liveUpdateResponse['Code'] = '
<?php

// Community

$Output = leCheckbox::get("le_communityChoice_ReallySkip", "yes", array(), $this->Language["message"]["reallySkipVerify"], false);
$Error = $this->Language["message"]["reallySkip"]."<br />".$Output;
	$Content = <<<EOF
{$this->Language[\'message\'][\'reallySkip\']}
{$Output}
<br />
{$this->Language[\'privacy\']}<br />
EOF;

	$this->setHeadline($this->Language[\'headline\']);

	$this->setContent($Content);

	//$Template->addError($Error);

	return LE_STEP_NEXT;

?>';	
/*
$liveUpdateResponse['Type'] = 'executeOnline';
$liveUpdateResponse['Code'] = '
<?php

// Community

$Output = leCheckbox::get("le_communityChoice_ReallySkip", "yes", array(), $this->Language["message"]["reallySkipVerify"], false);
$Error = $this->Language["message"]["reallySkip"]."<br />".$Output;
	$Content = <<<EOF
{$this->Language[\'content\']}
{$Output}
<br />
{$this->Language[\'privacy\']}<br />
EOF;

	$this->setHeadline($this->Language[\'headline\']);

	$this->setContent($Content);

	$Template->addError($Error);

	return false;

?>';
*/
?>