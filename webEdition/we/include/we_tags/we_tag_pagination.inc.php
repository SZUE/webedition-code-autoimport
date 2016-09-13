<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_tag_pagination(array $attribs, $content){
	return '';

	/* first, check given parameters */
	$ln_range = (int) weTag_getAttribute("range", $attribs);
	$ln_circle = weTag_getAttribute('circle', $attribs, false, we_base_request::BOOL);
	$ln_type = (string) weTag_getAttribute("type", $attribs, 'link');
	$ln_singlePage = (boolean) (((string) weTag_getAttribute("singlePage", $attribs, 'true')) == 'true');
	$ln_prePage = (string) weTag_getAttribute("prePage", $attribs);
	$ln_pastPage = (string) weTag_getAttribute("pastPage", $attribs);
	$ln_preFirstPage = (string) weTag_getAttribute("preFirstPage", $attribs);
	$ln_pastLastPage = (string) weTag_getAttribute("pastLastPage", $attribs);
	$ln_title = (string) weTag_getAttribute("title", $attribs);
	$ln_alt = (string) weTag_getAttribute("alt", $attribs);
	$ln_digits = (int) weTag_getAttribute("digits", $attribs);
	$ln_fillChar = (string) weTag_getAttribute("fillChar", $attribs, '0');
	$ln_style = (string) weTag_getAttribute("style", $attribs);
	$ln_activePageStyle = (string) weTag_getAttribute("activePageStyle", $attribs);
	$ln_class = (string) weTag_getAttribute("class", $attribs);
	$ln_activePageClass = (string) weTag_getAttribute("activePageClass", $attribs);
	$ln_link_style = (string) weTag_getAttribute("link_style", $attribs);
	$ln_link_activePageStyle = (string) weTag_getAttribute("link_activePageStyle", $attribs);
	$ln_link_class = (string) weTag_getAttribute("link_class", $attribs);
	$ln_link_activePageClass = (string) weTag_getAttribute("link_activePageClass", $attribs);
	$ln_link_activePage = (boolean) ((string) weTag_getAttribute("link_activePage", $attribs) == 'true');
	$ln_pageFormat = (string) weTag_getAttribute("pageFormat", $attribs, ':#:');
	$ln_pages = (int) we_tag("listviewPages", $attribs, '');

	$ln_varName = $ln_pages . '_' . md5(print_r($attribs, true));

	static $cache = [];

	/* only start, if output doesn't exist in global var */
	if(isset($cache[$ln_varName])){
		return $cache[$ln_varName];
	}
	/* determine pages */
	$ln_pages = (int) we_tag("listviewPages", $attribs, '');

	/* determine actual page */
	$ln_active_page = (int) we_tag("listviewPageNr", $attribs, '');

	/* determine start  / end Offset */
	$ln_start_offset = we_tag("listviewStart", $attribs, '');
	$ln_end_offset = we_tag("listviewEnd", $attribs, '');

	/* determine rows per page */
	$ln_rows = (int) $GLOBALS['lv']->rows;

	/* determine number of rows */
	$ln_number_of_rows = (int) we_tag("listviewRows", $attribs, '');

	/* determine listview offset */
	$ln_lv_offset = (int) $GLOBALS['lv']->offset;

	/* determine listview name */
	$ln_lv_name = $GLOBALS['lv']->name;

	// is there just 1 page (singlePage) ? When true, check if output has to be generated
	if(($ln_pages > 1) || ($ln_singlePage == true)){
		if($ln_digits > 0){
			$ln_pageNumberFormat = "%'" . substr($ln_fillChar, 0, 1) . $ln_digits . "s";
		} else {
			$ln_pageNumberFormat = "%s";
		}

		$ln_pageFormat = str_replace(':#:', "%1\$s", $ln_pageFormat);
		$ln_pageFormat = str_replace(':start:', "%3\$s", $ln_pageFormat);
		$ln_pageFormat = str_replace(':end:', "%4\$s", $ln_pageFormat);

		if(!empty($ln_style)){
			$ln_style = " style=\"" . $ln_style . "\" ";
		}

		if(!empty($ln_activePageStyle)){
			$ln_activePageStyle = " style=\"" . $ln_activePageStyle . "\" ";
		}

		if(!empty($ln_class)){
			$ln_class = " class=\"" . $ln_class . "\" ";
		}

		if(!empty($ln_activePageClass)){
			$ln_activePageClass = " class=\"" . $ln_activePageClass . "\" ";
		}

		if(!empty($ln_link_style)){
			$ln_link_style = " style=\"" . $ln_link_style . "\" ";
		}

		if(!empty($ln_link_activePageStyle)){
			$ln_link_activePageStyle = " style=\"" . $ln_link_activePageStyle . "\" ";
		}

		if(!empty($ln_link_class)){
			$ln_link_class = " class=\"" . $ln_link_class . "\" ";
		}

		if(!empty($ln_link_activePageClass)){
			$ln_link_activePageClass = " class=\"" . $ln_link_activePageClass . "\" ";
		}

		if(!empty($ln_title)){
			$ln_title = str_replace(':#:', "%1\$s", $ln_title);
			$ln_title = str_replace(':start:', "%3\$s", $ln_title);
			$ln_title = str_replace(':end:', "%4\$s", $ln_title);
			$ln_title = " title=\"" . $ln_title . "\" ";
		}

		/* set output format */
		/* param order: page, url */

		$ln_output_item = $ln_prePage . "<a href=\"%2\$s\"" . $ln_link_style . $ln_link_class . $ln_title . ">" . $ln_pageFormat . "</a>" . $ln_pastPage;
		$ln_output_first_item = $ln_preFirstPage . "<a href=\"%2\$s\"" . $ln_link_style . $ln_link_class . $ln_title . ">" . $ln_pageFormat . "</a>" . $ln_pastPage;
		$ln_output_last_item = $ln_prePage . "<a href=\"%2\$s\"" . $ln_link_style . $ln_link_class . $ln_title . ">" . $ln_pageFormat . "</a>" . $ln_pastLastPage;

		if($ln_link_activePage){
			$ln_output_active_first_item = $ln_preFirstPage . "<a href=\"%2\$s\"" . $ln_link_activePageStyle . $ln_link_activePageClass . $ln_title . ">" . $ln_pageFormat . "</a>" . $ln_pastPage;

			$ln_output_active_item = $ln_prePage . "<a href=\"%2\$s\"" . $ln_link_activePageStyle . $ln_link_activePageClass . $ln_title . ">" . $ln_pageFormat . "</a>" . $ln_pastPage;

			$ln_output_active_last_item = $ln_prePage . "<a href=\"%2\$s\"" . $ln_link_activePageStyle . $ln_link_activePageClass . $ln_title . ">" . $ln_pageFormat . "</a>" . $ln_pastLastPage;
		} else {
			if((!empty($ln_link_activePageStyle)) || (!empty($ln_link_activePageClass))){
				$ln_output_active_first_item = $ln_preFirstPage . "<span " . $ln_link_activePageStyle . $ln_link_activePageClass . ">" . $ln_pageFormat . "</span>" . $ln_pastPage;

				$ln_output_active_item = $ln_prePage . "<span " . $ln_link_activePageStyle . $ln_link_activePageClass . ">" . $ln_pageFormat . "</span>" . $ln_pastPage;

				$ln_output_active_last_item = $ln_prePage . "<span " . $ln_link_activePageStyle . $ln_link_activePageClass . ">" . $ln_pageFormat . "</span>" . $ln_pastLastPage;
			} else {
				$ln_output_active_first_item = $ln_preFirstPage . $ln_pageFormat . $ln_pastPage;
				$ln_output_active_item = $ln_preFirstPage . $ln_pageFormat . $ln_pastPage;
				$ln_output_active_last_item = $ln_prePage . $ln_pageFormat . $ln_pastLastPage;
			}
		}

		if(($ln_type == 'list') || ($ln_type == 'table')){
			if($ln_type == 'list'){
				$ln_start_pattern = "<li";
				$ln_end_pattern = "</li>";
			} else {
				$ln_start_pattern = "<td";
				$ln_end_pattern = "</td>";
			}

			$ln_output_first_item = $ln_start_pattern . $ln_style . $ln_class . '>' . $ln_output_first_item . $ln_end_pattern;
			$ln_output_item = $ln_start_pattern . $ln_style . $ln_class . '>' . $ln_output_item . $ln_end_pattern;
			$ln_output_last_item = $ln_start_pattern . $ln_style . $ln_class . '>' . $ln_output_last_item . $ln_end_pattern;
			$ln_output_active_first_item = $ln_start_pattern . $ln_activePageStyle . $ln_activePageClass . '>' . $ln_output_active_first_item . $ln_end_pattern;
			$ln_output_active_item = $ln_start_pattern . $ln_activePageStyle . $ln_activePageClass . '>' . $ln_output_active_item . $ln_end_pattern;
			$ln_output_active_last_item = $ln_start_pattern . $ln_activePageStyle . $ln_activePageClass . '>' . $ln_output_active_last_item . $ln_end_pattern;
		}

		/* set start/end page */
		/* set default */
		$ln_start = 1;
		$ln_end = $ln_pages;

		/* in circle mode, check if actual_page +/- range is beyond first/last page */
		$ln_start_circle = false;
		$ln_end_circle = false;

		if($ln_circle){
			if($ln_range == 0){
				$ln_range = $ln_pages;
			}
			$ln_start = $ln_active_page - $ln_range;
			$ln_end = $ln_active_page + $ln_range;
			if($ln_start < 1){
				$ln_start_circle = (-1 * $ln_start) + 1;
				if($ln_pages - $ln_start_circle < $ln_active_page){
					$ln_start_circle = $ln_pages - $ln_active_page;
				}
				$ln_start = 1;
			} else {
				$ln_start_circle = false;
			}

			if($ln_end > $ln_pages){
				$ln_end_circle = $ln_end - $ln_pages;
				if($ln_end_circle > $ln_active_page){
					$ln_end_circle = $ln_active_page - 1;
				}
				$ln_end = $ln_pages;
			} else {
				$ln_end_circle = false;
			}
		} else {
			if($ln_range > 0){
				$ln_start = ($ln_active_page - $ln_range > 0) ? $ln_active_page - $ln_range : 1;
				$ln_end = ($ln_active_page + $ln_range <= $ln_pages) ? $ln_active_page + $ln_range : $ln_pages;
			}
		}

		/* generate array with pagenr/offset mapping */

		$ln_output_array = [];

		if($ln_start_circle !== false){
			for($i = $ln_start_circle - 1; $i >= 0; $i--){
				$tmp_page = $ln_end - $i - 1;
				$ln_output_array[] = ['page' => $tmp_page + 1, 'offset' => ($tmp_page * $ln_rows) + $ln_lv_offset, 'start' => ($tmp_page * $ln_rows) + 1, 'end' => ($tmp_page < $ln_pages - 1 ? (($tmp_page + 1) * $ln_rows) : $ln_number_of_rows)];
			}
		}

		for($i = ($ln_start - 1); $i < $ln_end; $i++){
			$ln_output_array[] = ['page' => $i + 1, 'offset' => ($i * $ln_rows) + $ln_lv_offset, 'start' => ($i * $ln_rows) + 1, 'end' => ($i < $ln_pages - 1 ? ($i + 1) * $ln_rows : $ln_number_of_rows)];
		}

		if($ln_end_circle !== false){
			for($i = 0; $i < $ln_end_circle; $i++){
				$ln_output_array[] = ['page' => $i + 1, 'offset' => ($i * $ln_rows) + $ln_lv_offset, 'start' => ($i * $ln_rows) + 1, 'end' => ($i < $ln_pages - 1 ? ($i + 1) * $ln_rows : $ln_number_of_rows)];
			}
		}

		/* generate output from mapping array */

		$ln_output_array_count = count($ln_output_array);

		$ln_output = '';

		$ln_page_string = sprintf($ln_pageNumberFormat, $ln_output_array[0]['page']);
		$ln_start_string = sprintf($ln_pageNumberFormat, $ln_output_array[0]['start']);
		$ln_end_string = sprintf($ln_pageNumberFormat, $ln_output_array[0]['end']);
		$ln_url = $_SERVER["PHP_SELF"] . "?" . listviewBase::we_makeQueryString("we_lv_start_" . $ln_lv_name . "=" . $ln_output_array[0]['offset']);

		$ln_output .= sprintf(($ln_active_page == $ln_output_array[0]['page'] ? $ln_output_active_first_item : $ln_output_first_item), $ln_page_string, $ln_url, $ln_start_string, $ln_end_string);

		if($ln_output_array_count > 2){
			for($i = 1; $i < ($ln_output_array_count - 1); $i++){
				$ln_page_string = sprintf($ln_pageNumberFormat, $ln_output_array[$i]['page']);
				$ln_start_string = sprintf($ln_pageNumberFormat, $ln_output_array[$i]['start']);
				$ln_end_string = sprintf($ln_pageNumberFormat, $ln_output_array[$i]['end']);
				$ln_url = $_SERVER["PHP_SELF"] . "?" . listviewBase::we_makeQueryString("we_lv_start_" . $ln_lv_name . "=" . $ln_output_array[$i]['offset']);

				$ln_output .= sprintf(($ln_active_page == $ln_output_array[$i]['page'] ? $ln_output_active_item : $ln_output_item), $ln_page_string, $ln_url, $ln_start_string, $ln_end_string);
			}
		}

		if($ln_output_array_count > 1){

			$ln_last_key = ($ln_output_array_count - 1);

			$ln_page_string = sprintf($ln_pageNumberFormat, $ln_output_array[$ln_last_key]['page']);
			$ln_start_string = sprintf($ln_pageNumberFormat, $ln_output_array[$ln_last_key]['start']);
			$ln_end_string = sprintf($ln_pageNumberFormat, $ln_output_array[$ln_last_key]['end']);
			$ln_url = $_SERVER["PHP_SELF"] . "?" . listviewBase::we_makeQueryString("we_lv_start_" . $ln_lv_name . "=" . $ln_output_array[$ln_last_key]['offset']);

			$ln_output .= sprintf(($ln_active_page == $ln_output_array[$ln_last_key]['page'] ? $ln_output_active_last_item : $ln_output_last_item), $ln_page_string, $ln_url, $ln_start_string, $ln_end_string);
		}
	} else {
		$ln_output = "";
	}
	return $cache[$ln_varName] = $ln_output;
}
