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
 * Default Model Entity
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Library\Model
 */
final class ModelEntityDefault extends ModelEntityAbstract implements ObjectInstantiable
{
    /**
     * Create an entity or a collection instance
     *
     * @param  ObjectConfigInterface   $config	  A ObjectConfig object with configuration options
     * @param  ObjectManagerInterface	$manager  A ObjectInterface object
     * @return EventPublisher
     */
    public static function getInstance(ObjectConfig $config, ObjectManagerInterface $manager)
    {
        $name = $config->object_identifier->name;

        if(StringInflector::isSingular($name)) {
            $class = 'Nooku\Library\ModelEntityRow';
        } else {
            $class = 'Nooku\Library\ModelEntityRowset';
        }

        $instance = new $class($config);
        return $instance;
    }
}