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
 * Components Class Locator
 *
 * Component class names are case sensitive and uses a Upper Camel Case or Pascal Case naming convention. Component
 * class names must be namespaced where each component has it's own namespace. File and folder names must be lower
 * case.
 *
 * Each folder in the file structure must be represented in the class name.
 *
 * Classname : [Namespace]\[Path][To][File]
 * Location  : namespace/.../path/to/file.php
 *
 * Exceptions
 *
 * 1. An exception is made for files where the last segment of the file path and the file name are the same. In this case
 * class name can use a shorter syntax where the last segment of the path is omitted.
 *
 * Location  : nooku/component/foo/bar/bar.php
 * Classname : Nooku\Component\FooBar instead of Nooku\Component\Foo\BarBar
 *
 * 2. An exception is made for exception class names. Exception class names are only party case sensitive. The part after
 * the word 'Exception' is transformed to lower case.  Exceptions are loaded from the .../Exception folder relative to
 * their path.
 *
 * Classname : [Namespace]\[Path][To]Exception[FileNameForException]
 * Location  : namespace/.../path/to/exception/filenameforexception.php
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Library\Class
 */
class ClassLocatorComponent extends ClassLocatorAbstract
{
    /**
     * The type
     *
     * @var string
     */
    protected $_type = 'component';

    /**
     *  Get a fully qualified path based on a class name
     *
     * @param  string $class     The class name
     * @param  string $classpath The class path
     * @return string|false   Returns canonicalized absolute pathname or FALSE if the class could not be found.
     */
    public function locate($class, $classpath = null)
	{
        //Find the class
        foreach($this->getNamespaces() as $namespace => $basepath)
        {
            if(empty($namespace) && strpos($class, '\\')) {
                continue;
            }

            if(strpos('\\'.$class, '\\'.$namespace) !== 0) {
                continue;
            }

            $class = str_replace(array($namespace, '\\'), '', '\\'.$class);

            /*
             * Exception rule for Exception classes
             *
             * Transform class to lower case to always load the exception class from the /exception/ folder.
             */
            if($pos = strpos($class, 'Exception'))
            {
                $filename  = substr($class, $pos + strlen('Exception'));
                $class = str_replace($filename, ucfirst(strtolower($filename)), $class);
            }

            $parts = explode(' ', strtolower(preg_replace('/(?<=\\w)([A-Z])/', ' \\1', $class)));

            $component = strtolower(array_shift($parts));
            $file 	   = array_pop($parts);

            if(count($parts)){
                $path = implode('/', $parts).'/'.$file;
            } else {
                $path = $file;
            }

            if(!empty($classpath) && empty($namespace)) {
                $basepath = $classpath;
            }

            return $basepath.'/'.$component.'/'.$path.'.php';
        }

		return false;
	}
}