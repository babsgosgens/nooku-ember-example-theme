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
 * Resettable Dispatcher Behavior - Post, Redirect, Get
 *
 * When a browser sends a POST request (e.g. after submitting a form), the browser will try to protect them from sending
 * the POST again, breaking the back button, causing browser warnings and pop-ups, and sometimes re-posting the form.
 *
 * Instead, when receiving a none AJAX POST request reset the browser by redirecting it through a GET request.
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Library\Dispatcher
 */
class DispatcherBehaviorResettable extends ControllerBehaviorAbstract
{
    /**
     * Check if the behavior is supported
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported()
    {
        $mixer   = $this->getMixer();
        $request = $mixer->getRequest();

        if(!$request->isSafe() && !$request->isAjax() && $request->getFormat() == 'html') {
            return true;
        }

        return false;
    }

    /**
     * Force a GET after POST using the referrer
     *
     * Redirect if the controller has a returned a 2xx status code.
     *
     * @param 	DispatcherContextInterface $context The active command context
     * @return 	void
     */
    protected function _beforeSend(DispatcherContextInterface $context)
    {
        $response = $context->response;
        $request  = $context->request;

        if($response->isSuccess()) {
            $response->setRedirect($request->getReferrer());
        }
    }
}