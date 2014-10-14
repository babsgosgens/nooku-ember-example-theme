<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-platform for the canonical source repository
 */

use Nooku\Library;
use Nooku\Component\Users;

/**
 * Http Dispatcher
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Component\Users
 */
class UsersDispatcherHttp extends Users\DispatcherHttp
{
    protected function _actionDispatch(Library\DispatcherContextInterface $context)
	{        	
        if($context->user->isAuthentic())
        {  
            //Redirect if user is already logged in
            if($context->request->query->get('view', 'alpha') == 'session')
            {
                $menu = $this->getObject('application.pages')->getHome();
                //@TODO : Fix the redirect
                //$this->getObject('application')->redirect('?Itemid='.$menu->id, 'You are already logged in!');
            }
        }

        if(!$context->user->isAuthentic())
        {  
            //Redirect if user is already logged in
            if($context->request->query->get('view', 'alpha') == 'session')
            {
                $menu = $this->getObject('application.pages')->getHome();
                //@TODO : Fix the redirect
                //$this->getObject('application')->redirect('?Itemid='.$menu->id, 'You are already logged out!');
            }
        } 
               
        return parent::_actionDispatch($context);
	}
}