<?php

/**
 * webEdition CMS
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
class we_wysiwyg_ToolbarButton extends we_wysiwyg_ToolbarElement{

	var $classname = __CLASS__;
	var $tooltiptext = '';
	var $imgSrc = '';

	function __construct($editor, $cmd, $width = 22, $height = 22){
		parent::__construct($editor, $cmd, $width, $height);
		$this->showMeInContextmenu = $this->hasProp('', self::SHOW_IN_CONTEXTMENU);
		if(IS_TINYMCE_4){
			$this->showMeInMenu = $this->hasProp('', self::SHOW_IN_MENU);
		}
	}

	function hasProp($cmd = '', $showWhere = self::SHOW_IN_TOOLBAR){
		//FIXME use of a structred array may be used for help to.
		/* $commands = array(
		  'table' => array(
		  'plugin' => 'table',
		  'commands' => array(
		  "inserttable",
		  "editcell",
		  "insertcolumnright",
		  "insertcolumnleft",
		  "insertrowabove",
		  "insertrowbelow",
		  "deleterow",
		  "deletecol",
		  "increasecolspan",
		  "decreasecolspan"
		  )
		  )
		  ); */
		switch($this->cmd){
			case 'fontname':
			case 'fontsize':
				return parent::hasProp('', $showWhere) || parent::hasProp('font', $showWhere);
			case 'formatblock':
			case 'applystyle':
			case 'bold':
			case 'italic':
			case 'underline':
			case 'subscript':
			case 'superscript':
			case 'strikethrough':
			case 'removetags':
			case 'removeformat':
				return parent::hasProp('', $showWhere) || parent::hasProp('prop', $showWhere);
			case 'styleprops':
				if(IS_TINYMCE_4){
					return false;
				}
				return $this->editor->setPlugin('style', parent::hasProp('', $showWhere) || parent::hasProp('prop', $showWhere));
			case 'abbr':
			case 'acronym':
			case 'lang':
				return parent::hasProp('', $showWhere) || parent::hasProp('xhtmlxtras', $showWhere);
			case 'del':
			case 'ins':
			case 'cite' :
				return $this->editor->setPlugin('xhtmlxtras', parent::hasProp('', $showWhere) || parent::hasProp('xhtmlxtras', $showWhere));
			case 'ltr':
			case 'rtl':
				return $this->editor->setPlugin('directionality', parent::hasProp('', $showWhere) || parent::hasProp('xhtmlxtras', $showWhere));
			case 'forecolor':
			case 'backcolor':
				if(IS_TINYMCE_4){
					$this->editor->setPlugin('colorpicker', parent::hasProp('', $showWhere) || parent::hasProp('color', $showWhere));
					return $this->editor->setPlugin('textcolor', parent::hasProp('', $showWhere) || parent::hasProp('color', $showWhere));
				}
				return parent::hasProp('', $showWhere) || parent::hasProp('color', $showWhere);
			case 'justifyleft':
			case 'justifycenter':
			case 'justifyright':
			case 'justifyfull':
				return parent::hasProp('', $showWhere) || parent::hasProp('justify', $showWhere);
			case 'insertunorderedlist':
			case 'insertorderedlist':
			case 'indent':
			case 'outdent':
				$this->editor->setPlugin('lists', parent::hasProp('', $showWhere) || parent::hasProp('list', $showWhere));
				return $this->editor->setPlugin('advlist', parent::hasProp('', $showWhere) || parent::hasProp('list', $showWhere));
			case 'blockquote':
				return parent::hasProp('', $showWhere) || parent::hasProp('list', $showWhere);
			case 'anchor':
			case 'createlink':
			case 'unlink':
				return parent::hasProp('', $showWhere) || parent::hasProp('link', $showWhere);
			case 'insertimage':
			case 'hr':
			case 'inserthorizontalrule':
			case 'insertspecialchar':
			case 'insertbreak':
				return parent::hasProp('', $showWhere) || parent::hasProp('insert', $showWhere);
			case 'insertgallery':
				return (parent::hasProp('', $showWhere) || parent::hasProp('insert', $showWhere))
					&& !$this->editor->getIsFrontend() && we_base_moduleInfo::isActive(we_base_moduleInfo::COLLECTION);
			case 'insertdate':
			case 'inserttime':
				return $this->editor->setPlugin('insertdatetime', parent::hasProp('', $showWhere) || parent::hasProp('insert', $showWhere));
			case 'inserttable':
			case 'edittable':
			case 'deletetable':
				if(IS_TINYMCE_4){
					if(parent::hasProp() || parent::hasProp('table', $showWhere)){
						if($showWhere === self::SHOW_IN_MENU){
							$this->editor->addTableMenuCommand($this->cmd);
							return true;
						}
						if(!$showWhere){
							$this->editor->addTableCommand($this->cmd);
							return false;
						}
						$this->editor->setPlugin('wetable', true);
					}
					return false;
				} else {
					return parent::hasProp('', $showWhere) || parent::hasProp('table', $showWhere);
				}
			case 'editcell':
			case 'increasecolspan':
			case 'decreasecolspan':
				if(IS_TINYMCE_4){
					if(parent::hasProp() || parent::hasProp('table', $showWhere)){
						$this->editor->setPlugin('wetable', true);
						if($showWhere === self::SHOW_IN_MENU){
							$this->editor->addTableMenuCommand($this->cmd);
							$this->editor->addTableMenuCommand('tablemenucell');
							return false;
						}
						if(!$showWhere){
							$this->editor->addTableCommand($this->cmd);
							return false;
						}
					}
					return false;
				} else {
					return parent::hasProp('', $showWhere) || parent::hasProp('table', $showWhere);
				}
			case 'insertcolumnright':
			case 'insertcolumnleft':
			case 'deletecol':
				if(IS_TINYMCE_4){
					if(parent::hasProp() || parent::hasProp('table', $showWhere)){
						$this->editor->setPlugin('wetable', true);
						if($showWhere === self::SHOW_IN_MENU){
							$this->editor->addTableMenuCommand($this->cmd);
							$this->editor->addTableMenuCommand('tablemenucolumn');
							return false;
						}
						if(!$showWhere){
							$this->editor->addTableCommand($this->cmd);
							return false;
						}
					}
					return false;
				} else {
					return parent::hasProp('', $showWhere) || parent::hasProp('table', $showWhere);
				}
			case 'insertrowabove':
			case 'insertrowbelow':
			case 'deleterow':
			case 'editrow':
				if(IS_TINYMCE_4){
					if(parent::hasProp() || parent::hasProp('table', $showWhere)){
						$this->editor->setPlugin('wetable', true);
						if($showWhere === self::SHOW_IN_MENU){
							$this->editor->addTableMenuCommand($this->cmd);
							$this->editor->addTableMenuCommand('tablemenurow');
							return false;
						}
						if(!$showWhere){
							$this->editor->addTableCommand($this->cmd);
							return false;
						}
					}
					return false;
				} else {
					return parent::hasProp('', $showWhere) || parent::hasProp('table', $showWhere);
				}
			case 'tablemenucell':
			case 'tablemenucolumn':
			case 'tablemenurow':
				return (IS_TINYMCE_4 && $showWhere === self::SHOW_IN_MENU && $this->editor->isTableMenuCommand($this->cmd));
			case 'table_placeholder': // obsolete?
				if(IS_TINYMCE_4 && $this->editor->isTableCommands()){
					return true;
				}
				return false;
			case 'cut':
			case 'copy':
			case 'paste':
				return false;
			case 'pastetext':
			case 'pasteword':
				return parent::hasProp('', $showWhere) || parent::hasProp('copypaste', $showWhere);
			case 'absolute':
			case 'insertlayer':
			case 'movebackward':
			case 'moveforward':
				if(IS_TINYMCE_4 && $this->editor->isTableCommands()){
					return false;
				}
				return $this->editor->setPlugin('layer', parent::hasProp('', $showWhere) || parent::hasProp('layer', $showWhere));
			case 'undo':
			case 'redo':
			case 'spellcheck':
			case 'visibleborders':
				return parent::hasProp('', $showWhere) || parent::hasProp('essential', $showWhere);
			case 'selectall':
				if(IS_TINYMCE_4 && $showWhere === self::SHOW_IN_TOOLBAR){ // icon missing in tiny4
					return false;
				}
				return parent::hasProp('', $showWhere) || parent::hasProp('essential', $showWhere);
			case 'search':
			case 'replace':
				return $this->editor->setPlugin('searchreplace', parent::hasProp('', $showWhere) || parent::hasProp('essential', $showWhere));
			case 'fullscreen':
				return !$this->editor->getIsFrontend() && (parent::hasProp('', $showWhere) || parent::hasProp('essential', $showWhere));
			case 'maximize':
				if(IS_TINYMCE_4){
					return (parent::hasProp('', $showWhere) || parent::hasProp('essential', $showWhere));
				}
				return false;
			case 'editsource':
				return parent::hasProp('', $showWhere) || parent::hasProp('advanced', $showWhere);
			case 'template':
				return parent::hasProp('', $showWhere) || parent::hasProp('advanced', $showWhere);
			case 'codesample':
				if(IS_TINYMCE_4){
					return (parent::hasProp('', $showWhere) || parent::hasProp('advanced', $showWhere));
				}
				return false;
			case 'fontname':
			case 'fontsize':
				return parent::hasProp('', $showWhere) || parent::hasProp('font', $showWhere);
			case 'formatblock':
			case 'applystyle':
				return parent::hasProp('', $showWhere) || parent::hasProp('prop', $showWhere);
			default:
				//FIXME: find the command using this case!
				return false;
		}
	}

}
