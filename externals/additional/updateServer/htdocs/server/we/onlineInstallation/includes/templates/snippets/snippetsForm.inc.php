<?php

$liveUpdateResponse['Type'] = 'executeOnline';
$liveUpdateResponse['Code'] = '
<?php

	$Content = <<<EOF
{$this->Language[\'content\']}<br />
<br />

Leider derzeit noch nicht verfügbar!
EOF;

	$this->setHeadline($this->Language[\'headline\']);

	$this->setContent($Content);

	return LE_STEP_NEXT;

?>';

?>