<?php

$liveUpdateResponse['Type'] = 'executeOnline';
$liveUpdateResponse['Code'] = '
<?php

// Community

$Error = $this->Language["error"]["userExists"];
	$Content = <<<EOF
{$this->Language[\'content\']}

<br />
{$this->Language[\'privacy\']}<br />
EOF;

	$this->setHeadline($this->Language[\'headline\']);

	$this->setContent($Content);

	$Template->addError($Error);

	return true;

?>';

?>

