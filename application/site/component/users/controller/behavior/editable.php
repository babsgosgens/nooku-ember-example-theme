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
 * Editable Controller Behavior
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Component\Users
 */
class UsersControllerBehaviorEditable extends Library\ControllerBehaviorEditable
{
    protected function _actionSave(Library\ControllerContextInterface $context)
    {
        $entity = parent::_actionSave($context);

        if ($entity->getStatus() === $entity::STATUS_FAILED) {
            $context->response->setRedirect($context->request->getUrl(), $entity->getStatusMessage(), 'error');
        }

        return $entity;
    }

    protected function _actionCancel(Library\ControllerContextInterface $context)
    {
        $context->response->setRedirect($this->getReferrer($context));
    }

    protected function _actionApply(Library\ControllerContextInterface $context)
    {
        $entity = $context->getSubject()->execute('edit', $context);

        $context->response->setRedirect($context->request->getUrl());
        return $entity;
    }

    public function canSave()
    {
        return $this->canEdit();
    }
}