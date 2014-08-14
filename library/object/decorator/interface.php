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
 * Object Decorator Interface
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Nooku\Library\Object
 */
interface ObjectDecoratorInterface extends ObjectHandlable
{
    /**
     * Get the decorated object
     *
     * @return object The decorated object
     */
    public function getDelegate();

    /**
     * Set the decorated object
     *
     * @param   object $delegate The object to decorate
     * @return  ObjectDecoratorInterface
     * @throws  \InvalidArgumentException If the delegate is not an object
     */
    public function setDelegate($delegate);

    /**
     * Get a list of all the available methods
     *
     * This function returns an array of all the public methods, both native and mixed.
     *
     * @return array An array
     */
    public function getMethods();

    /**
     * Decorate Notifier
     *
     * This function is called when an object is being decorated. It will get the delegate passed in.
     *
     * @param object $delegate The object being decorated
     * @return void
     * @throws  \InvalidArgumentException If the delegate is not an object
     */
    public function onDecorate($delegate);
}