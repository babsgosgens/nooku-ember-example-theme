<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-platform for the canonical source repository
 */

namespace Nooku\Component\Application;

use Nooku\Library;

/**
 * Toolbar Template Helper
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Nooku\Component\Application
 */
class TemplateHelperActionbar extends Library\TemplateHelperAbstract
{
    /**
     * Render the toolbar
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function render($config = array())
    {
        $config = new Library\ObjectConfig($config);
        $config->append(array(
        	'toolbar' => null,
            'attribs' => array()
        ));

        $html  = '<ul '.$this->buildAttributes($config->attribs).'>';
	    foreach ($config->toolbar->getCommands() as $command)
	    {
            $name = $command->getName();

            $html .= '<li>';
	        if(method_exists($this, $name)) {
                $html .= $this->$name(array('command' => $command));
            } else {
                $html .= $this->command(array('command' => $command));
            }
            $html .= '</li>';
       	}
		$html .= '</ul>';

		return $html;
    }

    /**
     * Render a actionbar command
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function command($config = array())
    {
        $config = new Library\ObjectConfig($config);
        $config->append(array(
        	'command' => array()
        ));

        $command = $config->command;

        //Create the id
        $command->attribs['id'] = 'command-'.$command->id;

        //Add a disabled class if the command is disabled
        if($command->disabled) {
            $command->attribs->class->append(array('disabled'));
        }

        //Create the href
        if(!empty($command->href)) {
            $command->attribs['href'] = $this->getTemplate()->route($command->href);
        }

        $html  = '<a '.$this->buildAttributes($command->attribs).'>';
       	$html .= $this->getObject('translator')->translate($command->label);
       	$html .= '</a>';

    	return $html;
    }
}