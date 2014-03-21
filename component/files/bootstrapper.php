<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Files;

use Nooku\Library;

/**
 * Bootstrapper
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Component\Files
 */
 class Bootstrapper extends Library\ObjectBootstrapperComponent
{
     protected function _initialize(Library\ObjectConfig $config)
     {
         $config->append(array(
             'priority' => self::PRIORITY_LOW,
             'aliases'  => array(
                 'com:files.database.rowset.directories'  => 'com:files.database.rowset.folders',
                 'com:files.database.row.directory'       => 'com:files.database.row.folder',
             ),
             'namespaces' => array(
                 'standard' => array('Imagine' =>  JPATH_VENDOR.'/imagine/imagine/lib')
             )
         ));

         parent::_initialize($config);
     }
}