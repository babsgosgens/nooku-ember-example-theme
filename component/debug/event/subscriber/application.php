<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Debug;

use Nooku\Library;

/**
 * Application Event Subscriber
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Component\Debug
 */
class EventSubscriberApplication extends Library\EventSubscriberAbstract
{
    public function __construct(Library\ObjectConfig $config)
	{
	    //Intercept the events for profiling
	    if($this->getObject('application')->getCfg('debug'))
	    {
	        //Profile the event dispatcher
	        $this->getObject('event.dispatcher')->decorate('event.profiler');
	          
	        //Trace database queries
	        $this->getObject('event.dispatcher')->addEventSubscriber('com:debug.event.subscriber.database');
		}
		
		parent::__construct($config);
	}
}