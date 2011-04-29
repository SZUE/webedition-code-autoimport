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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


    include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language/' . $GLOBALS['WE_LANGUAGE'] . '/accessibility.inc.php');

    class validation{

        function validation(){
            die('static class do not use constructor');
        }

        function getAllCategories(){

            global $l_validation;

            $cats = array(
                'xhtml'=>$l_validation['category_xhtml'],
                'links'=>$l_validation['category_links'],
                'css'=>$l_validation['category_css'],
                'accessibility'=>$l_validation['category_accessibility']
                );
            return $cats;
        }

        function saveService($validationService){

            global $DB_WE;

            // before saving check if another validationservice has this name
			$checkNameQuery = '
				SELECT *
				FROM ' . VALIDATION_SERVICES_TABLE . '
				WHERE name="' . $DB_WE->escape($validationService->name) . '"
					AND PK_tblvalidationservices != ' . abs($validationService->id) . '
				';

			$DB_WE->query($checkNameQuery);
			if ($DB_WE->num_rows()) {

				$GLOBALS['errorMessage'] = $GLOBALS['l_validation']['edit_service']['servicename_already_exists'];
				return false;
			}


            if($validationService->id != 0){
                $query = '
                    UPDATE ' . VALIDATION_SERVICES_TABLE . '
                        SET category="' . $DB_WE->escape($validationService->category). '",name="' . $DB_WE->escape($validationService->name) . '",host="' . $DB_WE->escape($validationService->host) . '",path="' . $DB_WE->escape($validationService->path) . '",method="' . $DB_WE->escape($validationService->method) . '",varname="' . $DB_WE->escape($validationService->varname) . '",checkvia="' . $DB_WE->escape($validationService->checkvia) . '",additionalVars="' . $DB_WE->escape($validationService->additionalVars) . '",ctype="' . $DB_WE->escape($validationService->ctype) . '",fileEndings="' . $DB_WE->escape($validationService->fileEndings) . '",active="' . $DB_WE->escape($validationService->active) . '"
                        WHERE PK_tblvalidationservices = ' . abs($validationService->id);
            } else {

                $query = '
                    INSERT INTO ' . VALIDATION_SERVICES_TABLE . '
                        (category, name, host, path, method, varname, checkvia, additionalVars, ctype, fileEndings, active)
                        VALUES("' . $DB_WE->escape($validationService->category) . '", "' . $DB_WE->escape($validationService->name) . '", "' . $DB_WE->escape($validationService->host) . '", "' . $DB_WE->escape($validationService->path) . '", "' . $DB_WE->escape($validationService->method) . '", "' . $DB_WE->escape($validationService->varname) . '", "' . $DB_WE->escape($validationService->checkvia) . '", "' . $DB_WE->escape($validationService->additionalVars) . '", "' . $DB_WE->escape($validationService->ctype) . '", "' . $DB_WE->escape($validationService->fileEndings) . '", "' . $DB_WE->escape($validationService->active) . '");
                ';
            }

            if($DB_WE->query($query)){
                if($validationService->id == 0){
                    $id = f("SELECT LAST_INSERT_ID() as LastID FROM " . VALIDATION_SERVICES_TABLE,"LastID",$DB_WE);
                    $validationService->id = $id;
                }
                return $validationService;
            } else {
                return false;
            }
        }

        function deleteService($validationService){

            global $DB_WE;

            if($validationService->id != 0){
                $query = '
                    DELETE FROM ' . VALIDATION_SERVICES_TABLE . '
                        WHERE PK_tblvalidationservices = ' . abs($validationService->id);

                if($DB_WE->query($query)){
                    return true;
                }
            } else {
                //  not saved entry - must not be deleted from db
                return true;
            }
            return false;
        }

        function getValidationServices($mode='edit'){

            global $DB_WE,$we_doc;

            $_ret = array();

            switch($mode){

                case 'edit':
                    $query = '
                        SELECT *
                        FROM ' . VALIDATION_SERVICES_TABLE;
                    break;
                case 'use':
                    $query = '
                        SELECT *
                        FROM ' . VALIDATION_SERVICES_TABLE . '
                        WHERE fileEndings LIKE "%' . $DB_WE->escape($we_doc->Extension) . '%" AND active=1';
                    break;
            }

            $DB_WE->query($query);
            while($DB_WE->next_record()){
                $_ret[] = new validationService($DB_WE->f('PK_tblvalidationservices'),'custom',$DB_WE->f('category'),$DB_WE->f('name'),$DB_WE->f('host'),$DB_WE->f('path'),$DB_WE->f('method'),$DB_WE->f('varname'),$DB_WE->f('checkvia'),$DB_WE->f('ctype'),$DB_WE->f('additionalVars'),$DB_WE->f('fileEndings'),$DB_WE->f('active'));
            }
            return $_ret;
        }
    }
