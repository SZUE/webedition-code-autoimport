<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_choiceAttribute('name', array(new weTagDataOption('WE_PATH', false, ''), new weTagDataOption('WE_ID', false, ''), new weTagDataOption('WE_TEXT', false, ''), new weTagDataOption('wedoc_CreationDate', false, ''), new weTagDataOption('wedoc_ModDate', false, ''), new weTagDataOption('wedoc_Published', false, ''), new weTagDataOption('wedoc_ParentID', false, ''), new weTagDataOption('wedoc_Text', false, ''), new weTagDataOption('WE_SHOPVARIANTS', false, '')), false,false, '');
$this->Attributes[] = new weTagData_typeAttribute('type', array(new weTagDataOption('-', false, '', array('id117_type'), array()), new weTagDataOption('text', false, '', array('id117_type','id116_name','id119_hyperlink','id121_href','id122_target','id126_num_format','id131_alt','id132_max','id647_striphtml','id720_htmlspecialchars','id390_triggerid','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('date', false, '', array('id117_type','id116_name','id119_hyperlink','id121_href','id122_target','id125_format','id131_alt','id132_max','id720_htmlspecialchars','id390_triggerid','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('img', false, '', array('id117_type','id116_name','id119_hyperlink','id121_href','id122_target','id127_thumbnail','id133_src','id134_width','id135_height','id136_border','id137_hspace','id138_vspace','id139_align','id140_only','id390_triggerid','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('flashmovie', false, '', array('id117_type','id116_name','id134_width','id135_height','id390_triggerid','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('href', false, '', array('id117_type','id116_name','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('link', false, '', array('id117_type','id116_name','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('day', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('dayname', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('week', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('month', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('monthname', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('year', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('select', false, 'object', array('id117_type','id116_name','id891_usekey','id720_htmlspecialchars','id390_triggerid','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('binary', false, 'object', array('id117_type','id116_name','id119_hyperlink','id121_href','id122_target','id850_only','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('float', false, '', array('id117_type','id116_name','id119_hyperlink','id121_href','id122_target','id126_num_format','id390_triggerid','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('int', false, 'object', array('id117_type','id116_name','id119_hyperlink','id121_href','id122_target','id390_triggerid','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('shopVat', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('checkbox', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('country', false, '', array('id117_type','id872_outputlanguage','id855_doc','id478_to','id479_nameto'), array()), new weTagDataOption('language', false, '', array('id117_type','id872_outputlanguage','id855_doc','id478_to','id479_nameto'), array())), false, '');
if(defined("OBJECT_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('classid',OBJECT_TABLE, 'object', false, ''); }
$this->Attributes[] = new weTagData_selectAttribute('hyperlink', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
if(defined("TEMPLATES_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('tid',TEMPLATES_TABLE, 'text/weTmpl', false, ''); }
$this->Attributes[] = new weTagData_textAttribute('href', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('target', array(new weTagDataOption('_top', false, ''), new weTagDataOption('_parent', false, ''), new weTagDataOption('_self', false, ''), new weTagDataOption('_blank', false, '')), false,false, '');
$this->Attributes[] = new weTagData_textAttribute('class', false, '');
$this->Attributes[] = new weTagData_textAttribute('style', false, '');
$this->Attributes[] = new weTagData_textAttribute('format', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('num_format', array(new weTagDataOption('german', false, ''), new weTagDataOption('french', false, ''), new weTagDataOption('english', false, ''), new weTagDataOption('swiss', false, '')), false,false, '');
$this->Attributes[] = new weTagData_sqlRowAttribute('thumbnail',THUMBNAILS_TABLE, false, 'Name', '', '', '');
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('id',FILE_TABLE, 'text/webedition', false, ''); }
$this->Attributes[] = new weTagData_textAttribute('parentidname', false, '');
$this->Attributes[] = new weTagData_textAttribute('winprops', false, '');
$this->Attributes[] = new weTagData_textAttribute('alt', false, '');
$this->Attributes[] = new weTagData_textAttribute('max', false, '');
$this->Attributes[] = new weTagData_textAttribute('src', false, '');
$this->Attributes[] = new weTagData_textAttribute('width', false, '');
$this->Attributes[] = new weTagData_textAttribute('height', false, '');
$this->Attributes[] = new weTagData_textAttribute('border', false, '');
$this->Attributes[] = new weTagData_textAttribute('hspace', false, '');
$this->Attributes[] = new weTagData_textAttribute('vspace', false, '');
$this->Attributes[] = new weTagData_selectAttribute('align', array(new weTagDataOption('left', false, ''), new weTagDataOption('right', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('bottom', false, ''), new weTagDataOption('absmiddle', false, ''), new weTagDataOption('middle', false, ''), new weTagDataOption('texttop', false, ''), new weTagDataOption('baseline', false, ''), new weTagDataOption('absbottom', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('only', false, '');
$this->Attributes[] = new weTagData_selectAttribute('htmlspecialchars', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('seeMode', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('win2iso', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('listviewname', false, '');
$this->Attributes[] = new weTagData_selectAttribute('striphtml', array(new weTagDataOption('false', false, ''), new weTagDataOption('true', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('only', array(new weTagDataOption('name', false, ''), new weTagDataOption('path', false, ''), new weTagDataOption('parentpath', false, ''), new weTagDataOption('filename', false, ''), new weTagDataOption('extension', false, ''), new weTagDataOption('filesize', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('outputlanguage', false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('triggerid',FILE_TABLE, 'text/webedition', false, ''); }
$this->Attributes[] = new weTagData_selectAttribute('usekey', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');

$this->Attributes[] = new weTagData_selectAttribute('to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('nameto', false, '');
