<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

use Nooku\Library;

/**
 * Breadcrumbs Module Html View
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Component\Pages
 */
class PagesModuleBreadcrumbsHtml extends PagesModuleDefaultHtml
{
    protected function _fetchData(Library\ViewContext $context)
    {
        $list   = (array) $this->getObject('application')->getPathway()->items;
        $params = $this->module->getParameters();

        if($params->get('homeText'))
        {
            $item = new \stdClass();
            $item->name = $params->get('homeText', $this->getObject('translator')->translate('Home'));

            $home = $this->getObject('application.pages')->getHome();
            $item->link = $this->getRoute($home->getLink()->getQuery().'&Itemid='.$home->id);

            array_unshift($list, $item);
        }

        $context->data->list = $list;

        parent::_fetchData($context);
    }
} 