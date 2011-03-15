<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_choiceAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_typeAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectorAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_sqlRowAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id116_name'] = new weTagData_choiceAttribute('116', 'name', array(new weTagDataOption('WE_PATH', false, ''), new weTagDataOption('WE_ID', false, ''), new weTagDataOption('WE_TEXT', false, ''), new weTagDataOption('wedoc_CreationDate', false, ''), new weTagDataOption('wedoc_ModDate', false, ''), new weTagDataOption('wedoc_Published', false, ''), new weTagDataOption('wedoc_ParentID', false, ''), new weTagDataOption('wedoc_Text', false, ''), new weTagDataOption('WE_SHOPVARIANTS', false, '')), false,false, '');
$GLOBALS['weTagWizard']['attribute']['id117_type'] = new weTagData_typeAttribute('117', 'type', array(new weTagDataOption('-', false, '', array('id117_type'), array()), new weTagDataOption('text', false, '', array('id117_type','id116_name','id119_hyperlink','id121_href','id122_target','id126_num_format','id131_alt','id132_max','id647_striphtml','id720_htmlspecialchars','id390_triggerid','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('date', false, '', array('id117_type','id116_name','id119_hyperlink','id121_href','id122_target','id125_format','id131_alt','id132_max','id720_htmlspecialchars','id390_triggerid','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('img', false, '', array('id117_type','id116_name','id119_hyperlink','id121_href','id122_target','id127_thumbnail','id133_src','id134_width','id135_height','id136_border','id137_hspace','id138_vspace','id139_align','id140_only','id390_triggerid','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('flashmovie', false, '', array('id117_type','id116_name','id134_width','id135_height','id390_triggerid','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('href', false, '', array('id117_type','id116_name','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('link', false, '', array('id117_type','id116_name','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('day', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('dayname', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('week', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('month', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('monthname', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('year', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('select', false, 'object', array('id117_type','id116_name','id720_htmlspecialchars','id390_triggerid','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('binary', false, 'object', array('id117_type','id116_name','id119_hyperlink','id121_href','id122_target','id850_only','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('float', false, '', array('id117_type','id116_name','id119_hyperlink','id121_href','id122_target','id126_num_format','id390_triggerid','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('int', false, 'object', array('id117_type','id116_name','id119_hyperlink','id121_href','id122_target','id390_triggerid','id478_to','id479_nameto'), array('id116_name')), new weTagDataOption('shopVat', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('checkbox', false, '', array('id117_type','id478_to','id479_nameto'), array()), new weTagDataOption('country', false, '', array('id117_type','id872_outputlanguage','id855_doc','id478_to','id479_nameto'), array()), new weTagDataOption('language', false, '', array('id117_type','id872_outputlanguage','id855_doc','id478_to','id479_nameto'), array())), false, '');
if(defined("OBJECT_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id118_classid'] = new weTagData_selectorAttribute('118', 'classid',OBJECT_TABLE, 'object', false, ''); }
$GLOBALS['weTagWizard']['attribute']['id119_hyperlink'] = new weTagData_selectAttribute('119', 'hyperlink', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
if(defined("TEMPLATES_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id120_tid'] = new weTagData_selectorAttribute('120', 'tid',TEMPLATES_TABLE, 'text/weTmpl', false, ''); }
$GLOBALS['weTagWizard']['attribute']['id121_href'] = new weTagData_textAttribute('121', 'href', false, '');
$GLOBALS['weTagWizard']['attribute']['id122_target'] = new weTagData_choiceAttribute('122', 'target', array(new weTagDataOption('_top', false, ''), new weTagDataOption('_parent', false, ''), new weTagDataOption('_self', false, ''), new weTagDataOption('_blank', false, '')), false,false, '');
$GLOBALS['weTagWizard']['attribute']['id123_class'] = new weTagData_textAttribute('123', 'class', false, '');
$GLOBALS['weTagWizard']['attribute']['id124_style'] = new weTagData_textAttribute('124', 'style', false, '');
$GLOBALS['weTagWizard']['attribute']['id125_format'] = new weTagData_textAttribute('125', 'format', false, '');
$GLOBALS['weTagWizard']['attribute']['id126_num_format'] = new weTagData_choiceAttribute('126', 'num_format', array(new weTagDataOption('german', false, ''), new weTagDataOption('french', false, ''), new weTagDataOption('english', false, ''), new weTagDataOption('swiss', false, '')), false,false, '');
$GLOBALS['weTagWizard']['attribute']['id127_thumbnail'] = new weTagData_sqlRowAttribute('127', 'thumbnail',THUMBNAILS_TABLE, false, 'Name', '', '', '');
if(defined("FILE_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id128_id'] = new weTagData_selectorAttribute('128', 'id',FILE_TABLE, 'text/webedition', false, ''); }
$GLOBALS['weTagWizard']['attribute']['id129_parentidname'] = new weTagData_textAttribute('129', 'parentidname', false, '');
$GLOBALS['weTagWizard']['attribute']['id130_winprops'] = new weTagData_textAttribute('130', 'winprops', false, '');
$GLOBALS['weTagWizard']['attribute']['id131_alt'] = new weTagData_textAttribute('131', 'alt', false, '');
$GLOBALS['weTagWizard']['attribute']['id132_max'] = new weTagData_textAttribute('132', 'max', false, '');
$GLOBALS['weTagWizard']['attribute']['id133_src'] = new weTagData_textAttribute('133', 'src', false, '');
$GLOBALS['weTagWizard']['attribute']['id134_width'] = new weTagData_textAttribute('134', 'width', false, '');
$GLOBALS['weTagWizard']['attribute']['id135_height'] = new weTagData_textAttribute('135', 'height', false, '');
$GLOBALS['weTagWizard']['attribute']['id136_border'] = new weTagData_textAttribute('136', 'border', false, '');
$GLOBALS['weTagWizard']['attribute']['id137_hspace'] = new weTagData_textAttribute('137', 'hspace', false, '');
$GLOBALS['weTagWizard']['attribute']['id138_vspace'] = new weTagData_textAttribute('138', 'vspace', false, '');
$GLOBALS['weTagWizard']['attribute']['id139_align'] = new weTagData_selectAttribute('139', 'align', array(new weTagDataOption('left', false, ''), new weTagDataOption('right', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('bottom', false, ''), new weTagDataOption('absmiddle', false, ''), new weTagDataOption('middle', false, ''), new weTagDataOption('texttop', false, ''), new weTagDataOption('baseline', false, ''), new weTagDataOption('absbottom', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id140_only'] = new weTagData_textAttribute('140', 'only', false, '');
$GLOBALS['weTagWizard']['attribute']['id720_htmlspecialchars'] = new weTagData_selectAttribute('720', 'htmlspecialchars', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id142_seeMode'] = new weTagData_selectAttribute('142', 'seeMode', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id628_xml'] = new weTagData_selectAttribute('628', 'xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id143_win2iso'] = new weTagData_selectAttribute('143', 'win2iso', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id144_listviewname'] = new weTagData_textAttribute('144', 'listviewname', false, '');
$GLOBALS['weTagWizard']['attribute']['id647_striphtml'] = new weTagData_selectAttribute('647', 'striphtml', array(new weTagDataOption('false', false, ''), new weTagDataOption('true', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id850_only'] = new weTagData_selectAttribute('850', 'only', array(new weTagDataOption('name', false, ''), new weTagDataOption('path', false, ''), new weTagDataOption('parentpath', false, ''), new weTagDataOption('filename', false, ''), new weTagDataOption('extension', false, ''), new weTagDataOption('filesize', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id872_outputlanguage'] = new weTagData_textAttribute('872', 'outputlanguage', false, '');
$GLOBALS['weTagWizard']['attribute']['id855_doc'] = new weTagData_selectAttribute('855', 'doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');
if(defined("FILE_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id390_triggerid'] = new weTagData_selectorAttribute('390', 'triggerid',FILE_TABLE, 'text/webedition', false, ''); }

$GLOBALS['weTagWizard']['attribute']['id478_to'] = new weTagData_selectAttribute('478', 'to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id479_nameto'] = new weTagData_textAttribute('479', 'nameto', false, '');
