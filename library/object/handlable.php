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
 * Object Handlable interface
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Nooku\Library\Object
 */
interface ObjectHandlable
{
	/**
	 * Get the object handle
	 *
	 * This function returns an unique identifier for the object. This id can be used as a hash key for storing objects
     * or for identifying an object
	 *
	 * @return string A string that is unique, or NULL
	 */
	public function getHandle();
}