<?php

$Code = <<<CODE
<?php

\$this->setHeadline(\$this->Language['headline']);
\$this->setContent(\$this->Language['no_serial']);
return LE_STEP_NEXT;

?>
CODE;

$liveUpdateResponse['Type'] = 'executeOnline';
$liveUpdateResponse['Code'] = $Code;

?>