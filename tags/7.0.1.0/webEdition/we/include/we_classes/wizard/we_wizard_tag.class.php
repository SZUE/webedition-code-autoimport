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
abstract class we_wizard_tag{

	static function getExistingWeTags($useDeprecated = true){
		$retTags = array();
		$main = self::getMainTagModules($useDeprecated);
		foreach($main as $modulename => $tags){

			if($modulename === 'basis' || $modulename === 'navigation' || we_base_moduleInfo::isActive($modulename)){
				$retTags = array_merge($retTags, $tags);
			}
		}

		// add custom tags
		$retTags = array_merge($retTags, self::getCustomTags());

		// add application tags
		$retTags = array_merge($retTags, self::getApplicationTags());
		natcasesort($retTags);
		self::initTagLists($retTags);
		return array_values($retTags);
	}

	static function getWeTagGroups($allTags = array()){
		if(($data = we_cache_file::load('TagWizard_groups')) === false){
			self::getExistingWeTags();
			$data = we_cache_file::load('TagWizard_groups');
		}
		return $data;

		/*
		  $taggroups = array();
		  $main = self::getMainTagModules();
		  // 1st make grps based on modules
		  foreach($main as $modulename => $tags){

		  if($modulename === 'basis'){
		  $taggroups['alltags'] = $tags;
		  }

		  if(we_base_moduleInfo::isActive($modulename)){
		  $taggroups[$modulename] = $tags;
		  $taggroups['alltags'] = array_merge($taggroups['alltags'], $tags);
		  }
		  }
		  //add applicationTags
		  $apptags = we_wizard_tag::getApplicationTags();
		  if(!empty($apptags)){
		  $taggroups['apptags'] = $apptags;
		  $taggroups['alltags'] = array_merge($taggroups['alltags'], $taggroups['apptags']);
		  }


		  // 2nd add some taggroups to this array
		  if(empty($allTags)){
		  $allTags = we_wizard_tag::getExistingWeTags();
		  }
		  foreach($GLOBALS['tag_groups'] as $key => $tags){

		  foreach($tags as $tag){
		  if(in_array($tag, $allTags)){
		  $taggroups[$key][] = $tag;
		  }
		  }
		  }

		  // at last add custom tags.
		  $customTags = we_wizard_tag::getCustomTags();
		  if(!empty($customTags)){
		  $taggroups['custom'] = $customTags;
		  $taggroups['alltags'] = array_merge($taggroups['alltags'], $taggroups['custom']);
		  }

		  natcasesort($taggroups['alltags']);
		  return $taggroups; */
	}

	static function getMainTagModules($useDeprecated = true){
		if(!($main = we_cache_file::load('TagWizard_mainTags'))){
			$main = array();
			$tags = self::getTagsFromDir(WE_INCLUDES_PATH . 'weTagWizard/we_tags/');
			foreach($tags as $tagname){
				$tag = weTagData::getTagData($tagname);
				if($useDeprecated || !$tag->isDeprecated()){
					$main[$tag->getModule()][] = $tagname;
				}
			}
			we_cache_file::save('TagWizard_mainTags', $main);
		}
		return $main;
	}

	/**
	 * Initializes database for all tags
	 */
	static function initTagLists($tags){
		if(($count = we_cache_file::load('TagWizard_tagCount')) && (count($tags) == $count[0])){
			return;
		}
		$endTags = array();
		$modules = array();
		$groups = array();
		foreach($tags as $tagname){
			$tag = weTagData::getTagData($tagname);
			if(!is_object($tag)){
				continue;
			}
			$mod = $tag->getModule();
			$modules[$mod][] = $tagname;
			$groups['alltags'][] = $tagname;
			if($mod != 'basis'){
				$groups[$mod][] = $tagname;
			}
			foreach($tag->getGroups() as $group){
				$groups[$group][] = $tagname;
			}
			if($tag->needsEndTag()){
				$endTags[] = $tagname;
			}
		}
		$expiry = 24 * 3600;
		we_cache_file::save('TagWizard_tagCount', array(count($tags)), $expiry);
		we_cache_file::save('TagWizard_needsEndTag', $endTags, $expiry);
		we_cache_file::save('TagWizard_groups', $groups, $expiry);
		we_cache_file::save('TagWizard_modules', $modules, $expiry);
	}

	//FIXME: check if custom tags are updated correctly!
	static function getTagsWithEndTag(){
		if(!($tags = we_cache_file::load('TagWizard_needsEndTag'))){
			self::getExistingWeTags();
			$tags = we_cache_file::load('TagWizard_needsEndTag');
		}
		return $tags;
	}

	static function getCustomTags(){
		if(!($customTags = we_cache_file::load('TagWizard_customTags'))){
			$customTags = self::getTagsFromDir(WE_INCLUDES_PATH . 'weTagWizard/we_tags/custom_tags');
			we_cache_file::save('TagWizard_customTags', $customTags);
		}
		return $customTags;
	}

	static function getTagsFromDir($dir){
		$match = $ret = array();
		if(is_dir($dir)){

			// get the custom tag-descriptions
			$handle = dir($dir);

			while(false !== ($entry = $handle->read())){

				if(preg_match('/we_tag_(.*).inc.php/', $entry, $match)){
					$ret[] = $match[1];
				}
			}
		}
		return $ret;
	}

	static function getApplicationTags(){

		if(!isset($GLOBALS['weTagWizard_applicationTags'])){

			$GLOBALS['weTagWizard_applicationTags'] = array();
			$apptags = array();
			$alltools = we_tool_lookup::getAllTools(true);
			foreach($alltools as $tool){
				$apptags = we_tool_lookup::getAllToolTagWizards($tool['name']);
				$apptagnames = array_keys($apptags);
				$GLOBALS['weTagWizard_applicationTags'] = array_merge($GLOBALS['weTagWizard_applicationTags'], $apptagnames);
			}
		}
		return $GLOBALS['weTagWizard_applicationTags'];
	}

}
