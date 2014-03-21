<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

use Nooku\Library;

/**
 * Article Controller
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Component\Articles
 */
class ArticlesControllerArticle extends Library\ControllerModel
{ 
    protected function _initialize(Library\ObjectConfig $config)
    {
    	$config->append(array(
    		'behaviors' => array(
                'editable', 'persistable',
                'com:activities.controller.behavior.loggable',
    	        'com:revisions.controller.behavior.revisable',
    		    'com:languages.controller.behavior.translatable',
                'com:attachments.controller.behavior.attachable',
                'com:tags.controller.behavior.taggable'
    	    )
    	));
    
    	parent::_initialize($config);
    }
}