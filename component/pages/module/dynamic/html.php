<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Pages;

use Nooku\Library;

/**
 * Dynamic Module Html View
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Component\Pages
 */
class ModuleDynamicHtml extends ModuleDefaultHtml implements Library\ObjectMultiton
{
    protected function _actionRender(Library\ViewContext $context)
    {
        //Dynamically attach the chrome filter
        if(!empty($this->module->chrome))
        {
            $this->getTemplate()->attachFilter('com:pages.template.filter.chrome', array(
                'module' => $this->getIdentifier(),
                'styles' => $this->module->chrome
            ));
        }

        $this->_content = (string) $this->getTemplate()
            ->setContent($this->_content)
            ->setData($this->_data);

        return $this->_content;
    }
}