<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-platform for the canonical source repository
 */

namespace Nooku\Library;

/**
 * Controller Viewable Interface
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Nooku\Library\Controller
 */
interface ControllerViewable
{
    /**
     * Get the controller view
     *
     * @throws	\UnexpectedValueException	If the view doesn't implement the ViewInterface
     * @return	ViewInterface
     */
    public function getView();

    /**
     * Set the controller view
     *
     * @param	mixed	$view   An object that implements ObjectInterface, ObjectIdentifier object
     * 					        or valid identifier string
     * @return	ControllerInterface
     */
    public function setView($view);

    /**
     * Get the supported formats
     *
     * @return array
     */
    public function getFormats();
}