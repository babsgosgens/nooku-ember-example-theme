<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Comments;

use Nooku\Library;

/**
 * Comment Controller
 *
 * @author    	Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku\Component\Comments
 */
abstract class ControllerComment extends Library\ControllerModel
{ 
    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'com:activities.controller.behavior.loggable'
            ),
            'model' => 'com:comments.model.comments',
        ));

        //Alias the permission
        $permission         = $this->getIdentifier();
        $permission['path'] = array('controller', 'permission');

        $this->getObject('manager')->registerAlias(
            'com:comments.controller.permission.comment',
            'com:'.$permission->package.'controller.permission.comment'
        );

        parent::_initialize($config);
    }
    
    protected function _actionRender(Library\ControllerContextInterface $context)
    {
        $view = $this->getView();

	    //Alias the view layout
        if($view instanceof Library\ViewTemplate)
	    {
	        $layout = $view->getIdentifier()->toArray();
            $layout['name']  = $view->getLayout();

            $alias = $layout;
            $alias['package'] = 'comments';

	        $this->getObject('manager')->registerAlias($layout, $alias);
	    }

        return parent::_actionRender($context);
    }

    public function getRequest()
	{
		$request = parent::getRequest();

        //Force set the 'table' in the request
        $request->query->table  = $this->getIdentifier()->package;
        $request->data->table   = $this->getIdentifier()->package;

	    return $request;
	}
}