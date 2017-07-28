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

/**
 * Class taskFragment()
 *
 * This class offers methods to split tasks in more than one fragment.
 * It is needed if you need to do a lot of work, which takes time
 * longer than the timeout of some servers
 */
abstract class we_fragment_base{
	/**
	 * Number of all tasks.
	 * @var        int
	 */
	protected $numberOfTasks = 1;

	/**
	 * Number of current tasks.
	 * @var        int
	 */
	protected $currentTask = 0;

	/**
	 * Number of tasks per fragment.
	 * @var        int
	 */
	protected $taskPerFragment = 1;

	/**
	 * Array of the data.
	 * @var        array
	 */
	protected $alldata = [];

	/**
	 * Data for the current task.
	 * @var        mixed
	 */
	protected $data = null;

	/**
	 * Name for the whole fragment action.
	 * This variable is used for a reference, so it must be unique
	 * @var        string
	 */
	protected $name;

	/**
	 * Pause for each task in ms.
	 * @var        int
	 */
	protected $initdata = null;
	protected $jsCmd = null;

	/**
	 * init Data.
	 * @var        array
	 */

	/**
	 * This is the constructor of the class. Everything will be done here.
	 *
	 * @param      string $name
	 * @param      int $taskPerFragment
	 * @param      int $pause
	 * @param      int $bodyAttributes
	 * @param      array $initdata
	 */
	public function __construct($name, $taskPerFragment, array $bodyAttributes = [], $initdata = "", we_base_jsCmd $jsCmd = null){
		$this->name = $name;
		$this->taskPerFragment = $taskPerFragment;
		if($initdata){
			$this->initdata = $initdata;
		}
		$this->jsCmd = $jsCmd? : new we_base_jsCmd();

		//FIXME: make this DB entries; create method for early creation, since the whole data might be too much for memory!
		$filename = WE_FRAGMENT_PATH . $this->name;
		$this->currentTask = we_base_request::_(we_base_request::INT, 'fr_' . $this->name . '_ct', 0);
		if(file_exists($filename) && $this->currentTask){
			$ser = we_base_file::load($filename);
			if(!$ser){
				exit('Could not read: ' . $filename);
			}
			$this->alldata = we_unserialize($ser);
		} else {
			$this->taskPerFragment = $taskPerFragment;
			$this->init();
			if(!we_base_file::save($filename, we_serialize($this->alldata))){
				exit('Could not write: ' . $filename);
			}
			we_base_file::insertIntoCleanUp($filename, 10 * 3600);
		}
		$this->numberOfTasks = count($this->alldata);
		$this->updateTaskPerFragment();
		for($i = 0; $i < $this->taskPerFragment && $this->currentTask < $this->numberOfTasks; $i++){
			$this->data = $this->alldata[$this->currentTask];
			$this->doTask();
			$this->currentTask++;
		}

		$this->printPage($bodyAttributes);
	}

	protected function printPage(array $bodyAttributes = []){
		$this->updateProgressBar();
		if($this->currentTask >= $this->numberOfTasks){
			$filename = WE_FRAGMENT_PATH . $this->name;
			we_base_file::delete($filename);
			$this->finish($this->jsCmd);
		} else {
			$this->getJSReload();
		}

		echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(JS_DIR . 'we_fragment.js') . $this->jsCmd->getCmds(), we_html_element::htmlBody($bodyAttributes));
	}

	protected function getJSReload(){
		$nextTask = $this->currentTask;

		if(($nextTask < $this->numberOfTasks)){
			$tmp = $_REQUEST;
			$tmp['fr_' . $this->name . '_ct'] = $nextTask;
			$tmp['doFragments'] = 1;
			$tail = http_build_query($tmp, null, '&', PHP_QUERY_RFC3986);
			$this->jsCmd->addCmd('location', ['doc' => 'document', 'loc' => getScriptName() . '?' . $tail]);
		}
	}

	protected function updateProgressBar(){
		return '';
	}

	protected function updateTaskPerFragment(){

	}

	/**
	 * Overwrite this function to initialize the array taskFragment::alldata.
	 *
	 */
	protected function init(){
		$this->alldata = $this->initdata;
	}

	/**
	 * Overwrite this function to do the work for each task.
	 *
	 */
	protected function doTask(){

	}

	/**
	 * Overwrite this function to do the work when everything is finished.
	 *
	 */
	protected function finish(){

	}

}

/*
  //EXAMPLE:


class myFrag extends taskFragment{

	function init(){
		$this->alldata = array(
							array("color"=>"red","size"=>30),
							array("color"=>"green","size"=>10),
							array("color"=>"blue","size"=>20),
							array("color"=>"yellow","size"=>50)
							);
	}

	function doTask(){
		$id = $this->data;
		print "Color:".$this->data["color"]."<br/>";
		print "Size:".$this->data["size"]."<br/><br/>";
	}

	function finish(){
		print "FINISHED!";
	}

	function printHeader(){
		print "<html><head><title>myFragment</title></head>";
	}
}

$fr = new myFrag("holeg",1,800,array("bgcolor"=>"silver"));


*/