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
 * Module Login Html View
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Component\Users
 */
class UsersModuleLoginHtml extends PagesModuleDefaultHtml
{
    protected function _initialize(Library\ObjectConfig $config)
    { 
        $config->append(array(
            'layout' => $this->getObject('user')->isAuthentic() ? 'logout' : 'login'
        ));
        
        parent::_initialize($config);
    }

    protected function _fetchData(Library\ViewContext $context)
    {
        $page   = $this->getObject('application.pages')->getActive();

        $context->data->name          = $this->module->params->get('name');
        $context->data->usesecure     = $this->module->params->get('usesecure');
        $context->data->show_title    = $this->module->params->get('show_title', false);

        parent::_fetchData($context);
    }
} 