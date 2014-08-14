<?php
/**
 * Nooku Platform - http://www.nooku.org/platform
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-platform for the canonical source repository
 */

namespace Nooku\Component\Pages;

use Nooku\Library;

/**
 * Module Template Helper
 *
 * @author  Johan Janssens <http://github.com/johanjanssens>
 * @package Nooku\Component\Pages
 */
class TemplateHelperModule extends Library\TemplateHelperAbstract
{
    /**
     * Database rowset or identifier
     *
     * @var	string|object
     */
    protected $_modules;

    /**
     * Constructor.
     *
     * @param  Library\ObjectConfig $config An optional Library\ObjectConfig object with configuration options
     */
    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_modules = $config->modules;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   Library\ObjectConfig $config An optional Library\ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'modules' => null,
        ));

        parent::_initialize($config);
    }

    /**
     * Get the modules
     *
     * @throws	\UnexpectedValueException	If the request doesn't implement the Library\ModelEntityInterface
     * @return Library\ModelEntityInterface
     */
    public function getModules()
    {
        if(!$this->_modules instanceof Library\ModelEntityInterface)
        {
            $this->_modules = $this->getObject($this->_modules);

            if(!$this->_modules instanceof Library\ModelEntityInterface)
            {
                throw new \UnexpectedValueException(
                    'Modules: '.get_class($this->_modules).' does not implement Library\ModelEntityInterface'
                );
            }
        }

        return $this->_modules;
    }

    /**
     * Count the modules based on a condition of positions
     *
     * @param  array|string $config
     * @return integer Returns the result of the evaluated condition
     */
    public function count($config = array())
    {
        //Condition is passed as a string
        if(is_string($config)) {
            $config = array('condition' => $config);
        }

        $result = 0;
        if(isset($config['condition']) && !empty($config['condition']))
        {
            $operators = '(\+|\-|\*|\/|==|\!=|\<\>|\<|\>|\<=|\>=|and|or|xor)';
            $words = preg_split('# ' . $operators . ' #', $config['condition'], null, PREG_SPLIT_DELIM_CAPTURE);
            for ($i = 0, $n = count($words); $i < $n; $i += 2)
            {
                // Odd parts (modules)
                $position = strtolower($words[$i]);
                $words[$i] = count($this->getModules()->find(array('position' => $position)));
            }

            $str = 'return ' . implode(' ', $words) . ';';
            $result = eval($str);
        }

        return $result;
    }
}