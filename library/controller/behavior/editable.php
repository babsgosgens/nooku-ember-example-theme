<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Library;

/**
 * Editable Controller Behavior
 *
 * Behavior defines 'save', 'apply' and cancel functions. Functions are only executable if the request format is
 * 'html'. For other formats, eg json use 'edit' and 'read' actions directly.
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Library\Controller
 */
class ControllerBehaviorEditable extends ControllerBehaviorAbstract
{
    /**
     * Constructor
     *
     * @param ObjectConfig $config  An optional ObjectConfig object with configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('after.read'  , '_lockResource');
        $this->addCommandCallback('after.save'  , '_unlockResource');
        $this->addCommandCallback('after.cancel', '_unlockResource');


        $this->addCommandCallback('before.read' , 'setReferrer');
        $this->addCommandCallback('after.apply' , '_lockReferrer');
        $this->addCommandCallback('after.read'  , '_unlockReferrer');
        $this->addCommandCallback('after.save'  , '_unsetReferrer');
        $this->addCommandCallback('after.cancel', '_unsetReferrer');
    }

    /**
     * Check if the behavior is supported
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported()
    {
        $mixer   = $this->getMixer();
        $request = $mixer->getRequest();

        if ($mixer instanceof ControllerModellable && $mixer->isDispatched() && $request->getFormat() == 'html') {
            return true;
        }

        return false;
    }

    /**
     * Get the referrer
     *
     * @param   ControllerContextInterface $context A controller context object
     * @return HttpUrl    A HttpUrl object.
     */
    public function getReferrer(ControllerContextInterface $context)
    {
        $referrer = $this->getObject('lib:http.url',
            array('url' => $context->request->cookies->get('referrer', 'url'))
        );

        return $referrer;
    }

    /**
     * Set the referrer
     *
     * @param  ControllerContextInterface $context A controller context object
     * @return void
     */
    public function setReferrer(ControllerContextInterface $context)
    {
        if (!$context->request->cookies->has('referrer_locked'))
        {
            $request  = $context->request->getUrl();
            $referrer = $context->request->getReferrer();

            //Compare request url and referrer
            if (!isset($referrer) || ((string)$referrer == (string)$request))
            {
                $controller = $this->getMixer();
                $identifier = $controller->getIdentifier();

                $option = 'com_' . $identifier->package;
                $view = StringInflector::pluralize($identifier->name);
                $referrer = $controller->getView()->getRoute('option=' . $option . '&view=' . $view, true, false);
            }

            //Add the referrer cookie
            $cookie = $this->getObject('lib:http.cookie', array(
                'name'   => 'referrer',
                'value'  => $referrer,
                'path'   => $context->request->getBaseUrl()->getPath()
            ));

            $context->response->headers->addCookie($cookie);
        }
    }

    /**
     * Lock the referrer from updates
     *
     * @param  ControllerContextInterface  $context A controller context object
     * @return void
     */
    protected function _lockReferrer(ControllerContextInterface $context)
    {
        $cookie = $this->getObject('lib:http.cookie', array(
            'name'   => 'referrer_locked',
            'value'  => true,
            'path'   => $context->request->getBaseUrl()->getPath()
        ));

        $context->response->headers->addCookie($cookie);
    }

    /**
     * Unlock the referrer for updates
     *
     * @param   ControllerContextInterface  $context A controller context object
     * @return void
     */
    protected function _unlockReferrer(ControllerContextInterface $context)
    {
        $path = $context->request->getBaseUrl()->getPath();
        $context->response->headers->clearCookie('referrer_locked', $path);
    }

    /**
     * Unset the referrer
     *
     * @return void
     */
    protected function _unsetReferrer(ControllerContextInterface $context)
    {
        $path = $context->request->getBaseUrl()->getPath();
        $context->response->headers->clearCookie('referrer', $path);
    }

    /**
     * Lock the resource
     *
     * Only lock if the context contains a row object and if the user has an active session he can edit or delete the
     * resource. Otherwise don't lock it.
     *
     * @param   ControllerContextInterface  $context A controller context object
     * @return  void
     */
    protected function _lockResource(ControllerContextInterface $context)
    {
        if($this->isLockable() && $this->canEdit()) {
            $context->result->lock();
        }
    }

    /**
     * Unlock the resource
     *
     * @param  ControllerContextInterface  $context A controller context object
     * @return void
     */
    protected function _unlockResource(ControllerContextInterface $context)
    {
        if($this->isLockable() && $this->canEdit()) {
            $context->result->unlock();
        }
    }

