<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

namespace Nooku\Component\Pages;

use Nooku\Library;

/**
 * Types Model
 *
 * @author  Gergo Erdosi <http://nooku.assembla.com/profile/gergoerdosi>
 * @package Nooku\Component\Pages
 */
class ModelTypes extends Library\ModelAbstract
{
    protected $_rowset;
    
    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);
        
        $this->getState()->insert('application', 'word');
    }

    public function getRowset()
    {
        if(!isset($this->_rowset))
        {
            $components = array();

            $app_path  = $this->getObject('manager')->getClassLoader()->getBasepath($this->getState()->application);
            $com_path  = $app_path.'/component';

            foreach(new \DirectoryIterator($com_path) as $component)
            {
                $views = array();

                $view_path = $com_path.'/'.$component.'/view';
                if($component->isDir() && substr($component, 0, 1) !== '.' && is_dir($view_path))
                {
                    foreach(new \DirectoryIterator($view_path) as $view)
                    {
                        $xml_path = $view_path.'/'.$view.'/metadata.xml';
                        if($view->isDir() && substr($view, 0, 1) !== '.' && is_file($xml_path))
                        {
                            $xml_view = simplexml_load_file($xml_path);
                            if(strtolower($xml_view->view->attributes()->hidden) !== 'true')
                            {
                                $layouts = array();

                                $tmpl_path = $view_path.'/'.$view.'/templates';
                                if(is_dir($tmpl_path))
                                {
                                    foreach(new \DirectoryIterator($tmpl_path) as $layout)
                                    {
                                        if($layout->isFile() && substr($layout, 0, 1) !== '.' && $layout->getExtension() == 'xml')
                                        {
                                            $xml_layout = simplexml_load_file($tmpl_path.'/'.$layout);
                                            if(!$xml_layout->layout) {
                                                continue;
                                            }

                                            if(strtolower($xml_layout->layout->attributes()->hidden) !== 'true')
                                            {
                                                $layouts[$layout->getBasename('.xml')] = (object) array(
                                                    'name'        => $layout->getBasename('.xml'),
                                                    'title'       => trim($xml_layout->layout->attributes()->title),
                                                    'description' => trim($xml_layout->layout->message)
                                                );
                                            }
                                        }
                                    }
                                }

                                $views[$view->getFilename()] = (object) array(
                                    'name'      => $view->getFilename(),
                                    'title'     => trim($xml_view->view->attributes()->title),
                                    'layouts'   => $layouts
                                );
                            }
                        }
                    }

                    $components[] = array(
                        'name'  => 'com_'.$component->getFilename(),
                        'title' => ucfirst($component->getFilename()),
                        'views' => $views
                    );
                }
            }

            $this->_rowset = $this->getObject('com:pages.database.rowset.types', array('data' => $components));
        }

        return $this->_rowset;
    }
}
