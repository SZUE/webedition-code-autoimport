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
 * @package    webEdition_language
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/**
 * Language file: navigation.inc.php
 * Provides language strings.
 * Language: English
 */
$l_navigation = array(
		'no_perms' => 'You do not have the permission to select this option.',
		'delete_alert' => 'Delete current entry/folder.\\n Are you sure?', // TRANSLATE
		'nothing_to_delete' => 'The entry cannot be deleted!', // TRANSLATE
		'nothing_to_save' => 'The entry cannot be saved!', // TRANSLATE
		'nothing_selected' => 'Please select the entry/folder to delete.', // TRANSLATE
		'we_filename_notValid' => 'The username is not correct!\\nAlphanumeric characters, upper case and lower case, just as low line, hyphen, dot and blank character{blank; space} (a-z, A-Z, 0-9, _,-.,) are valid', // TRANSLATE

		'menu_new' => 'New', // TRANSLATE
		'menu_save' => 'Save', // TRANSLATE
		'menu_delete' => 'Delete', // TRANSLATE
		'menu_exit' => 'Quit',
		'menu_options' => 'Options', // TRANSLATE
		'menu_generate' => 'Generate source code', // TRANSLATE

		'menu_settings' => 'Settings', // TRANSLATE
		'menu_highlight_rules' => 'Rules for Highlighting', // TRANSLATE

		'menu_info' => 'Info', // TRANSLATE
		'menu_help' => 'Help', // TRANSLATE

		'property' => 'Properties', // TRANSLATE
		'preview' => 'Preview', // TRANSLATE
		'preview_code' => 'Preview - source code', // TRANSLATE
		'navigation' => 'Navigation', // TRANSLATE
		'group' => 'Folder', // TRANSLATE
		'name' => 'Name', // TRANSLATE
		'newFolder' => 'New folder', // TRANSLATE
		'display' => 'Display', // TRANSLATE
		'save_group_ok' => 'The folder was saved.', // TRANSLATE
		'save_ok' => 'The navigation was saved.', // TRANSLATE

		'path_nok' => 'The path is not correct!', // TRANSLATE
		'name_empty' => 'The name may not be empty!', // TRANSLATE
		'name_exists' => 'The name already exists!', // TRANSLATE
		'wrongtext' => 'The name is not valid!\\nValid characters are are letters from a to z (upper case or lower case), figures, low line (_), deficit (-), dot (.), blank characters ( ) and at symbols (@). ', // TRANSLATE
		'wrongTitleField' => 'The navigation folder could not be saved, because the given title field doesn\'t  exist. Please correct the title field on the "content" tab and save again.', // TRANSLATE
		'folder_path_exists' => 'A entry/foder with this name exists allredy.', // TRANSLATE
		'navigation_deleted' => 'The entry/folder was deleted successfully.', // TRANSLATE
		'group_deleted' => 'The folder was deleted successfully.', // TRANSLATE

		'selection' => 'Selection', // TRANSLATE
		'icon' => 'Image', // TRANSLATE
		'presentation' => 'Representation', // TRANSLATE
		'text' => 'Text', // TRANSLATE
		'title' => 'Title', // TRANSLATE

		'dir' => 'Directory', // TRANSLATE
		'categories' => 'Categories', // TRANSLATE
		'stat_selection' => 'Static selection', // TRANSLATE
		'dyn_selection' => 'Dynamic selection', // TRANSLATE
		'manual_selection' => 'Manual selection', // TRANSLATE
		'txt_dyn_selection' => 'Explanation text for the dynamic selection', // TRANSLATE
		'txt_stat_selection' => 'Explanation text for the static selection. Linked to the selected document or object.', // TRANSLATE

		'sort' => 'Sorting', // TRANSLATE
		'ascending' => 'ascending', // TRANSLATE
		'descending' => 'descending', // TRANSLATE

		'show_count' => 'Number of entries to be displayed', // TRANSLATE
		'title_field' => 'Title field', // TRANSLATE
		'select_field_txt' => 'Select a field', // TRANSLATE

		'content' => 'Content', // TRANSLATE
		'no_dyn_content' => '- No dynamic contents -', // TRANSLATE
		'dyn_content' => 'The folder contains dynamic contents', // TRANSLATE
		'link' => 'Link', // TRANSLATE
		'docLink' => 'Internal document', // TRANSLATE
		'objLink' => 'Object', // TRANSLATE
		'catLink' => 'Category', // TRANSLATE
		'order' => 'Order', // TRANSLATE

		'general' => 'General', // TRANSLATE
		'entry' => 'Entry', // TRANSLATE
		'entries' => 'Entries', // TRANSLATE
		'save_populate_question' => 'You have defined the dynamic contents for the folder. After saving the document the generated entries are added resp. renewed. Would you like to proceed? ', // TRANSLATE
		'depopulate_question' => 'The dynamic contents will now be deleted. Would like you to proceed?', // TRANSLATE
		'populate_question' => 'The dynamic entries are now updated. Would you like to proceed?', // TRANSLATE
		'depopulate_msg' => 'The dynamic entries were deleted.', // TRANSLATE
		'populate_msg' => 'The dynamic entries were added.', // TRANSLATE

		'documents' => 'Documents', // TRANSLATE
		'objects' => 'Objects', // TRANSLATE
		'workspace' => 'Workspace', // TRANSLATE
		'no_workspace' => 'The object has no defined workspace! Thus, the object can not be selected!', // TRANSLATE

		'no_entry' => '--all the same--', // TRANSLATE
		'parameter' => 'Parameter', // TRANSLATE
		'urlLink' => 'External document', // TRANSLATE
		'doctype' => 'Document type', // TRANSLATE
		'class' => 'Class', // TRANSLATE

		'parameter_text' => 'In the parameter the following variables of the navigation can be used: $LinkID, FolderID, $DocTypID, $ClassID, $Ordn and $WorkspaceID', // TRANSLATE

		'intern' => 'Internal Link', // TRANSLATE
		'extern' => 'External Link', // TRANSLATE
		'linkSelection' => 'Link selection', // TRANSLATE
		'catParameter' => 'Name of the category parameter', // TRANSLATE

		'navigation_rules' => "Navigation rules", // TRANSLATE
		'available_rules' => "Available rules", // TRANSLATE
		'rule_name' => "Name of rule", // TRANSLATE
		'rule_navigation_link' => "Active navigation item", // TRANSLATE
		'rule_applies_for' => "Rule applies for", // TRANSLATE
		'rule_folder' => "In folder", // TRANSLATE
		'rule_doctype' => "Document type", // TRANSLATE
		'rule_categories' => "Categories", // TRANSLATE
		'rule_class' => "Of class", // TRANSLATE
		'rule_workspace' => "Workspace", // TRANSLATE
		'invalid_name' => "The name may consist only of letter, figures, hyphen and unterscore", // TRANSLATE
		'name_exists' => "The name \"%s\" already exists, please enter another name.", // TRANSLATE
		'saved_successful' => "The entry: \"%s\" was saved.",
		'exit_doc_question' => 'It seems, as if you have changed the navigation.<br>Do you want to save your changes?', // TRANSLATE
		'add_navigation' => 'Add navigation', // TRANSLATE
		'begin' => 'at the beginning', // TRANSLATE
		'end' => 'at the end', // TRANSLATE

		'del_question' => 'The entry will be deleted definitely. Are you sure?', // TRANSLATE
		'dellall_question' => 'All entries will be deleted definitely. Are you sure?', // TRANSLATE
		'charset' => 'Character coding', // TRANSLATE

		'more_attributes' => 'More properties', // TRANSLATE
		'less_attributes' => 'Less properties', // TRANSLATE
		'attributes' => 'Attributes', // TRANSLATE
		'title' => 'Title', // TRANSLATE
		'anchor' => 'Anchor', // TRANSLATE
		'language' => 'Language', // TRANSLATE
		'target' => 'Target', // TRANSLATE
		'link_language' => 'Link', // TRANSLATE
		'href_language' => 'Linked document', // TRANSLATE
		'keyboard' => 'Keyboard', // TRANSLATE
		'accesskey' => 'Accesskey', // TRANSLATE
		'tabindex' => 'Tabindex', // TRANSLATE
		'relation' => 'Relation', // TRANSLATE
		'link_attribute' => 'Link attributes', // TRANSLATE
		'popup' => 'Popup window', // TRANSLATE
		'popup_open' => 'Open', // TRANSLATE
		'popup_center' => 'Center', // TRANSLATE
		'popup_x' => 'x position', // TRANSLATE
		'popup_y' => 'y position', // TRANSLATE
		'popup_width' => 'Width', // TRANSLATE
		'popup_height' => 'Height', // TRANSLATE
		'popup_status' => 'Status', // TRANSLATE
		'popup_scrollbars' => 'Scrollbars', // TRANSLATE
		'popup_menubar' => 'Menubar', // TRANSLATE
		'popup_resizable' => 'Resizable', // TRANSLATE
		'popup_location' => 'Location', // TRANSLATE
		'popup_toolbar' => 'Toolbar', // TRANSLATE

		'icon_properties' => 'Image properties', // TRANSLATE
		'icon_properties_out' => 'Hide image properties', // TRANSLATE
		'icon_width' => 'Width', // TRANSLATE
		'icon_height' => 'Heigt', // TRANSLATE
		'icon_border' => 'Border', // TRANSLATE
		'icon_align' => 'Align', // TRANSLATE
		'icon_hspace' => 'horiz. space', // TRANSLATE
		'icon_vspace' => 'vert. space', // TRANSLATE
		'icon_alt' => 'Alt text', // TRANSLATE
		'icon_title' => 'Title', // TRANSLATE

		'linkprops_desc' => 'Here you can define the additional link properties. In dynamic items only link target and popup window properties will be applied.', // TRANSLATE
		'charset_desc' => 'The selected charset will be applyed on the current folder and all folder entries.', // TRANSLATE


		'customers' => 'Customers',
		'limit_access' => 'Define customer access level', // TRANSLATE
		'customer_access' => 'All customers can access the item', // TRANSLATE
		'filter' => 'Define filter', // TRANSLATE
		'and' => 'and', // TRANSLATE
		'or' => 'or', // TRANSLATE
		'selected_customers' => 'Only folowing customers can access the item', // TRANSLATE
		'useDocumentFilter' => 'Use filter settings of document/object', // TRANSLATE
		'reset_customer_filter' => 'Reset all customer filters', // TRANSLATE
		'reset_customerfilter_done_message' => 'The customer filters were successfully reset!', // TRANSLATE
		'reset_customerfilter_question' => 'Do you realy want to reset all customer filters', // TRANSLATE

		'NoDeleteFromDocument' => "Navigation entry with subentries, can be edited from here, but deletion has to be done in the navigation tool.", // TRANSLATE
		'current_on_urlpar' => "Take into account at highlighting", // TRANSLATE
		'current_on_anker' => "Take into account at highlighting (using add. URL-Par. we_anchor)", // TRANSLATE
);