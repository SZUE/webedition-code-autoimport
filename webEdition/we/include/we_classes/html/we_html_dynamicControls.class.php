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
//FIXME: remove this class, since only used in we_users

/**
 * Class we_dynamicControls
 *
 * Provides functions for creating layers that can hide and unhide.
 */
class we_html_dynamicControls{
	/*	 * ***********************************************************************
	 * VARIABLES
	 * *********************************************************************** */
	var $arrow_hint_closed;
	var $arrow_hint_opened;

	/*	 * ***********************************************************************
	 * CONSTRUCTOR
	 * *********************************************************************** */

	/**
	 * Constructor of class.
	 *
	 * @return     we_html_dynamicControls
	 */
	function __construct(){

		// Set hint text for the groups arrows
		$this->arrow_hint_closed = g_l('dynamicControls', '[expand_group]');
		$this->arrow_hint_opened = g_l('dynamicControls', '[fold_group]');
	}

	/*	 * ***********************************************************************
	 * FUNCTIONS
	 * *********************************************************************** */

	/**
	 * This function creates the JavaScript needed to fold or unfold groups
	 *
	 * @param      $groups                                 array
	 * @param      $filter                                 array
	 * @param      $use_with_user_module                   bool
	 *
	 * @see        fold_checkbox_groups()
	 *
	 * @return     string
	 */
	private function js_fold_checkbox_groups($groups, $filter, $use_with_user_module){
		// Initialize string representing the array of all groups
		$groups_array = [];

		// Build array of all groups
		foreach(array_keys($groups) as $groups_key){
			// Filter out groups not to be shown
			$count_filters = count($filter);
			$show_group = true;

			// Check, if current group is in groups not to be shown
			for($count = 0; $count < $count_filters; $count++){
				if(isset($filter[$count])){
					if($groups_key == $filter[$count]){
						$show_group = false;
					}
				}
			}

			// Now build string representing the array of all groups if this group is visible
			if($show_group){
				$groups_array[] = $groups_key;
			}
		}

		// Build string to be returned by the function
		return we_html_element::jsScript(JS_DIR . 'dynamicControls.js', '', ['id' => 'loadVarDynamicControls', 'data-groups' => setDynamicVar($groups_array)]);
	}

