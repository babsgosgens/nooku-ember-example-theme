<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

use Nooku\Library;
use Nooku\Component\Application;

/**
 * Bootstrapper
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Component\Application
 */
class ApplicationBootstrapper extends Application\Bootstrapper
{
     protected function _initialize(Library\ObjectConfig $config)
     {
         $config->append(array(
             'priority' => self::PRIORITY_LOW,
             'aliases'  => array(
                 'application.languages'          => 'com:application.model.entity.languages',
                 'application.pages'              => 'com:application.model.entity.pages',
                 'application.modules'            => 'com:application.model.entity.modules',
                 'lib:template.locator.component' => 'com:application.template.locator.component',
             ),
         ));

         parent::_initialize($config);
     }
}