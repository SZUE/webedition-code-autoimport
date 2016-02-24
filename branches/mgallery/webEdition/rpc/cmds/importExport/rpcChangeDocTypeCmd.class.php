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

				$paths_arr = id_to_path($values['Templates'], TEMPLATES_TABLE, null, false, true);
				$TPLselect = new we_html_select(array(
					'name' => 'docTypeTemplateId',
					'size' => 1,
					'class' => 'weSelect',
					'onclick' => (defined('OBJECT_TABLE')) ? "self.document.forms['we_form'].elements['v[import_type]'][0].checked=true;" : '',
					//"onchange"  => "we_submit_form(self.document.forms['we_form'], 'wizbody', '".$this->path."');",
					'style' => 'width: 300px')
				);
				$optid = 0;
				foreach($paths_arr as $templateID => $path){
					$TPLselect->insertOption($optid, $templateID, $path);
					$optid++;
				}
				$templateElement = we_html_tools::htmlFormElementTable($TPLselect->getHTML(), g_l('import', '[template]'), 'left', 'defaultfont');
				$categories = ($values['Category'] ? $categories : $this->getCategories('doc', $values['Category'], 'v[docCategories]'));

				$categories = strtr($categories, array("\r" => "", "\n" => ""));
				if($ids_arr){
					$_docTypeLayerDisplay = 'block';
					$_noDocTypeLayerDisplay = 'none';
				} else {
					$_docTypeLayerDisplay = 'none';
					$_noDocTypeLayerDisplay = 'block';
				}
				$_templateName = '';
				if(!empty($values['TemplateID'])){
					$_templateName = f('SELECT Path FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($values['TemplateID']));
				}
				$resp->setData('elements', array(
					"self.document.we_form.elements['v[store_to_id]']" => array("value" => $values["ParentID"] | 0),
					"self.document.we_form.elements['v[store_to_path]']" => array("value" => $values["ParentPath"] | "/"),
					"self.document.we_form.elements['v[we_TemplateID]']" => array("value" => $values["TemplateID"] | 0),
					"self.document.we_form.elements['v[we_TemplateName]']" => array("value" => $_templateName | ""),
					"self.document.we_form.elements['v[we_Extension]']" => array("value" => $values["Extension"] | ""),
					"self.document.we_form.elements['v[is_dynamic]']" => array("value" => $values["IsDynamic"] | 0),
					"self.document.we_form.elements['chbxIsDynamic']" => array("checked" => $values["IsDynamic"] | 0),
					"self.document.we_form.elements['v[docCategories]']" => array("value" => $values["Category"] | ""),
					"self.document.we_form.elements.noDocTypeTemplateId" => array("value" => 0),
					"document.getElementById('docTypeLayer')" => array("innerHTML" => addslashes($templateElement), "style.display" => $_docTypeLayerDisplay),
					"document.getElementById('noDocTypeLayer')" => array("style.display" => $_noDocTypeLayerDisplay),
					"document.getElementById('docCatTable')" => array("innerHTML" => addslashes($categories)
					)
					)
				);
			} else {
				$resp->setData("elements", array(
					"self.document.we_form.elements['v[store_to_id]']" => array("value" => 0),
					"self.document.we_form.elements['v[store_to_path]']" => array("value" => "/"),
					"self.document.we_form.elements['v[we_TemplateID]']" => array("value" => 0),
					"self.document.we_form.elements['v[we_TemplateName]']" => array("value" => "/"),
					"self.document.we_form.elements['v[we_Extension]']" => array("value" => ""),
					"self.document.we_form.elements['v[is_dynamic]']" => array("value" => 0),
					"self.document.we_form.elements['chbxIsDynamic']" => array("checked" => 0),
					"self.document.we_form.elements['v[docCategories]']" => array("value" => ""),
					"self.document.we_form.elements.noDocTypeTemplateId" => array("value" => 0),
					"document.getElementById('docTypeLayer')" => array("innerHTML" => "", "style.display" => "none"),
					"document.getElementById('noDocTypeLayer')" => array("style.display" => "block"),
					"document.getElementById('docCatTable')" => array("innerHTML" => $categories
					)
					)
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
