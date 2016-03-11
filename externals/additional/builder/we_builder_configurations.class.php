<?php
/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 11477 $
 * $Author: lukasimhof $
 * $Date: 2016-02-19 16:51:56 +0100 (Fri, 19 Feb 2016) $
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

 class we_builder_configurations{
	protected $configurations = array(
		'trunk' => array(
			'release' => array(
				'targetBranchDir' => 'trunk',
				'targetType' => 'release',
				'targetVersion' => 6440,
				'targetCompareVersion' => 6430,
				'targetName' => 'webEdition 6.4.4',
				'builderCreateTag' => true,
				'builderVersionsToDelete' => array(),
			),
			'nightly' => array(
				'targetBranchDir' => 'trunk',
				'targetType' => 'nightly',
				'targetVersion' => 6441,
				'targetCompareVersion' => 6440,
				'targetName' => '6.4.4.1 Nightly',
			)
		),
		'mgallery' => array(
			'release' => array(
				'targetBranchDir' => 'branches/mgallery',
				'targetType' => 'rc',
				'targetTypeversion' => 2,
				'targetVersion' => 6501,
				'targetName' => '7.0 RC 2',
				'targetTakeSnapshot' => true,
				'builderVersionsToDelete' => array(),
			),
			'nightly' => array(
				'targetBranchDir' => 'branches/mgallery',
				'targetType' => 'nightly',
				'targetVersion' => 6502,
				'targetName' => '6.5.0.2 mGallery Nightly',
				'targetTakeSnapshot' => true,
			)
		),
		'main-develop' => array(
			'nightly' => array(
				'targetBranchDir' => 'branches/main-develop',
				'targetType' => 'nightly',
				'targetVersion' => 7001,
				'targetName' => '7.0.0.1 MAIN-DEVELOP',
				'targetTakeSnapshot' => true,
				'builderVersionsToDelete' => array(),
			)
		)
	);

	protected $defaultConfiguration = array(
		'targetBranchDir' => '',
		'targetOrigBranchDir' => '',
		'targetType' => '',
		'targetVersion' => 0,
		'targetName' => '',
		'targetVersionstring' => '',
		'targetTypeversion' => 0,
		'targetZFVersion' => '1.12.13',
		'targetTakeSnapshot' => false,
		'targetCompareVersion' => 0,
		'targetRevisionFrom' => 0,
		'targetHotfixNr' => 0,
		'builderCreateTag' => false,
		'builderVersionsToDelete' => array(),
		'builderIsHotfix' => false,
	);

	protected $branchDirs = array(
		'trunk' => 'trunk',
		'mgallery' => 'branches/mgallery',
		'main-develop' => 'branches/main-develop'
	);

	protected $db = null;
	protected $branch = '';
	protected $type = '';
	protected $isHotfix = false;
	protected $hotfixSN = 0;
	protected $activeConfiguration = array();

	const TYPE_RELEASE = 'release';
	const TYPE_RC = 'rc';
	const TYPE_BETA = 'beta';
	const TYPE_ALPHA = 'alpha';
	const TYPE_NIGHTLY = 'nightly';
	const TYPE_HOTFIX = 'hotfix';

	public function __construct($db, $branch = '', $type = '', $hotfixSN = 0){
		$this->db = $db;
		$this->branch = strtolower($branch);
		$this->setNormalizedType($type);
		$this->isHotfix = $this->type === 'hotfix'; // FIXME: use constant
		$this->hotfixSN = intval($hotfixSN);

		$this->setActiveConfiguration();
	}

	protected function setNormalizedType($type){
		switch(strtolower($type)){
			case 'release':
			case 'rc':
			case 'beta':
			case 'alpha':
				$this->type = 'release';
				break;
			case 'nightly':
			case 'nighty-build':
				$this->type = 'nightly';
				break;
			case 'hotfix':
				$this->type = 'hotfix';
				break;
		}
	}

	protected function setActiveConfiguration(){
		$this->activeConfiguration = ($this->type === 'hotfix') ? ($this->hotfixSN ? $this->getHotfixConfiguration() : array()) :
			(($this->getIsValidBranch() && $this->getIsValidType()) ? array_merge($this->defaultConfiguration, $this->configurations[$this->branch][$this->type]) : array());

		if($this->activeConfiguration){
			$this->activeConfiguration['targetVersionstring'] = implode('.', str_split($this->activeConfiguration['targetVersion']));

			if(!$this->isHotfix && $this->activeConfiguration['targetCompareVersion']){
				$this->activeConfiguration['targetRevisionFrom'] = (f('SELECT revisionTo FROM v6_versions WHERE version=' . intval($this->activeConfiguration['targetCompareVersion']) . ' LIMIT 1', '', $this->db)) - 3;
			}
		}
	}

	protected function getHotfixConfiguration(){
		if(!$this->hotfixSN){
			return array();
		}

		$hash = $this->db->getHash('SELECT * FROM v6_versions WHERE version=' . intval($this->hotfixSN) . ' LIMIT 1');

		$this->branch = $hash['branch'];
		$this->setNormalizedType($hash['type']);t_e("haSH", $hash);

		return array(
			'targetBranchDir' => 'tags/' . implode('.', str_split($hash['version'])),
			'targetOrigBranchDir' => $this->branchDirs[$hash['branch']],
			'targetType' => $hash['type'],
			'targetVersion' => 8000,//$hash['version'],
			'targetName' => $hash['versname'] . ' (h' . ++$hash['hotfixnr'] . ')',
			'targetTypeversion' => $hash['typeversion'],
			'targetTakeSnapshot' => $hash['isSnapshot'],
			'targetCompareVersion' => '[not needed when making hotfix]',
			'targetRevisionFrom' => $hash['revisionFrom'],
			'targetRevisionTo' => $hash['revisionTo'],
			'targetHotfixNr' => $hash['hotfixnr'],
			'targetZFVersion' => $hash['zfversion'],
			'builderIsHotfix' => true
		);
	}

	public function getActiveConfiguration(){
		return $this->activeConfiguration;
	}

	public function get($name = ''){
		if(empty($this->activeConfiguration)){
			//echo 'no such param: ' . $name;
		}

		switch($name){
			case 'targetBranchDir':
			case 'targetOrigBranchDir':
			case 'targetType':
			case 'targetTypeversion':
			case 'targetVersion':
			case 'targetName':
			case 'targetVersionstring':
			case 'targetCompareVersion':
			case 'targetZFVersion':
			case 'targetRevisionFrom':
			case 'targetRevisionTo';
			case 'targetHotfixNr':
			case 'builderVersionsToDelete':
			case 'targetTakeSnapshot':
			case 'builderCreateTag':
				return $this->activeConfiguration[$name];
			case 'targetNormalizedType':
				return $this->type;
			case 'targetBranch':
				return $this->branch;
			default:
				t_e('call for unexisting builder param');
				return '';
		}
	}

	public function getConfigurationString(){
		return 'Branch:            ' . $this->get('targetBranchDir') . '\n' .
'Version:           ' . $this->get('targetVersion') . '\n' .
'Name:              ' . $this->get('targetName') . '\n' .
'Type:              ' . $this->get('targetType') . '\n' .
'Typeversion:       ' . $this->get('targetTypeversion') . '\n' .
'Versionstring:     ' . $this->get('targetVersionstring') . '\n' .
'Compareversion:    ' . ($this->get('targetCompareVersion') ? : ($this->get('targetTakeSnapshot') ? '[not needed when taking snapshot]' : 0)) . '\n' .
'RevisionFrom:      ' . ($this->get('targetRevisionFrom') ? : ($this->get('targetTakeSnapshot') ? '[not needed when taking snapshot]' : 0)) . '\n' .
'Take snapshot:     ' . $this->get('targetTakeSnapshot') . '\n' .
'ZF-Version:        ' . $this->get('targetZFVersion') . '\n' .
'Delete Versions:   ' . $this->get('builderVersionsToDelete') . '\n' .
($this->getIsHotfix() ? 'Hotfix index:      ' . $this->get('targetHotfixNr') . '\n' : '') .
'Create Tag:        ' . $this->get('builderCreateTag');
	}

	public function getIsValidBranch(){
		return isset($this->configurations[$this->branch]) || $this->isHotfix;
	}

	public function getIsValidType(){
		return $this->getIsValidBranch() && (isset($this->configurations[$this->branch][$this->type]) || $this->isHotfix);
	}

	public function getIsHotfix(){
		 return $this->isHotfix;
	}

	public function getIsValidHotfixSN(){
		 return f('SELECT version FROM v6_versions WHERE version=' . intval($this->hotfixSN) . ' LIMIT 1', '', $this->db) ? true : false;
	}

	public function getNormalizedType(){
		 return $this->type;
	}
}


