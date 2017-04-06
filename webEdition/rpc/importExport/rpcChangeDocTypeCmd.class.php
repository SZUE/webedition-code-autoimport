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
class rpcChangeDocTypeCmd extends we_rpc_cmd{

	function execute(){
		$resp = new we_rpc_response();
		$categories = '<tr><td style="font-size:8px">&nbsp;</td></tr>';
		if(($dt = we_base_request::_(we_base_request::INT, 'docType')) !== false){
			if($dt >= 0){
				$values = getHash('SELECT dt.*,dtf.Path FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID WHERE dt.ID=' . $dt);

				$ids_arr = makeArrayFromCSV($values['Templates']);

				$paths_arr = id_to_path($values['Templates'], TEMPLATES_TABLE, null, true);
				$TPLselect = new we_html_select([
					'name' => 'docTypeTemplateId',
					'class' => 'weSelect',
					'onclick' => (defined('OBJECT_TABLE')) ? "self.document.forms['we_form'].elements['v[import_type]'][0].checked=true;" : '',
					//"onchange"  => "we_submit_form(self.document.forms['we_form'], 'wizbody', '".$this->path."');",
					'style' => 'width: 300px']
				);
				$optid = 0;
				foreach($paths_arr as $templateID => $path){
					$TPLselect->insertOption($optid, $templateID, $path);
					$optid++;
				}
				$templateElement = we_html_tools::htmlFormElementTable($TPLselect->getHTML(), g_l('import', '[template]'), 'left', 'defaultfont');
				$categories = ($values['Category'] ? $categories : $this->getCategories('doc', $values['Category'], 'v[docCategories]'));

				$categories = strtr($categories, ["\r" => "", "\n" => ""]);
				if($ids_arr){
					$docTypeLayerDisplay = 'block';
					$noDocTypeLayerDisplay = 'none';
				} else {
					$docTypeLayerDisplay = 'none';
					$noDocTypeLayerDisplay = 'block';
				}
				$templateName = '';
				if(!empty($values['TemplateID'])){
					$templateName = f('SELECT Path FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($values['TemplateID']));
				}

				$resp->setData('elements', [
					['name' => 'v[store_to_id]', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => ($values["ParentID"] | 0)]]],
					['name' => 'v[store_to_path]', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => ($values["ParentPath"] | "/")]]],
					['name' => 'v[we_TemplateID]', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => ($values["TemplateID"] | 0)]]],
					['name' => 'v[we_TemplateName]', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => ($templateName | "")]]],
					['name' => 'v[we_Extension]', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => ($values["Extension"] | "")]]],
					['name' => 'v[is_dynamic]', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => ($values["IsDynamic"] | 0)]]],
					['name' => 'chbxIsDynamic', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => ($values["IsDynamic"] | 0)]]],
					['name' => 'v[docCategories]', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => ($values["Category"] | "")]]],
					['name' => 'noDocTypeTemplateId', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => 0]]],
					['name' => 'docTypeLayer', 'type' => 'node', 'props' => [['type' => 'innerHTML', 'val' => $templateElement], ['type' => 'style', 'name' => 'display', 'val' => $docTypeLayerDisplay]]],
					['name' => 'noDocTypeLayer', 'type' => 'node', 'props' => [['type' => 'style', 'name' => 'display', 'val' => $noDocTypeLayerDisplay]]],
					['name' => 'docCatTable', 'type' => 'node', 'props' => [['type' => 'innerHTML', 'val' => addslashes($categories)]]],
					]
				);
			} else {
				$resp->setData('elements', [
					['name' => 'v[store_to_id]', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => 0]]],
					['name' => 'v[store_to_path]', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => "/"]]],
					['name' => 'v[we_TemplateID]', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => 0]]],
					['name' => 'v[we_TemplateName]', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => "/"]]],
					['name' => 'v[we_Extension]', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => ""]]],
					['name' => 'v[is_dynamic]', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => 0]]],
					['name' => 'chbxIsDynamic', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => 0]]],
					['name' => 'v[docCategories]', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => ""]]],
					['name' => 'noDocTypeTemplateId', 'type' => 'formelement', 'props' => [['type' => 'attrib', 'name' => 'value', 'val' => 0]]],
					['name' => 'docTypeLayer', 'type' => 'node', 'props' => [['type' => 'innerHTML', 'val' => ""], ['type' => 'style', 'name' => 'display', 'val' => "none"]]],
					['name' => 'noDocTypeLayer', 'type' => 'node', 'props' => [['type' => 'style', 'name' => 'display', 'val' => "block"]]],
					['name' => 'docCatTable', 'type' => 'node', 'props' => [['type' => 'innerHTML', 'val' => $categories]]],
					]
				);
			}
		}
		return $resp;
	}

	private function getCategories($obj, $categories, $catField = ''){
		$cats = new we_chooser_multiDirExtended(410, $categories, 'delete_' . $obj . 'Cat', '', '', '"we/category"', CATEGORY_TABLE);
		$cats->setRowPrefix($obj);
		$cats->setCatField($catField);
		return $cats->getTableRows();
	}

}
