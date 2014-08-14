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
 * Controller Toolbar Command Interface
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Nooku\Library\Controller
 */
interface ControllerToolbarCommandInterface extends ControllerToolbarInterface
{
    /**
     * Constructor.
     *
     * @param	string $name The command name
     * @param   array|ObjectConfig 	An associative array of configuration settings or a ObjectConfig instance.
     */
    public function __construct( $name, $config = array());

    /**
     * Get the parent command
     *
     * @return	ControllerToolbarCommandInterface
     */
    public function getParent();

    /**
     * Set the parent command
     *
     * @param ControllerToolbarCommandInterface $command The parent command
     * @return ControllerToolbarCommandInterface
     */
    public function setParent(ControllerToolbarCommandInterface $command );

    /**
     * Get the toolbar object
     *
     * @return ControllerToolbarInterface
     */
    public function getToolbar();

    /**
     * Set the parent node
     *
     * @param object $node The toolbar this command belongs too
     * @return ControllerToolbarCommand
     */
    public function setToolbar(ControllerToolbarInterface $toolbar );
}