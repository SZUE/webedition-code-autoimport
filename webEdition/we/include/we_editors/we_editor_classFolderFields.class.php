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
class we_editor_classFolderFields extends we_editor_base{

	public function show(){
		switch(we_base_request::_(we_base_request::STRING, 'do')){
			case 'delete':
				$this->we_doc->deleteObjects($this->jsCmd);
				break;
			case 'unpublish':
				$this->we_doc->publishObjects($this->jsCmd, false);
				break;
			case 'publish':
				$this->we_doc->publishObjects($this->jsCmd);
				break;
			case 'unsearchable':
				$this->we_doc->setObjectProperty('IsSearchable', false);
				break;
			case 'searchable':
				$this->we_doc->setObjectProperty('IsSearchable', true);
				break;
			case 'copychar':
				$this->we_doc->setObjectProperty('Charset');
				break;
			case 'copyws':
				$this->we_doc->setObjectProperty('Workspaces');
				break;
			case 'copytid':
				$this->we_doc->setObjectProperty('TriggerID');
				break;
		}

		return $this->getPage(we_html_multiIconBox::getHTML('', [
							['html' => $this->we_doc->getSearchDialog()],
							['html' => $this->we_doc->getSearch()],
								]
								, 30), $this->we_doc->getSearchJS() .
						we_editor_script::get()
		);
	}

}
