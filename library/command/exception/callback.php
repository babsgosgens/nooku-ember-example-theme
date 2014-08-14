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
 * Command Callback Exception
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Nooku\Library\Command
 */
class CommandExceptionCallback extends \RuntimeException implements CommandException {}