<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Activities;

use Nooku\Library;

/**
 * Activities JSON View Class
 *
 * @author  Arunas Mazeika <http://nooku.assembla.com/profile/arunasmazeika>
 * @package Nooku\Component\Activities
 * @see 	http://activitystrea.ms/specs/json/1.0/
 */
class ViewActivitiesJson extends Library\ViewJson
{
    protected function _getEntity(Library\ModelEntityInterface $entity)
    {
        $data = parent::_getEntity($entity);
        unset($data['links']); // Cleanup.

        return $data;
    }

    protected function _getActivity(Library\ModelEntityInterface $entity)
    {
        $id = array(
            'tag:'.$this->getUrl()->toString(Library\HttpUrl::BASE),
            'id:'.$entity->id
        );

        $template = $this->getObject('template.default', array('view' => $this));
        $item = array(
            'id' => implode(',', $id),
            'published' => $this->getObject('com:activities.template.helper.date', array('template' => $template))->format(array(
                    'date'   => $entity->created_on,
                    'format' => 'Y-m-dTZ'
                )),
            'verb' => $entity->action,
            'object' => array(
                'url' => (string)$this->getRoute('option=com_'.$entity->package.'&view='.$entity->name.'&id='.$entity->row),
            ),
            'target' => array(
                'url' => (string)$this->getRoute('option=com_'.$entity->package.'&view='.$entity->name),
            ),
            'actor' => array(
                'url' => (string)$this->getRoute('option=com_users&view=user&id='.$entity->created_by),
            )
        );

        return $item;
    }
}
