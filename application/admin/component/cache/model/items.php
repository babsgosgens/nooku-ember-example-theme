<?php
/**
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Cache
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

use Nooku\Library;

/**
 * Cache Group Model
 *
 * @author      Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @category	Nooku
 * @package     Nooku_Server
 * @subpackage  Cache
 */
class CacheModelItems extends Library\ModelAbstract
{	
    public function __construct(Library\ObjectConfig $config)
	{
	    parent::__construct($config);

        $this->getState()
		    ->insert('name'  , 'cmd')
		    ->insert('hash'  , 'cmd')
		    ->insert('group' , 'url')
		    ->insert('site'  , 'cmd')
		 	->insert('limit' , 'int')
            ->insert('offset', 'int')
            ->insert('sort'     , 'cmd')
            ->insert('direction', 'word', 'asc')
            ->insert('search'   , 'string');
	}
	
    public function getRowset()
    { 
        if(!isset($this->_data))
        {
            //Get the keys
            $data = $this->_getData();
          
            //Apply state information
		    if($this->getState()->hash) {
		        $data = array_intersect_key($data, array_flip((array)$this->getState()->hash));
		    } 
		    
		    foreach($data as $key => $value)
	        {    
	            if($this->getState()->group)
		        {
		            if($value->group != $this->getState()->group) {
		               unset($data[$key]);
		            }
		        }
		        
	            if($this->getState()->site)
		        {
		            if($value->site != $this->getState()->site) {
		               unset($data[$key]);
		            }
		        }
	            
	            if($this->getState()->search)
	            {
	                 if($value->name != $this->getState()->search) {
		               unset($data[$key]);
		            }
	            }
            } 
		    
		    //Set the total
            $this->_total = count($data);
		    
            //Apply limit and offset
            if($this->getState()->limit) {
		        $data = array_slice($data, $this->getState()->offset, $this->getState()->limit);
            }
		    
		    $this->_data = $this->getObject('com:cache.database.rowset.items', array('data' => $data));
        }
        
        return $this->_data;
    }
    
    public function getTotal()
    {
        if(!isset($this->_total)) {
            $this->fetch();
        }
        
        return $this->_total;
    }
    
    protected function _getData()
    {  
        return JFactory::getCache()->keys();
    }
}