	/**
	 * This function creates a menu with different groups that contain checkboxes.
	 * These groups can be folded and unfolded.
	 *
	 * @param      $groups                                 array
	 * @param      $main_titles                            array
	 * @param      $titles                                 array
	 * @param      $item_names                             array
	 * @param      $open_group                             string
	 * @param      $filter                                 array
	 * @param      $check_permissions                      bool
	 * @param      $use_form                               bool
	 * @param      $form_name                              string
	 * @param      $form_group_name                        string
	 * @param      $display_check_all                      bool
	 * @param      $use_with_user_module                   bool
	 * @param      $width                                  int
	 * @param      $bgcolor                                string
	 * @param      $seperator_color                        string
	 *
	 * @see        js_fold_checkbox_groups()
	 *
	 * @return     string
	 */
	function fold_checkbox_groups($groups, $parentGroups, $main_titles, $titles, $item_names, $open_group = "", $filter = "", $check_permissions = false, $use_form = false, $form_name = "", $form_group_name = "", $display_check_all = false, $use_with_user_module = false, $width = 500, $bgcolor = "#DDDDDD", $seperator_color = "#EEEEEE"){
		// Include the needed JavaScript
		$content = $this->js_fold_checkbox_groups($groups, $filter, $use_with_user_module);

		// Count the number of groups to be displayed
		$visible_groups = count($groups) - count($filter);

		// Initialize the counter for number of seperators being painted later
		$seperator_counter = 0;

		// Go through all groups to be displayed
		foreach($groups as $groups_key => $groups_value){
			// Filter out groups not to be shown
			$count_filters = count($filter);
			$show_group = true;

			// Check, if current group is in groups not to be shown
			for($i = 0; $i < $count_filters; $i++){
				if($groups_key == $filter[$i]){
					$show_group = false;
				}
			}

			// Now only show the group if it was not found in the groups not to be shown
			if($show_group){

				// Set variable for painting of seperator
				$seperator_counter++;

				// Set title of group
				$checkbox_title = $main_titles[$groups_key];

				//	the different permission-groups shall be sorted alphabetically
				//	therefore the content is first saved in an array.
				// Build header of group
				$contentTable[$main_titles[$groups_key]] = '<table class="default" style="width:' . $width . 'px;border-top:5px solid ' . $seperator_color . '">';

				// Continue building header of group
				$contentTable[$main_titles[$groups_key]] .= '
<tr style="vertical-align:middle;background-color:' . $bgcolor . ';line-height:24px;">
	<td style="width:30px;padding-left:5px;">
		<a href="javascript:toggle(\'' . $groups_key . '\', \'show_single\', \'' . $use_form . '\', \'' . $form_name . '\', \'' . $form_group_name . '\');" name="arrow_link' . $groups_key . '">';

				// If a group is open display it unfolded
				$show_open = false;

				// Check, if current group is in groups to be shown opened
				if($groups_key == $open_group){
					$show_open = true;
				}

				if($show_open){
					// Define various values for expanded groups
					$arrow_image = 'fa fa-lg fa-caret-down fa-fw';
					$arrow_hint = $this->arrow_hint_opened;
					$style_display = "block";
				} else {
					// Define various values for folded groups
					$arrow_image = 'fa fa-lg fa-caret-right fa-fw';
					$arrow_hint = $this->arrow_hint_closed;
					$style_display = "none";
				}

				// Build header for open group
				$contentTable[$main_titles[$groups_key]] .= '
			<i class="' . $arrow_image . '" title="' . $arrow_hint . '" name="arrow_' . $groups_key . '"></i></a></td>
		<td class="defaultfont" colspan="3"><label for="arrow_link_' . $groups_key . '" style="cursor: pointer;" onclick="toggle(\'' . $groups_key . '\', \'show_single\', \'' . $use_form . '\', \'' . $form_name . '\', \'' . $form_group_name . '\');"><b>' . $checkbox_title . '</b></label></td>
	</tr>
</table>';

				// Now fill the group with content
				$contentTable[$main_titles[$groups_key]] .= '<table class="default" style="width:' . $width . 'px;display: ' . $style_display . '" id="group_' . $groups_key . '">';

				// first of all order all the entries
				$groups = [];
				foreach($groups_value as $group_item_key => $group_item_value){

					$groups[$groups_key][$titles[$groups_key][$group_item_key]] = ['perm' => $group_item_key,
						'value' => $group_item_value
						];
				}

				foreach($groups as $groups_key => $group_item){
					foreach($group_item as $group_item_values){

						$group_item_key = $group_item_values['perm'];
						$group_item_value = $group_item_values['value'];

						if(($check_permissions && permissionhandler::hasPerm($group_item_key)) || !$check_permissions){
							// Display the items of the group
							$contentTable[$main_titles[$groups_key]] .= '
<tr>
	<td>' . ($parentGroups === false ? '' : '<i class="showParentPerms fa fa-' . (isset($parentGroups[$group_item_values['perm']]) ? 'check" style="color:lightgreen"' : 'close" style="color:red"') . '></i>') . '</td>
	<td style="padding:5px 0;">		' . we_html_forms::checkbox(1, ($group_item_value ? true : false), $item_names . '_Permission_' . $group_item_key, $titles[$groups_key][$group_item_key], false, "defaultfont", "top.content.setHot();") . '</td></tr>';
						}
					}
				}

				// Finish output of table
				$contentTable[$main_titles[$groups_key]] .= '</table>';
			}
		}
		//	sort the permission-groups alphabetically (perm_group_name)
		ksort($contentTable);
		foreach($contentTable as $value){
			$content .= $value;
		}
		return $content;
	}

