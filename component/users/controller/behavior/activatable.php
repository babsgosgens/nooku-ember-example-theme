<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Users;

use Nooku\Library;

/**
 * Activatable Controller Behavior
 *
 * @author  Arunas Mazeika <http://nooku.assembla.com/profile/arunasmazeika>
 * @package Nooku\Component\Users
 */
class ControllerBehaviorActivatable extends Library\ControllerBehaviorAbstract
{
    /**
     * Determines whether new created items will be forced for activation.
     *
     * @var mixed bool
     */
    protected $_force;

    /**
     * @var string The filter to be used on activation tokens.
     */
    protected $_filter;

    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_force = $config->force;
        $this->_filter = $config->filter;
    }

    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'force' => true,
            'filter' => 'alnum'
        ));

        parent::_initialize($config);
    }

    protected function _beforeActivate(Library\ControllerContextInterface $context)
    {
        $result = true;

        $activation = $context->request->data->get('activation', $this->_filter);
        $row        = $this->getModel()->getRow();

        if ($activation !== $row->activation)
        {
            $result = false;
        }

        return $result;
    }

    protected function _actionActivate(Library\ControllerContextInterface $context)
    {
        $result = true;

        $row = $this->getModel()->getRow();
        $row->setData(array('activation' => '', 'enabled' => 1));

        if (!$row->save()) {
            $result = false;
        }

        return $result;
    }

    protected function _beforeAdd(Library\ControllerContextInterface $context)
    {
        // Force activation on new records.
        if ($this->_force)
        {
            $context->request->data->enabled    = 0;
        }

        if (!$context->request->data->enabled) {
            $context->request->data->activation = $this->getObject('com:users.database.row.password')->getRandom(32);
        }
    }

    protected function _afterEdit(Library\ControllerContextInterface $context)
    {
        $row = $context->result;

        // Reset activation token if necessary.
        if ($row->enabled && $row->activation)
        {
            $row->activation = '';
            $row->save();
        }
    }
}