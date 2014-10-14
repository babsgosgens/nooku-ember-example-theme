<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-platform for the canonical source repository
 */

namespace Nooku\Component\Files;

use Nooku\Library;

/**
 * Path Filter
 *
 * @author  Ercan Ozkaya <http://github.com/ercanozkaya>
 * @package Nooku\Component\Files
 */
class FilterPath extends Library\FilterAbstract implements Library\FilterTraversable
{
    protected static $_safepath_pattern = array('#(\.){2,}#', '#^\.#');

    protected static $_special_chars = array(
        "?", "[", "]", "\\", "=", "<", ">", ":", ";", "'", "\"",
        "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}"
    );

    /**
     * Validate a value
     *
     * @param	scalar	$value Value to be validated
     * @return	bool	True when the variable is valid
     */
    public function validate($value)
    {
        $value     = trim(str_replace('\\', '/', $value));
        $sanitized = $this->sanitize($value);

        return (is_string($value) && $sanitized == $value);
    }

    /**
     * Sanitize a value
     *
     * @param	mixed	$value Value to be sanitized
     * @return	string
     */
    public function sanitize($value)
    {
        $value = trim(str_replace('\\', '/', $value));
        $value = preg_replace(self::$_safepath_pattern, '', $value);

		return $value;
    }
}
