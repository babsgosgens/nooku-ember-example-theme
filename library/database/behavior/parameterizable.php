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
 * Database Parameterizable Behavior
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Library\Database
 */
class DatabaseBehaviorParameterizable extends DatabaseBehaviorAbstract
{
    /**
     * The parameters
     *
     * @var ObjectConfigInterface
     */
    protected $_parameters;

    /**
     * The column name
     *
     * @var string
     */
    protected $_column;

    /**
     * Constructor.
     *
     * @param   ObjectConfig $config Configuration options
     */
    public function __construct(ObjectConfig $config = null)
    {
        parent::__construct($config);

        $this->_column = $config->column;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config  An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'row_mixin' => true,
            'column'    => 'parameters'
        ));

        parent::_initialize($config);
    }

    /**
     * Get the parameters
     *
     * By default requires a 'parameters' table column. Column can be configured using the 'column' config option.
     *
     * @return ObjectConfigInterface
     */
    public function getParameters()
    {
        $result = false;

        if($this->hasProperty($this->_column))
        {
            $handle = $this->getMixer()->getHandle();

            if(!isset($this->_parameters[$handle]))
            {
                $type   = (array) $this->getTable()->getColumn($this->_column)->filter;
                $data   = $this->getProperty($this->_column);

                //Create the parameters object
                $config = $this->getObject('object.config.factory')->createFormat($type[0]);

                if(!empty($data))
                {
                    if (is_string($data)) {
                        $config->fromString(trim($data));
                    } else {
                        $config->append($data);
                    }
                }

                $this->_parameters[$handle] = $config;
            }

            $result = $this->_parameters[$handle];
        }

        return $result;
    }

    /**
     * Merge the parameters
     *
     * @param $value
     */
    public function setPropertyParameters($value)
    {
        if(!empty($value))
        {
            if(!is_string($value)) {
                $value = $this->getParameters()->add($value)->toString();
            }
        }

        return $value;
    }

    /**
     * Check if the behavior is supported
     *
     * Behavior requires a 'parameters' table column
     *
     * @return  boolean  True on success, false otherwise
     */
    public function isSupported()
    {
        $mixer = $this->getMixer();
        $table = $mixer instanceof DatabaseRowInterface ?  $mixer->getTable() : $mixer;

        if($table->hasColumn($this->_column))  {
            return true;
        }

        return false;
    }

    /**
     * Insert the parameters
     *
     * @param DatabaseContext	$context A database context object
     * @return void
     */
    protected function _beforeInsert(DatabaseContext $context)
    {
        $method = 'get'.ucfirst($this->_column);
        if($context->data->$method() instanceof ObjectConfigInterface) {
            $context->data->setProperty($this->_column, $context->data->$method()->toString());
        }
    }

    /**
     * Update the parameters
     *
     * @param DatabaseContext	$context A database context object
     * @return void
     */
    protected function _beforeUpdate(DatabaseContext $context)
    {
        $method = 'get'.ucfirst($this->_column);
        if($context->data->$method() instanceof ObjectConfigInterface) {
            $context->data->setProperty($this->_column, $context->data->$method()->toString());
        }
    }

    /**
     * Get the methods that are available for mixin based
     *
     * @param  array $exclude   A list of methods to exclude
     * @return array  An array of methods
     */
    public function getMixableMethods($exclude = array())
    {
        if($this->_column !== 'parameters')
        {
            $exclude += array('getParameters');
            $methods = parent::getMixableMethods($exclude);

            //Add dynamic methods based on the column name
            $methods['get'.ucfirst($this->_column)] = $this;
            $methods['setProperty'.ucfirst($this->_column)] = $this;
        }
        else $methods = parent::getMixableMethods();

        return $methods;
    }

    /**
     * Intercept parameter getter and setter calls
     *
     * @param  string   $method     The function name
     * @param  array    $arguments  The function arguments
     * @throws \BadMethodCallException   If method could not be found
     * @return mixed The result of the function
     */
    public function __call($method, $arguments)
    {
        if($this->_column !== 'parameters')
        {
            //Call getParameters()
            if($method == 'get'.ucfirst($this->_column)) {
                return $this->getParameters();
            }

            //Call setPropertyParameters()
            if($method == 'setProperty'.ucfirst($this->_column)) {
                return $this->setPropertyParameters($arguments[0]);
            }
        }

        return parent::__call($method, $arguments);
    }
}