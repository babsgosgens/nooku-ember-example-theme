<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

use Nooku\Library;
use Nooku\Component\Application;

/**
 * Html Page View
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Component\Application
 */
class ApplicationViewPageHtml extends Application\ViewPageHtml
{
    /**
     * Get the title
     *
     * @return 	string 	The title of the view
     */
    public function getTitle()
    {
        $title = $this->getObject('application')->getCfg('sitename' );
        return $title;
    }

    protected function _fetchData(Library\ViewContext $context)
    {
        //Set the component and layout information
        if($this->getObject('manager')->isRegistered('dispatcher'))
        {
            $context->data->component = $this->getObject('dispatcher')->getIdentifier()->package;
            $context->data->layout    = $this->getObject('dispatcher')->getController()->getView()->getLayout();
        }
        else
        {
            $context->data->component = '';
            $context->data->layout    = '';
        }

        parent::_fetchData($context);
    }
}