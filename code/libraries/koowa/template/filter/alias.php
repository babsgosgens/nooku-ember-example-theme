<?php
/**
* @version      $Id: helpers.php 506 2008-10-04 14:40:02Z mathias $
* @category		Koowa
* @package      Koowa_Template
* @subpackage	Filter
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link 		http://www.koowa.org
*/

/**
 * Template read filter for aliases such as @template, @text, @helper, @route etc
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_Template
 * @subpackage	Filter
 */
class KTemplateFilterAlias extends KTemplateFilterAbstract implements KTemplateFilterRead, KTemplateFilterWrite
{
	/**
	 * The alias read map
	 *
	 * @var array
	 */
	protected $_alias_read = array(
		'@helper('   	=> '$this->loadHelper(',
		'@date('    	=> '$this->loadHelper(\'date.format\',',
		'@overlay('    	=> '$this->loadHelper(\'behavior.overlay\', ',
		'@text('	 	=> 'JText::_(',
		'@template('	=> 'KFactory::get($this->getView())->loadTemplate(',
		'@route('    	=> 'KFactory::get($this->getView())->createRoute(',
		'@escape('		=> 'KFactory::get($this->getView())->escape(',
	);
	
	/**
	 * The alias write map
	 *
	 * @var array
	 */
	protected $_alias_write = array();
	
	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @return void
     */
	protected function _initialize(KConfig $config)
    {
    	$config->append(array(
			'priority'   => KCommandChain::PRIORITY_HIGH,
	  	));

    	parent::_initialize($config);
   	}
	
	/**
	 * Append an alias 
	 *
	 * @param array 	An array of aliases to be appended
	 * @return KTemplateFilterAlias
	 */
	public function append(array $alias, $mode = KTemplateFilter::MODE_READ)
	{
		if($mode == KTemplateFilter::MODE_READ) {
			$this->_alias_read = array_merge($this->_alias_read, $alias); 
		}
		
		if($mode == KTemplateFilter::MODE_WRITE) {
			$this->_alias_write = array_merge($this->_alias_write, $alias); 
		}
		
		return $this;
	}
	
	/**
	 * Convert the alias
	 *
	 * @param string
	 * @return KTemplateFilterAlias
	 */
	public function read(&$text) 
	{
		$text = str_replace(
			array_keys($this->_alias_read), 
			array_values($this->_alias_read), 
			$text);
			
		return $this;
	}
	
	/**
	 * Convert the alias
	 *
	 * @param string
	 * @return KTemplateFilterAlias
	 */
	public function write(&$text) 
	{
		$text = str_replace(
			array_keys($this->_alias_write), 
			array_values($this->_alias_write), 
			$text);
				
		return $this;
	}
}		