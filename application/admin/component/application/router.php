<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-platform for the canonical source repository
 */

use Nooku\Library;

/**
 * Router
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Component\Application
 */
class ApplicationRouter extends Library\DispatcherRouter
{
    public function parse(Library\HttpUrlInterface $url)
	{
        $vars = array();
        $path = trim($url->getPath(), '/');

        //Remove base path
        $path = substr_replace($path, '', 0, strlen($this->getObject('request')->getBaseUrl()->getPath()));

        // Set the format
        if(!empty($url->format)) {
            $url->query['format'] = $url->format;
        }

        //Parse site route
        $url->query['site'] = $this->getObject('application')->getSite();

        $path = str_replace($url->query['site'], '', $path);
        $path = ltrim($path, '/');

        //Parse component route
        if(!empty($path))
        {
            $segments = explode('/', $path);

            if(isset($segments[0]))
            {
                $vars['component'] = $segments[0];

                if(isset($segments[1])) {
                    $vars['view']   = $segments[1];
                } else {
                    $vars['view']   = $segments[0];
                }
            }
        }

        $url->query = array_merge($url->query, $vars);
        $url->path  = '';

        return true;
	}

	public function build(Library\HttpUrlInterface $url)
	{
        $query    = $url->query;
        $segments = array();

        //Build site route
        $site = $this->getObject('application')->getSite();
        if($site != 'default' && $site != $this->getObject('application')->getRequest()->getUrl()->toString(Library\HttpUrl::HOST)) {
            $segments[] = $site;
        }

	    //Build component route
        if(isset($query['component']))
        {
            $segments[] = $query['component'];
            unset($query['component']);

            if(isset($query['view']))
            {
                if($query['view'] != $segments[0]) {
                    $segments[] = $query['view'];
                }

                unset($query['view']);
            }
        }

        $url->query = $query;

        //Add the format to the uri
        if(isset($url->query['format']))
        {
            $format = $url->query['format'];

            if($format != 'html') {
                $url->format = $format;
            }

            unset($url->query['format']);
        }

        //Build the route
        $url->path = $this->getObject('request')->getBaseUrl()->getPath().'/'. implode('/', $segments);

        // Removed unused query variables
        unset($url->query['Itemid']);
        unset($url->query['component']);

        return true;
	}
}
