<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright      Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Users;

use Nooku\Library;

/**
 * Form Dispatcher Authenticator
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Library\Dispatcher
 */
class DispatcherAuthenticatorForm extends Library\DispatcherAuthenticatorAbstract
{
    /**
     * Authenticate using email and password credentials
     *
     * @param Library\DispatcherContextInterface $context A dispatcher context object
     * @return  boolean Returns FALSE if the check failed. Otherwise TRUE.
     */
    protected function _beforePost(Library\DispatcherContextInterface $context)
    {
        if ($context->subject->getController()->getIdentifier()->name == 'session' && !$context->user->isAuthentic())
        {
            $password = $context->request->data->get('password', 'string');
            $email    = $context->request->data->get('email', 'email');

            $user = $this->getObject('com:users.model.users')->email($email)->fetch();

            if ($user->id)
            {
                //Check user password
                if (!$user->getPassword()->verifyPassword($password)) {
                    throw new Library\ControllerExceptionRequestNotAuthenticated('Wrong password');
                }

                //Check user enabled
                if (!$user->enabled) {
                    throw new Library\ControllerExceptionRequestNotAuthenticated('Account disabled');
                }

                //Start the session (if not started already)
                $context->user->getSession()->start();

                //Set user data in context
                $data  = $this->getObject('user.provider')->load($user->id)->toArray();
                $data['authentic'] = true;

                $context->user->setData($data);
            }
            else throw new Library\ControllerExceptionRequestNotAuthenticated('Wrong email');
        }
    }
}