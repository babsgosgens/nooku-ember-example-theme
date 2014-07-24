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
 * Dispatcher Authenticator Interface
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Library\Dispatcher
 */
interface DispatcherAuthenticatorInterface
{
    /**
     * Authenticate the request
     *
     * @param DispatcherContextInterface $context	A dispatcher context object
     * @return bool Returns TRUE if the request could be authenticated, FALSE otherwise.
     */
    public function authenticateRequest(DispatcherContextInterface $context);

    /**
     * Sign the response
     *
     * @param DispatcherContextInterface $context	A dispatcher context object
     * @return bool Returns TRUE if the response could be signed, FALSE otherwise.
     */
    public function signResponse(DispatcherContextInterface $context);
}