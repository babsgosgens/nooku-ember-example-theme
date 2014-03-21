<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Debug;

use Nooku\Library;

/**
 * Debug Html View
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Component\Debug
 */
class ViewDebugHtml extends Library\ViewHtml
{
    protected function _fetchData(Library\ViewContext $context)
    {
        $database = $this->getObject('com:debug.event.subscriber.database');
        $profiler = $this->getObject('com:debug.event.profiler');
        $language = \JFactory::getLanguage();

        //Remove the template includes
        $includes = get_included_files();

        foreach($includes as $key => $value)
        {
            //Find the real file path
            if($alias = Library\ClassLoader::getInstance()->getAlias($value)) {
                $includes[$key] = $alias;
            };
        }

	    $context->data->memory    = $profiler->getMemory();
	    $context->data->events    = (array) $profiler->getEvents();
	    $context->data->queries   = (array) $database->getQueries();
	    $context->data->languages = (array) $language->getPaths();
	    $context->data->includes  = (array) $includes;
	    $context->data->strings   = (array) $language->getOrphans();

        parent::_fetchData($context);
    }
}