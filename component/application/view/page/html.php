<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Application;

use Nooku\Library;

/**
 * Html Page View
 *
 * @author      Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Component\Application
 */
class ViewPageHtml extends ViewHtml
{
    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'template_filters'	=> array('module', 'style', 'link', 'meta', 'script', 'title', 'message'),
        ));

        parent::_initialize($config);
    }

    protected function _fetchData(Library\ViewContext $context)
    {
        //Set the component and layout information
        $context->data->component = $this->getObject('dispatcher')->getIdentifier()->package;
        $context->data->layout    = $this->getObject('dispatcher')->getController()->getView()->getLayout();

        parent::_fetchData($context);
    }
}