<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_sqlColAttribute('name', CUSTOMER_TABLE, true, array(), '');
$this->Attributes[] = new weTagData_typeAttribute('type', array(new weTagDataOption('textinput', false, '', array('id460_type','id459_name','id461_size','id462_maxlength','id468_value'), array('id459_name')), new weTagDataOption('textarea', false, '', array('id460_type','id459_name','id463_rows','id464_cols','id468_value'), array('id459_name')), new weTagDataOption('checkbox', false, '', array('id460_type','id459_name','id467_checked'), array('id459_name')), new weTagDataOption('radio', false, '', array('id460_type','id459_name','id467_checked','id468_value'), array('id459_name')), new weTagDataOption('password', false, '', array('id460_type','id459_name','id461_size','id462_maxlength','id468_value'), array('id459_name')), new weTagDataOption('hidden', false, 'customer', array('id460_type','id459_name','id468_value','id473_autofill','id871_languageautofill','id855_doc','id879_usevalue'), array('id459_name')), new weTagDataOption('print', false, '', array('id460_type','id459_name','id741_dateformat','id868_ascountry','id869_aslanguage','id872_outputlanguage','id855_doc','id478_to','id479_nameto'), array('id459_name')), new weTagDataOption('select', false, '', array('id460_type','id459_name','id461_size','id468_value','id469_values'), array('id459_name')), new weTagDataOption('choice', false, '', array('id460_type','id459_name','id461_size','id462_maxlength','id468_value','id469_values'), array('id459_name')), new weTagDataOption('img', false, 'customer', array('id460_type','id459_name','id468_value','id471_id','id628_xml','id779_parentid','id780_width','id781_height','id782_quality','id783_keepratio','id784_maximize','id785_bordercolor','id786_checkboxstyle','id787_inputstyle','id788_checkboxclass','id789_inputclass','id790_checkboxtext','id813_showcontrol','id309_thumbnail'), array('id459_name','id779_parentid')), new weTagDataOption('date', false, '', array('id460_type','id459_name','id741_dateformat','id876_minyear','id877_maxyear','id468_value'), array('id459_name')),new weTagDataOption('country', false, '', array('id460_type','id459_name','id461_size','id855_doc','id468_value'), array('id459_name')),new weTagDataOption('language', false, '', array('id460_type','id459_name','id461_size','id855_doc','id468_value'), array('id459_name'))), true, '');
$this->Attributes[] = new weTagData_textAttribute('size', false, '');
$this->Attributes[] = new weTagData_textAttribute('maxlength', false, '');
$this->Attributes[] = new weTagData_textAttribute('rows', false, '');
$this->Attributes[] = new weTagData_textAttribute('cols', false, '');
$this->Attributes[] = new weTagData_textAttribute('onchange', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('choice', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false,false, '');
$this->Attributes[] = new weTagData_choiceAttribute('checked', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false,false, '');
$this->Attributes[] = new weTagData_textAttribute('value', false, '');
$this->Attributes[] = new weTagData_textAttribute('values', false, '');
$this->Attributes[] = new weTagData_textAttribute('dateformat', false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('id', false, '');
$this->Attributes[] = new weTagData_selectAttribute('removefirstparagraph', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('autofill', array(new weTagDataOption('true', false, '')), false, '');
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('parentid',FILE_TABLE, 'folder', false, 'customer'); }
$this->Attributes[] = new weTagData_textAttribute('width', false, 'customer');
$this->Attributes[] = new weTagData_textAttribute('height', false, 'customer');
$this->Attributes[] = new weTagData_selectAttribute('quality', array(new weTagDataOption('0', false, ''), new weTagDataOption('1', false, ''), new weTagDataOption('2', false, ''), new weTagDataOption('3', false, ''), new weTagDataOption('4', false, ''), new weTagDataOption('5', false, ''), new weTagDataOption('6', false, ''), new weTagDataOption('7', false, ''), new weTagDataOption('8', false, ''), new weTagDataOption('9', false, ''), new weTagDataOption('10', false, '')), false, 'customer');
$this->Attributes[] = new weTagData_selectAttribute('keepratio', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'customer');
$this->Attributes[] = new weTagData_selectAttribute('maximize', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'customer');
$this->Attributes[] = new weTagData_textAttribute('bordercolor', false, 'customer');
$this->Attributes[] = new weTagData_textAttribute('checkboxstyle', false, 'customer');
$this->Attributes[] = new weTagData_textAttribute('inputstyle', false, 'customer');
$this->Attributes[] = new weTagData_textAttribute('checkboxclass', false, 'customer');
$this->Attributes[] = new weTagData_textAttribute('inputclass', false, 'customer');
$this->Attributes[] = new weTagData_textAttribute('checkboxtext', false, 'customer');
$this->Attributes[] = new weTagData_selectAttribute('showcontrol', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'customer');
$this->Attributes[] = new weTagData_sqlRowAttribute('thumbnail',THUMBNAILS_TABLE, false, 'Name', '', '', '');
$this->Attributes[] = new weTagData_selectAttribute('ascountry', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('aslanguage', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('outputlanguage', false, '');
$this->Attributes[] = new weTagData_selectAttribute('languageautofill', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');

$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('nameto', false, '');
$this->Attributes[] = new weTagData_selectAttribute('usevalue', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('minyear', false, '');
$this->Attributes[] = new weTagData_textAttribute('maxyear', false, '');
