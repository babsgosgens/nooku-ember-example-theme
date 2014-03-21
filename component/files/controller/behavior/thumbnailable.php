<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Files;

use Nooku\Library;

/**
 * Thumbnailable Controller Behavior
 *
 * @author  Ercan Ozkaya <http://nooku.assembla.com/profile/ercanozkaya>
 * @package Nooku\Component\Files
 */
class ControllerBehaviorThumbnailable extends Library\ControllerBehaviorAbstract
{
    protected function _afterBrowse(Library\ControllerContextInterface $context)
    {
        $container = $this->getModel()->getContainer();

        if (!$context->request->query->get('thumbnails', 'cmd') || $container->parameters->thumbnails !== true) {
            return;
        }

        $files = array();
        foreach ($context->result as $row)
        {
            if ($row->getIdentifier()->name === 'file') {
                $files[] = $row->name;
            }
        }

        $query =  array(
            'container' => $this->getModel()->getState()->container,
            'folder'    => $context->request->query->get('folder', 'com:files.filter.path'),
            'filename'  => $files,
            'limit'     => 0,
            'offset'    => 0
       );

        $controller = $this->getObject('com:files.controller.thumbnail');
        $controller->getRequest()->setQuery($query);

        $thumbnails = $controller->browse();

        foreach ($thumbnails as $thumbnail)
        {
            if ($row = $context->result->find($thumbnail->filename)) {
                $row->thumbnail = $thumbnail->thumbnail;
            }
        }

        foreach ($context->result as $row)
        {
            if (!$row->thumbnail) {
                $row->thumbnail = null;
            }
        }
    }
}
