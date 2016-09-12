<?php

//NOTE you are inside the constructor of weTagData.class.php
$this->NeedsEndTag = false;
$this->Groups[] = 'listview_tags';

if(($GLOBALS['WE_LANGUAGE'] == "Deutsch") || ($GLOBALS['WE_LANGUAGE'] == "Deutsch_UTF-8")){
	$this->Description = "Dieser Tag erzeugt eine Seitennavigation f&uuml;r &lt;we:listview&gt;.<br />Die Ausgabe kann als Links, Liste oder Tabelle erfolgen. Die Anzahl der anzuzeigenden Seiten kann mit range eingeschr&auml;nkt werden, &uuml;ber die Style-/Classangaben kann die Ausgabe formatiert werden.<br />Folgende Platzhalter stehen bei title und pageFormat zur Verf&uuml;gung:<br /><p>:#:    - Seitennummer<br />:start: - Nummer des ersten Eintrags der Seite<br />:end:   - Nummer des letzten Eintrags der Seite";
} else {
	$this->Description = "Description will be available soon";
}

/*
$type = new weTagData_selectAttribute('type', array(new weTagDataOption('link', false, ''), new weTagDataOption('list', false, ''), new weTagDataOption('table', false, '')), false, '');

$circle = new weTagData_selectAttribute('circle', weTagData_selectAttribute::getTrueFalse(), false, '');
$range = new weTagData_textAttribute('range', false, '');

$prePage = new weTagData_textAttribute('prePage', false, '');
$pastPage = new weTagData_textAttribute('pastPage', false, '');
$preFirstPage = new weTagData_textAttribute('preFirstPage', false, '');
$pastLastPage = new weTagData_textAttribute('pastLastPage', false, '');
$title = new weTagData_textAttribute('title', false, '');
$digits = new weTagData_textAttribute('digits', false, '');
$fillChar = new weTagData_textAttribute('fillChar', false, '');

$link_activePage = new weTagData_selectAttribute('link_activePage', weTagData_selectAttribute::getTrueFalse(), false, '');

$style = new weTagData_textAttribute('style', false, '');
$activePageStyle = new weTagData_textAttribute('activePageStyle', false, '');
$class = new weTagData_textAttribute('class', false, '');
$activePageClass = new weTagData_textAttribute('activePageClass', false, '');
$link_style = new weTagData_textAttribute('link_style', false, '');
$link_activePageStyle = new weTagData_textAttribute('link_activePageStyle', false, '');
$link_class = new weTagData_textAttribute('link_class', false, '');
$link_activePageClass = new weTagData_textAttribute('link_activePageClass', false, '');
$pageFormat = new weTagData_textAttribute('pageFormat', false, '');

$singlePage = new weTagData_selectAttribute('singlePage', weTagData_selectAttribute::getTrueFalse(), false, '');


$this->Attributes = [$type, $circle, $range, $prePage, $pastPage, $preFirstPage, $pastLastPage, $title, $digits, $fillChar, $link_activePage, $style, $activePageStyle, $class, $activePageClass, $link_style, $link_activePageStyle, $link_class, $link_activePageClass, $pageFormat, $singlePage];
 *
 */