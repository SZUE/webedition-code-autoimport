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
 * this class implements the cache functionality for the glossary
 *
 */
class we_glossary_cache{
	/**
	 * language of the cache
	 *
	 * @var string
	 */
	var $language = '';

	/**
	 * internal id of the cache
	 *
	 * @var string
	 */
	var $_cacheId = '';
	private $content = '';

	/**
	 * Constructor
	 *
	 * @param string $language
	 */
	function __construct($language){
		$this->language = $language;
		$this->_createCacheId();
	}

	/**
	 * Create the cache identifier
	 *
	 * @access private
	 */
	function _createCacheId(){
		$this->_cacheId = $this->language;
	}

	/**
	 * get the cache filename of a given cache id
	 *
	 * @param string $id
	 * @return string
	 * @access public
	 * @abstract
	 */
	function cacheIdToFilename($id){
		return WE_GLOSSARY_MODULE_PATH . 'cache/cache_' . $id . '.php';
	}

	/**
	 * get the cache id of a given cache filename
	 *
	 * @param string $filename
	 * @return string
	 * @access public
	 * @abstract
	 */
	function filenameToCacheId($filename){
		return intval(str_replace(array(WE_GLOSSARY_MODULE_PATH . 'data/cache_', '.php'), '', $filename));
	}

	/**
	 * checks if the cache file is valid
	 *
	 * @return boolean
	 */
	function isValid(){
		$cacheFilename = self::cacheIdToFilename($this->_cacheId);

		return file_exists($cacheFilename) && is_file($cacheFilename);
	}

	/**
	 * deletes the cache file
	 *
	 * @return boolean
	 */
	function clear(){
		if($this->isValid()){
			return unlink(self::cacheIdToFilename($this->_cacheId));
		}
		return true;
	}

