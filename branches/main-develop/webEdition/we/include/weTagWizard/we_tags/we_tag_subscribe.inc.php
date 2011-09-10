<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'if_tags';
$this->Module = 'newsletter';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_typeAttribute('type', array(new weTagDataOption('email', false, 'newsletter', array('id505_type','id506_size','id507_maxlength','id508_value','id510_class','id511_style','id512_onchange'), array()), new weTagDataOption('htmlCheckbox', false, 'newsletter', array('id505_type','id510_class','id511_style','id513_checked'), array()), new weTagDataOption('htmlSelect', false, 'newsletter', array('id505_type','id508_value','id509_values','id510_class','id511_style'), array()), new weTagDataOption('firstname', false, 'newsletter', array('id505_type','id506_size','id507_maxlength','id508_value','id510_class','id511_style','id512_onchange'), array()), new weTagDataOption('lastname', false, 'newsletter', array('id505_type','id506_size','id507_maxlength','id508_value','id510_class','id511_style','id512_onchange'), array()), new weTagDataOption('salutation', false, 'newsletter', array('id505_type','id506_size','id507_maxlength','id508_value','id509_values','id510_class','id511_style','id512_onchange'), array()), new weTagDataOption('title', false, 'newsletter', array('id505_type','id506_size','id507_maxlength','id508_value','id509_values','id510_class','id511_style','id512_onchange'), array()), new weTagDataOption('listCheckbox', false, 'newsletter', array('id505_type','id510_class','id511_style','id513_checked'), array()), new weTagDataOption('listSelect', false, 'newsletter', array('id505_type','id506_size','id509_values','id510_class','id511_style'), array())), false, '');
$this->Attributes[] = new weTagData_textAttribute('size', false, '');
$this->Attributes[] = new weTagData_textAttribute('maxlength', false, '');
$this->Attributes[] = new weTagData_textAttribute('value', false, '');
$this->Attributes[] = new weTagData_textAttribute('values', false, '');
$this->Attributes[] = new weTagData_textAttribute('class', false, '');
$this->Attributes[] = new weTagData_textAttribute('style', false, '');
$this->Attributes[] = new weTagData_textAttribute('onchange', false, '');
$this->Attributes[] = new weTagData_selectAttribute('checked', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');