	/**
	 * This function creates a menu with different groups that contain checkboxes.
	 * These groups can be folded and unfolded.
	 *
	 * @param      $groups                                 array
	 * @param      $main_titles                            array
	 * @param      $titles                                 array
	 * @param      $open_group                             string
	 * @param      $filter                                 array
	 * @param      $use_form                               bool
	 * @param      $form_name                              string
	 * @param      $form_group_name                        string
	 * @param      $use_with_user_module                   bool
	 * @param      $width                                  int
	 * @param      $bgcolor                                string
	 * @param      $seperator_color                        string
	 *
	 * @see        js_fold_checkbox_groups()
	 *
	 * @return     string
	 */
	function fold_multibox_groups($groups, $main_titles, $multiboxes, $open_group = "", $filter = "", $use_form = false, $form_name = "", $form_group_name = "", $use_with_user_module = false, $width = 500, $bgcolor = "#DDDDDD", $seperator_color = "#EEEEEE"){
		// Include the needed JavaScript
		$content = $this->js_fold_checkbox_groups($groups, $filter, $use_with_user_module);

		// Count the number of groups to be displayed
		$visible_groups = count($groups) - count($filter);

		// Initialize the counter for number of seperators being painted later
		$seperator_counter = 0;

		// Go through all groups to be displayed
		foreach($groups as $groups_key => $groups_value){
			// Filter out groups not to be shown
			$count_filters = count($filter);
			$show_group = true;

			// Check, if current group is in groups not to be shown
			for($i = 0; $i < $count_filters; $i++){
				if(isset($filter[$i])){
					if($groups_key == $filter[$i]){
						$show_group = false;
					}
				}
			}

			// Now only show the group if it was not found in the groups not to be shown
			if($show_group){

				// Set variable for painting of seperator
				$seperator_counter++;

				// Set title of group
				$checkbox_title = $main_titles[$groups_key];

				//	the different permission-groups shall be sorted alphabetically
				//	therefore the content is first saved in an array.
				// Build header of group
				$contentTable[$main_titles[$groups_key]] = '
					<table class="default" style="width:' . $width . 'px;">';

				$seperator_color = $seperator_color;

				// Output the seperator
				$contentTable[$main_titles[$groups_key]] .= '
					<tr><td colspan="2" style="border-bottom:10px solid ' . $seperator_color . ';"></td></tr>';

				// Continue building header of group
				$contentTable[$main_titles[$groups_key]] .= '
					<tr style="vertical-align:middle;background-color:' . $bgcolor . ';line-height:24px;">
						<td style="width:30px;padding-left:5px;">
							<a href="javascript:toggle(\'' . $groups_key . '\', \'show_single\', \'' . $use_form . '\', \'' . $form_name . '\', \'' . $form_group_name . '\');" name="arrow_link' . $groups_key . '">';

				// If a group is open display it unfolded
				$show_open = false;

				// Check, if current group is in groups to be shown opened
				if($groups_key == $open_group){
					$show_open = true;
				}

				if($show_open){
					// Define various values for expanded groups
					$arrow_image = 'fa fa-lg fa-caret-down fa-fw';
					$arrow_hint = $this->arrow_hint_opened;
					$style_display = "block";
				} else {
					// Define various values for folded groups
					$arrow_image = 'fa fa-lg fa-caret-right fa-fw';
					$arrow_hint = $this->arrow_hint_closed;
					$style_display = "none";
				}

				// Build header for open group
				$contentTable[$main_titles[$groups_key]] .= '
			<i class="' . $arrow_image . '" title="' . $arrow_hint . '" name="arrow_' . $groups_key . '"></i></a></td>
		<td class="defaultfont" colspan="3">
			<label for="arrow_link_' . $groups_key . '" style="cursor: pointer;" onclick="toggle(\'' . $groups_key . '\', \'show_single\', \'' . $use_form . '\', \'' . $form_name . '\', \'' . $form_group_name . '\');"><b>' . $checkbox_title . '</b></label></td>
	</tr>
	<tr>
	<td colspan="2" style="border-bottom:10px solid ' . $bgcolor . '">
	</tr>
</table>';

				// Now fill the group with content
				$contentTable[$main_titles[$groups_key]] .= '<table class="default" style="width:' . $width . 'px;margin:10px 30px 0 0;display: ' . $style_display . '" id="group_' . $groups_key . '">';

				// Go through all items of the group
				foreach($multiboxes[$groups_key] as $i => $c){

					if(!isset($c["headline"])){
						$c["headline"] = '';
					}
					$contentTable[$main_titles[$groups_key]] .= '
<tr>
	<td style="text-align:left;vertical-align:top;padding-bottom:15px;"><span  id="headline_' . $i . '" class="weMultiIconBoxHeadline">' . $c["headline"] . '</span></td>
	<td class="defaultfont">' . $c["html"] . '</td>
</tr>';
					if($i < (count($multiboxes[$groups_key]) - 1) && (!isset($c["noline"]))){
						$contentTable[$main_titles[$groups_key]] .= '<tr><td colspan="2"><div style="border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;clear:both;"></div></td></tr>';
					}
				}
				$contentTable[$main_titles[$groups_key]] .= '</table>';
			}
		}
		//	sort the permission-groups alphabetically (perm_group_name)
		ksort($contentTable);
		foreach($contentTable as $value){
			$content .= $value;
		}
		return $content;
	}

}
