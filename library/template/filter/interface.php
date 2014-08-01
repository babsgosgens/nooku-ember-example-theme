<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Library;

/**
 * Template Filter Interface
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Nooku\Library\Template
 */
interface TemplateFilterInterface extends ObjectHandlable
{
    /**
     * Filter modes
     */
    const MODE_COMPILE  = 1;
    const MODE_RENDER   = 2;

    /**
     * Priority levels
     */
    const PRIORITY_HIGHEST = 1;
    const PRIORITY_HIGH    = 2;
    const PRIORITY_NORMAL  = 3;
    const PRIORITY_LOW     = 4;
    const PRIORITY_LOWEST  = 5;

    /**
     * Get the template object
     *
     * @return  TemplateInterface	The template object
     */
    public function getTemplate();

    /**
     * Set the template object
     *
     * @return  TemplateInterface $template	The template object
     */
    public function setTemplate(TemplateInterface $template);

    /**
     * Get the priority of the filter
     *
     * @return  integer The priority level
     */
    public function getPriority();

    /**
     * Method to extract key/value pairs out of a string with xml style attributes
     *
     * @param   string  $string String containing xml style attributes
     * @return  array   Key/Value pairs for the attributes
     */
    public function parseAttributes($string);

    /**
     * Method to build a string with xml style attributes from  an array of key/value pairs
     *
     * @param   mixed   $array The array of Key/Value pairs for the attributes
     * @return  string  String containing xml style attributes
     */
    public function buildAttributes($array);
}