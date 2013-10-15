<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_choiceAttribute('browser', array(new weTagDataOption('ie'),
	new weTagDataOption('nn'),
	new weTagDataOption('mozilla'),
	new weTagDataOption('safari'),
	new weTagDataOption('opera'),
	new weTagDataOption('lynx'),
	new weTagDataOption('konqueror'),
	new weTagDataOption('firefox'),
	new weTagDataOption('chrome'),
	new weTagDataOption('unknown'),
	), false, false, '');
$this->Attributes[] = new weTagData_choiceAttribute('version', array(new weTagDataOption('eq1'),
	new weTagDataOption('eq2'),
	new weTagDataOption('eq3'),
	new weTagDataOption('eq4'),
	new weTagDataOption('eq5'),
	new weTagDataOption('eq6'),
	new weTagDataOption('eq7'),
	new weTagDataOption('eq8'),
	new weTagDataOption('eq9'),
	new weTagDataOption('up2'),
	new weTagDataOption('up3'),
	new weTagDataOption('up4'),
	new weTagDataOption('up5'),
	new weTagDataOption('up6'),
	new weTagDataOption('up7'),
	new weTagDataOption('up8'),
	new weTagDataOption('up9'),
	new weTagDataOption('down1'),
	new weTagDataOption('down2'),
	new weTagDataOption('down3'),
	new weTagDataOption('down4'),
	new weTagDataOption('down5'),
	new weTagDataOption('down6'),
	new weTagDataOption('down7'),
	new weTagDataOption('down8'),
	new weTagDataOption('down9'),
	), false, true, '');
$this->Attributes[] = new weTagData_choiceAttribute('system', array(new weTagDataOption('win'),
	new weTagDataOption('mac'),
	new weTagDataOption('unix'),
	new weTagDataOption('unknown'),
	), false, false, '');
