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
class we_chooser_multiFile extends we_chooser_multiDir{
	private $disabledDelItems = [];
	private $disabledDelReason = '';
	private $iconFile;
	private $iconFolder;

	public function __construct($width, $ids, $cmd_del, $addbut, $cmd_edit, $iconFolder = 'folder', $iconFile = 'application/*'){
		parent::__construct($width, $ids, $cmd_del, $addbut);
		$this->iconFile = $iconFile;
		$this->iconFolder = $iconFolder;
		$this->cmd_edit = $cmd_edit;
	}

	public function setDisabledDelItems(array $items, $reason){
		$this->disabledDelItems = $items;
		$this->disabledDelReason = $reason;
	}

	function get(){
		$table = new we_html_table(['class' => 'default', "width" => abs($this->width - 20)], 1, 4);

		$this->nr = 0;
		$idArr = (is_array($this->ids) ? $this->ids : array_filter(explode(',', trim($this->ids, ','))));
		$c = 1;

		if($idArr){
			foreach($idArr as $id){
				$table->addRow();

				$edit = null;
				$trash = null;

				if($this->isEditable && $this->cmd_edit){
					$edit = we_html_button::create_button(we_html_button::EDIT, 'javascript:' . $this->getJsSetHot() . ";we_cmd('" . $this->cmd_edit . "','" . $id . "');");
				}

				if(($this->isEditable && $this->cmd_del) || $this->CanDelete){

					if($this->disabledDelItems){
						$DisArr = $this->disabledDelItems;
						if(in_array($id, $DisArr)){
							$trash = we_html_button::create_button(we_html_button::TRASH, 'javascript:' . $this->getJsSetHot() . ($this->extraDelFn ? : "") . ";we_cmd('" . $this->cmd_del . "','" . $id . "');", true, 100, 22, "", "", true);

							$table->setCol($c, 0, ["title" => $this->disabledDelReason, 'class' => 'chooserFileIcon', 'data-contenttype' => (@is_dir($id) ? $this->iconFolder : $this->iconFile)], '');
							$table->setCol($c, 1, ['class' => $this->css, "title" => $this->disabledDelReason], $id);
						} else {
							$trash = we_html_button::create_button(we_html_button::TRASH, 'javascript:' . $this->getJsSetHot() . ($this->extraDelFn ? : "") . ";we_cmd('" . $this->cmd_del . "','" . $id . "');");

							$table->setCol($c, 0, ['class' => 'chooserFileIcon', 'data-contenttype' => (@is_dir($id) ? $this->iconFolder : $this->iconFile)], '');
							$table->setCol($c, 1, ['class' => $this->css], $id);
						}
					} else {
						$trash = we_html_button::create_button(we_html_button::TRASH, 'javascript:' . $this->getJsSetHot() . ($this->extraDelFn ? : "") . ";we_cmd('" . $this->cmd_del . "','" . $id . "');");

						$table->setCol($c, 0, ['class' => 'chooserFileIcon', 'data-contenttype' => (@is_dir($id) ? $this->iconFolder : $this->iconFile)], '');
						$table->setCol($c, 1, ['class' => $this->css], $id);
					}
				} else {
					$trash = '';

					$table->setCol($c, 0, ['class' => 'chooserFileIcon', 'data-contenttype' => (@is_dir($id) ? $this->iconFolder : $this->iconFile)], '');
					$table->setCol($c, 1, ['class' => $this->css], $id);
				}

				$table->setCol($c, 2, ['style' => 'text-align:right'], $edit . $trash);

				$c++;
			}
		}


		$table2 = new we_html_table(['class' => 'default', 'width' => $this->width], 1, 1);

		$table2->setCol(0, 0, [], we_html_element::htmlDiv(['style' => 'background-color:white;', 'class' => "multichooser", 'id' => 'multi_selector'], $table->getHtml()));

		if($this->addbut){
			$table2->addRow(1);
			$table2->setCol(1, 0, ['style' => "text-align:right;padding-top:2px;"], $this->addbut);
		}

		return $table2->getHtml() . we_html_element::jsElement('WE().util.setIconOfDocClass(document,"chooserFileIcon");');
	}

}
