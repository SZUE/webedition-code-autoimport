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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

    abstract class validation{
			static function getAllCategories(){
            $cats = array(
                'xhtml'=>g_l('validation','[category_xhtml]'),
                'links'=>g_l('validation','[category_links]'),
                'css'=>g_l('validation','[category_css]'),
                'accessibility'=>g_l('validation','[category_accessibility]')
                );
            return $cats;
        }

        static function saveService($validationService){
            // before saving check if another validationservice has this name
			$checkNameQuery = '
				SELECT *
				FROM ' . VALIDATION_SERVICES_TABLE . '
				WHERE name="' . $DB_WE->escape($validationService->name) . '"
					AND PK_tblvalidationservices != ' . intval($validationService->id) . '
				';

			$GLOBALS['DB_WE']->query($checkNameQuery);
			if ($GLOBALS['DB_WE']->num_rows()) {

				$GLOBALS['errorMessage'] = g_l('validation','[edit_service][servicename_already_exists]');
				return false;
			}


            if($validationService->id != 0){
                $query = '
                    UPDATE ' . VALIDATION_SERVICES_TABLE . '
                        SET category="' . $DB_WE->escape($validationService->category). '",name="' . $DB_WE->escape($validationService->name) . '",host="' . $DB_WE->escape($validationService->host) . '",path="' . $DB_WE->escape($validationService->path) . '",method="' . $DB_WE->escape($validationService->method) . '",varname="' . $DB_WE->escape($validationService->varname) . '",checkvia="' . $DB_WE->escape($validationService->checkvia) . '",additionalVars="' . $DB_WE->escape($validationService->additionalVars) . '",ctype="' . $DB_WE->escape($validationService->ctype) . '",fileEndings="' . $DB_WE->escape($validationService->fileEndings) . '",active="' . $DB_WE->escape($validationService->active) . '"
                        WHERE PK_tblvalidationservices = ' . intval($validationService->id);
            } else {

                $query = '
                    INSERT INTO ' . VALIDATION_SERVICES_TABLE . '
                        (category, name, host, path, method, varname, checkvia, additionalVars, ctype, fileEndings, active)
                        VALUES("' . $DB_WE->escape($validationService->category) . '", "' . $DB_WE->escape($validationService->name) . '", "' . $DB_WE->escape($validationService->host) . '", "' . $DB_WE->escape($validationService->path) . '", "' . $DB_WE->escape($validationService->method) . '", "' . $DB_WE->escape($validationService->varname) . '", "' . $DB_WE->escape($validationService->checkvia) . '", "' . $DB_WE->escape($validationService->additionalVars) . '", "' . $DB_WE->escape($validationService->ctype) . '", "' . $DB_WE->escape($validationService->fileEndings) . '", "' . $DB_WE->escape($validationService->active) . '");
                ';
            }

            if($GLOBALS['DB_WE']->query($query)){
                if($validationService->id == 0){
                    $id = f("SELECT LAST_INSERT_ID() as LastID FROM " . VALIDATION_SERVICES_TABLE,"LastID",$GLOBALS['DB_WE']);
                    $validationService->id = $id;
                }
                return $validationService;
            } else {
                return false;
            }
        }

        static function deleteService($validationService){
            if($validationService->id != 0){
                $query = 'DELETE FROM ' . VALIDATION_SERVICES_TABLE . ' WHERE PK_tblvalidationservices = ' . intval($validationService->id);

                if($GLOBALS['DB_WE']->query($query)){
                    return true;
                }
            } else {
                //  not saved entry - must not be deleted from db
                return true;
            }
            return false;
        }

        static function getValidationServices($mode='edit'){
            $_ret = array();

            switch($mode){
                case 'edit':
                    $query = 'SELECT * FROM ' . VALIDATION_SERVICES_TABLE;
                    break;
                case 'use':
                    $query = 'SELECT * FROM ' . VALIDATION_SERVICES_TABLE . ' WHERE fileEndings LIKE "%' . $GLOBALS['DB_WE']->escape($GLOBALS['we_doc']->Extension) . '%" AND active=1';
                    break;
            }

            $GLOBALS['DB_WE']->query($query);
            while($GLOBALS['DB_WE']->next_record()){
                $_ret[] = new validationService($GLOBALS['DB_WE']->f('PK_tblvalidationservices'),'custom',$GLOBALS['DB_WE']->f('category'),$GLOBALS['DB_WE']->f('name'),$GLOBALS['DB_WE']->f('host'),$GLOBALS['DB_WE']->f('path'),$GLOBALS['DB_WE']->f('method'),$GLOBALS['DB_WE']->f('varname'),$GLOBALS['DB_WE']->f('checkvia'),$GLOBALS['DB_WE']->f('ctype'),$GLOBALS['DB_WE']->f('additionalVars'),$GLOBALS['DB_WE']->f('fileEndings'),$GLOBALS['DB_WE']->f('active'));
            }
            return $_ret;
        }
    }
