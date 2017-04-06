<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/
$this->NeedsEndTag = false;
$this->Groups[] = 'navigation_tags';
$this->Module = 'navigation';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new we_tagData_selectAttribute('name', [new we_tagData_option('name'),
		new we_tagData_option('accesskey'),
		new we_tagData_option('anchor'),
		new we_tagData_option('current'),
		new we_tagData_option('display'),
		new we_tagData_option('href'),
		new we_tagData_option('hreflang'),
		new we_tagData_option('icon'),
		new we_tagData_option('icon_width'),
		new we_tagData_option('icon_height'),
		new we_tagData_option('icon_border'),
		new we_tagData_option('icon_hspace'),
		new we_tagData_option('icon_vspace'),
		new we_tagData_option('icon_align'),
		new we_tagData_option('icon_alt'),
		new we_tagData_option('icon_title'),
		new we_tagData_option('id'),
		new we_tagData_option('lang'),
		new we_tagData_option('level'),
		new we_tagData_option('link_attribute'),
		new we_tagData_option('parentid'),
		new we_tagData_option('position'),
		new we_tagData_option('popup_open'),
		new we_tagData_option('popup_center'),
		new we_tagData_option('popup_xposition'),
		new we_tagData_option('popup_yposition'),
		new we_tagData_option('popup_width'),
		new we_tagData_option('popup_height'),
		new we_tagData_option('popup_toolbar'),
		new we_tagData_option('popup_status'),
		new we_tagData_option('popup_scrollbars'),
		new we_tagData_option('popup_menubar'),
		new we_tagData_option('popup_resizable'),
		new we_tagData_option('popup_location'),
		new we_tagData_option('properties'),
		new we_tagData_option('rel'),
		new we_tagData_option('rev'),
		new we_tagData_option('target'),
		new we_tagData_option('text'),
		new we_tagData_option('title'),
		new we_tagData_option('tabindex'),
		], false, ''),
	new we_tagData_choiceAttribute('attributes', [new we_tagData_option('position'),
		new we_tagData_option('rel'),
		new we_tagData_option('tabindex'),
		new we_tagData_option('accesskey'),
		new we_tagData_option('hreflang'),
		new we_tagData_option('lang'),
		new we_tagData_option('target'),
		new we_tagData_option('anchor'),
		new we_tagData_option('title'),
		new we_tagData_option('current'),
		new we_tagData_option('level'),
		new we_tagData_option('link'),
		new we_tagData_option('image'),
		new we_tagData_option('text'),
		new we_tagData_option('href'),
		new we_tagData_option('icon'),
		new we_tagData_option('rev'),
		], false, true, ''),
	new we_tagData_selectAttribute('complete', [new we_tagData_option('link'),
		new we_tagData_option('image'),
		], false, ''),
];