	/**
	 * write the given entries into the cache file
	 *
	 * @return boolean
	 */
	function write(){
		$DB_WE = new DB_WE();

		$DB_WE->query('SELECT Text, Type, Language, Title, Attributes, LENGTH(Text) as Length,Fullword FROM ' . GLOSSARY_TABLE . ' WHERE Language="' . $DB_WE->escape($this->language) . '" AND Published>0 ORDER BY Length DESC');
		$Items = array();

		while($DB_WE->next_record()){
			$Type = $DB_WE->f('Type');
			$Text = trim($DB_WE->f('Text'));
			$Title = trim($DB_WE->f('Title'));
			$Attributes = substr($DB_WE->f('Attributes'), 0, 2) == 'a:' ? unserialize($DB_WE->f('Attributes')) : array();
			$Attributes = array_map('trim', $Attributes);


			if($GLOBALS['WE_BACKENDCHARSET'] === 'UTF-8' && isset($GLOBALS['we_doc']->elements['Charset']['dat']) && $GLOBALS['we_doc']->elements['Charset']['dat'] != 'UTF-8'){
				$Text = utf8_decode($Text);
				$Title = utf8_decode($Title);
			}

			$Text = oldHtmlspecialchars($Text, ENT_NOQUOTES);
			$Title = oldHtmlspecialchars($Title, ENT_QUOTES);

			$temp = array(
				'Fullword' => $DB_WE->f('Fullword')
			);

			if($Title){
				$temp['title'] = $Title;
			}

			// Language
			if(isset($Attributes['lang']) && $Attributes['lang']){
				$temp['lang'] = $Attributes['lang'];
				$temp['xml:lang'] = $temp['lang'];
			}

			// Language
			if($Type == we_glossary_glossary::TYPE_LINK){
				$urladd = array();

				if(isset($Attributes['mode'])){
					$Attributes['mode'] = $Attributes['mode'];
					switch($Attributes['mode']){
						// External Link
						case "extern":

							// Href
							$temp['href'] = (isset($Attributes['ExternUrl']) && $Attributes['ExternUrl'] && $Attributes['ExternUrl'] != we_base_link::EMPTY_EXT ?
									$Attributes['ExternUrl'] :
									'');

							// Parameter
							if(isset($Attributes['ExternParameter']) && $Attributes['ExternParameter']){
								$urladd[] = $Attributes['ExternParameter'];
							}
							break;
						// Internal Link
						case "intern":

							// LinkID
							$temp['href'] = (isset($Attributes['InternLinkID']) && $Attributes['InternLinkID'] ?
									id_to_path($Attributes['InternLinkID']) :
									'');

							// Parameter
							if(isset($Attributes['InternParameter']) && $Attributes['InternParameter']){
								$urladd[] = $Attributes['InternParameter'];
							}
							break;
						// Object Link
						case "object":
							// LinkID
							$temp['href'] = (isset($Attributes['ObjectLinkPath']) && $Attributes['ObjectLinkPath'] ?
									$Attributes['ObjectLinkPath'] :
									'');

							if(isset($Attributes['ObjectLinkID']) && $Attributes['ObjectLinkID']){
								$urladd[] = 'we_objectID=' . $Attributes['ObjectLinkID'];
							}

							// Parameter
							if(isset($Attributes['ObjectParameter']) && $Attributes['ObjectParameter']){
								$urladd[] = $Attributes['ObjectParameter'];
							}
							break;

						case 'category':// Category Link

							$temp['href'] = '';
							if(isset($Attributes['modeCategory']) && $Attributes['modeCategory'] === 'intern'){
								// LinkID
								if(isset($Attributes['CategoryInternLinkID']) && $Attributes['CategoryInternLinkID']){
									$temp['href'] .= id_to_path($Attributes['CategoryInternLinkID']);
								}
							} else {
								// Href
								if(isset($Attributes['CategoryUrl']) && $Attributes['CategoryUrl']){
									$temp['href'] .= $Attributes['CategoryUrl'];
								}
							}

							// Cat Parameter & Cat ID
							if(isset($Attributes['CategoryCatParameter']) && $Attributes['CategoryCatParameter'] && isset($Attributes['CategoryLinkID']) && $Attributes['CategoryLinkID']){
								$urladd[] = $Attributes['CategoryCatParameter'] . '=' . $Attributes['CategoryLinkID'];
							}

							// Parameter
							if(isset($Attributes['CategoryParameter']) && $Attributes['CategoryParameter']){
								$urladd[] = $Attributes['CategoryParameter'];
							}
							break;
					}
				}


				// Attribute
				if(isset($Attributes['attribute']) && $Attributes['attribute']){
					$temp['attribute'] = ' ' . addslashes($Attributes['attribute'] . " ");
				}

				// Target
				if(isset($Attributes['target']) && $Attributes['target']){
					$temp['target'] = $Attributes['target'];
				}

				// hreflang
				if(isset($Attributes['hreflang']) && $Attributes['hreflang']){
					$temp['hreflang'] = $Attributes['hreflang'];
				}

				// Accesskey
				if(isset($Attributes['accesskey']) && $Attributes['accesskey']){
					$temp['accesskey'] = $Attributes['accesskey'];
				}

				// tabindex
				if(isset($Attributes['tabindex']) && $Attributes['tabindex']){
					$temp['tabindex'] = $Attributes['tabindex'];
				}

				// rel
				if(isset($Attributes['rel']) && $Attributes['rel']){
					$temp['rel'] = $Attributes['rel'];
				}

				// rev
				if(isset($Attributes['rev']) && $Attributes['rev']){
					$temp['rev'] = $Attributes['rev'];
				}

				$temp['href'] .= ($urladd ? '?' . implode('&', $urladd) : '') .
					// Anchor
					(isset($Attributes['anchor']) && $Attributes['anchor'] ?
						'#' . $Attributes['anchor'] :
						'');

				// popup_open
				if(isset($Attributes['popup_open']) && $Attributes['popup_open'] == 1){
					// popup_width
					$width = (isset($Attributes['popup_width']) && $Attributes['popup_width'] ? $Attributes['popup_width'] : 100);

					// popup_height
					$height = (isset($Attributes['popup_height']) && $Attributes['popup_height'] ? $Attributes['popup_height'] : 100);

					$temp['onclick'] = 'var we_winOpts=\'\';' .
						// popup_center
						(isset($Attributes['popup_center']) && $Attributes['popup_center'] ? '
if (window.screen) {
	var w=' . $width . ';
	var h=' . $height . ';
	var screen_height = screen.availHeight - 70;
	var screen_width = screen.availWidth-10;
	var w = Math.min(screen_width,w);
	var h = Math.min(screen_height,h);
	var h = Math.min(screen_height,h);
	var x = (screen_width - w) / 2;
	var y = (screen_height - h) / 2;
	we_winOpts = \'left=\'+x+\',top=\'+y;
}' :
// popup_xposition
							(isset($Attributes['popup_xposition']) && $Attributes['popup_xposition'] ?
								"we_winOpts += (we_winOpts ? ',' : '')+'left=" . $Attributes['popup_xposition'] . "';" :
								'') .
							// popup_yposition
							(isset($Attributes['popup_yposition']) && $Attributes['popup_yposition'] ?
								"we_winOpts += (we_winOpts ? ',' : '')+'top=" . $Attributes['popup_yposition'] . "';" :
								'')
						) .
						// popup_width
						strtr("we_winOpts += (we_winOpts ? ',' : '')+'width=" . $width . "';" .
							// popup_height
							"we_winOpts += (we_winOpts ? ',' : '')+'height=" . $height . "';" .
							// popup_status
							"we_winOpts += (we_winOpts ? ',' : '')+'status=" . (isset($Attributes['popup_status']) && $Attributes['popup_status'] == 1 ? 'yes' : 'no') . "';" .
							// popup_scrollbars
							"we_winOpts += (we_winOpts ? ',' : '')+'scrollbars=" . (isset($Attributes['popup_scrollbars']) && $Attributes['popup_scrollbars'] == 1 ? 'yes' : 'no') . "';" .
							// popup_menubar
							"we_winOpts += (we_winOpts ? ',' : '')+'menubar=" . (isset($Attributes['popup_menubar']) && $Attributes['popup_menubar'] == 1 ? 'yes' : 'no') . "';" .
							// popup_resizable
							"we_winOpts += (we_winOpts ? ',' : '')+'resizable=" . (isset($Attributes['popup_resizable']) && $Attributes['popup_resizable'] == 1 ? 'yes' : 'no') . "';" .
							// popup_location
							"we_winOpts += (we_winOpts ? ',' : '')+'location=" . (isset($Attributes['popup_location']) && $Attributes['popup_location'] == 1 ? 'yes' : 'no') . "';" .
							// popup_toolbar
							"we_winOpts += (we_winOpts ? ',' : '')+'toolbar=" . (isset($Attributes['popup_toolbar']) && $Attributes['popup_toolbar'] == 1 ? 'yes' : 'no') . "';" .
							"var we_win = window.open('" . $temp['href'] . "','we_test',we_winOpts);", array('\'' => '@@@we@@@'));

					$temp['href'] = '#';
				}
			}

			$Items[$Text][$Type] = $temp;
		}

		$content = array(
			we_glossary_glossary::TYPE_LINK => array(),
			we_glossary_glossary::TYPE_ACRONYM => array(),
			we_glossary_glossary::TYPE_ABBREVATION => array(),
			we_glossary_glossary::TYPE_FOREIGNWORD => array(),
			we_glossary_glossary::TYPE_TEXTREPLACE => array(),
		);

		foreach($Items as $Text => $Value){
			$prefix = '';
			$postfix = '';
			foreach($Value as $Type => $AttributeList){

				switch($Type){
					case we_glossary_glossary::TYPE_LINK:
						$Tag = 'a';
						break;
					case we_glossary_glossary::TYPE_ACRONYM:
						$Tag = 'acronym';
						break;
					case we_glossary_glossary::TYPE_ABBREVATION:
						$Tag = 'abbr';
						break;
					case we_glossary_glossary::TYPE_FOREIGNWORD:
						$Tag = 'span';
						break;
					case we_glossary_glossary::TYPE_TEXTREPLACE:
						$Tag = '';
						break;
				}

				if($Tag != ''){
					$prefix .= '<' . $Tag;
				}
				$full = $AttributeList['Fullword'];
				unset($AttributeList['Fullword']);

				if($Type != we_glossary_glossary::TYPE_TEXTREPLACE){
					foreach($AttributeList as $Attribute => $Val){
						$prefix .= ($Attribute === 'attribute' ? $Val : ' ' . $Attribute . '="' . $Val . '"');
					}
				} else {
					$prefix .=$AttributeList['title'];
				}
				if($Tag != ''){
					$prefix .= '>';
					$postfix = '</' . $Tag . '>' . $postfix;
				}
			}
			//make sure we found a whole word!
			if($full){//no quotes - they're used for attributes
				$content[$Type]['-(^|\s|[!#$%&\'()*+,\-./:;=?@[\\]^_`{\|}~])(' . preg_quote($Text, '-') . ')(\s|[!"#$%&\'()*+,\-./:;=?@[\\]^_`{\|}~]|$)-'] = '${1}' . $prefix . ($Tag ? '${2}' : '') . $postfix . '${3}';
			} else {
				$content[$Type]['-(' . preg_quote($Text, '-') . ')-'] = $prefix . ($Tag ? '${2}' : '') . $postfix;
			}
		}

		$cacheFilename = self::cacheIdToFilename($this->_cacheId);

		// Create Cache Directory if it not exists
		if(!is_dir(dirname($cacheFilename))){
			if(!we_base_file::createLocalFolder(dirname($cacheFilename))){
				return false;
			}
		}

		return we_base_file::save($cacheFilename, gzdeflate(serialize($content), 9));
	}

	/**
	 * get all entries from the cache
	 *
	 * @return array
	 */
	function get($type){
		if(!$this->content){
			$cacheFilename = self::cacheIdToFilename($this->_cacheId);

			if(!file_exists($cacheFilename) || !is_file($cacheFilename)){
				if(!self::write()){
					return array();
				}
			}
			if(we_base_file::load($cacheFilename, 'rb', 5) === '<?php'){
				include($cacheFilename);
				$this->content = $content;
				//update old cache file!
				we_base_file::save($cacheFilename, gzdeflate(serialize($content), 9));
			} else {
				$this->content = @unserialize(@gzinflate(we_base_file::load($cacheFilename)));
			}
		}
		return ($this->content ?
				$this->content[$type] :
				array());
	}

}