    /**
     * Check if the resource is locked
     *
     * @return bool Returns TRUE if the resource is locked, FALSE otherwise.
     */
    public function isLocked()
    {
        if($this->getModel()->getState()->isUnique())
        {
            $entity = $this->getModel()->getRow();

            if($entity->isLockable() && $entity->isLocked()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the resource is lockable
     *
     * @return bool Returns TRUE if the resource is can be locked, FALSE otherwise.
     */
    public function isLockable()
    {
        $controller = $this->getMixer();

        if($controller instanceof ControllerModellable)
        {
            if($this->getModel()->getState()->isUnique())
            {
                $entity = $this->getModel()->getRow();

                if($entity->isLockable()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Permission handler for save actions
     *
     * Method returns TRUE iff the controller implements the ControllerModellable interface.
     *
     * @return  boolean Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canSave()
    {
        if($this->getRequest()->getFormat() == 'html')
        {
            if($this->getModel()->getState()->isUnique())
            {
                if($this->canEdit() && !$this->isLocked()) {
                    return true;
                }
            }
            else
            {
                if($this->canAdd()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Permission handler for apply actions
     *
     * Method returns TRUE iff the controller implements the ControllerModellable interface.
     *
     * @return  boolean Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canApply()
    {
       return $this->canSave();
    }

    /**
     * Permission handler for cancel actions
     *
     * Method returns TRUE iff the controller implements the ControllerModellable interface.
     *
     * @return  boolean Return TRUE if action is permitted. FALSE otherwise.
     */
    public function canCancel()
    {
        if($this->getRequest()->getFormat() == 'html') {
            return $this->canRead();
        }

        return false;
    }

    /**
     * Save action
     *
     * This function wraps around the edit or add action. If the model state is unique a edit action will be
     * executed, if not unique an add action will be executed.
     *
     * This function also sets the redirect to the referrer.
     *
     * @param   ControllerContextInterface  $context A controller context object
     * @return  DatabaseRowInterface     A row object containing the saved data
     */
    protected function _actionSave(ControllerContextInterface $context)
    {
        $action = $this->getModel()->getState()->isUnique() ? 'edit' : 'add';
        $entity = $context->getSubject()->execute($action, $context);

        //Create the redirect
        $context->response->setRedirect($this->getReferrer($context));

        return $entity;
    }

    /**
     * Apply action
     *
     * This function wraps around the edit or add action. If the model state is unique a edit action will be
     * executed, if not unique an add action will be executed.
     *
     * This function also sets the redirect to the current url
     *
     * @param    ControllerContextInterface  $context A controller context object
     * @return   DatabaseRowInterface     A row object containing the saved data
     */
    protected function _actionApply(ControllerContextInterface $context)
    {
        $action = $this->getModel()->getState()->isUnique() ? 'edit' : 'add';
        $entity = $context->getSubject()->execute($action, $context);

        if($action == 'add')
        {
            $url = $this->getReferrer($context);
            if ($entity instanceof DatabaseRowInterface) {
                $url = $context->response->headers->get('Location');
            }

            $context->response->setRedirect($url);
        }
        else $context->response->setStatus(HttpResponse::NO_CONTENT);

        return $entity;
    }

    /**
     * Cancel action
     *
     * This function will unlock the row(s) and set the redirect to the referrer
     *
     * @param   ControllerContextInterface  $context A command context object
     * @return  DatabaseRowInterface    A row object containing the data of the cancelled object
     */
    protected function _actionCancel(ControllerContextInterface $context)
    {
        //Create the redirect
        $context->response->setRedirect($this->getReferrer($context));

        $entity = $context->getSubject()->execute('read', $context);
        return $entity;
    }

    /**
     * Add a lock flash message if the resource is locked
     *
     * @param   ControllerContextInterface	$context A command context object
     * @return 	void
     */
    protected function _afterRead(ControllerContextInterface $context)
    {
        $entity = $context->result;

        //Add the notice if the resource is locked
        if($this->canEdit() && $this->isLockable() && $this->isLocked())
        {
            //Prevent a re-render of the message
            if($context->request->getUrl() != $context->request->getReferrer())
            {
                $user = $this->getObject('user.provider')->load($entity->locked_by);
                $date = $this->getObject('lib:date',array('date' => $entity->locked_on));

                $message = \JText::sprintf('Locked by %s %s', $user->getName(), $date->humanize());

                $context->response->addMessage($message, 'notice');
            }
        }
    }

    /**
     * Prevent editing a locked resource
     *
     * If the resource is locked a Retry-After header indicating the time at which the conflicting edits are expected
     * to complete will be added. Clients should wait until at least this time before retrying the request.
     *
     * @param   ControllerContextInterface	$context A controller context object
     * @throws  ControllerExceptionResourceLocked If the resource is locked
     * @return 	void
     */
    protected function _beforeEdit(ControllerContextInterface $context)
    {
        if($this->isLocked())
        {
            $context->response->headers->set('Retry-After', $context->user->getSession()->getLifetime());
            throw new ControllerExceptionResourceLocked('Resource is locked.');
        }
    }

    /**
     * Prevent deleting a locked resource
     *
     * If the resource is locked a Retry-After header indicating the time at which the conflicting edits are expected
     * to complete will be added. Clients should wait until at least this time before retrying the request.
     *
     * @param   ControllerContextInterface	$context A controller context object
     * @throws  ControllerExceptionResourceLocked If the resource is locked
     * @return 	void
     */
    protected function _beforeDelete(ControllerContextInterface $context)
    {
        if($this->isLocked())
        {
            $context->response->headers->set('Retry-After', $context->user->getSession()->getLifetime());
            throw new ControllerExceptionResourceLocked('Resource is locked');
        }
    }
}