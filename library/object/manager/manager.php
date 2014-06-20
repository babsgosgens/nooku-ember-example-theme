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
 * Object Manager
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Nooku\Library\Object
 */
class ObjectManager implements ObjectInterface, ObjectManagerInterface, ObjectSingleton
{
    /**
     * The object identifier
     *
     * @var ObjectIdentifier
     */
    private $__object_identifier;

    /**
     * The object registry
     *
     * @var ObjectRegistry
     */
    protected $_registry;

    /*
     * The class loader
     *
     * @var ClassLoader
     */
    protected $_loader;

    /**
     * The identifier locators
     *
     * @var array
     */
    protected $_locators;

    /**
     * Constructor
     *
     * Prevent creating instances of this class by making the constructor private
     */
    public function __construct(ObjectConfig $config)
    {
        //Initialise the object
        $this->_initialize($config);

        //Set the class loader
        if (!$config->class_loader instanceof ClassLoaderInterface)
        {
            throw new \InvalidArgumentException(
                'class_loader [ClassLoaderInterface] config option is required, "'.gettype($config->class_loader).'" given.'
            );
        }
        else $this->setClassLoader($config->class_loader);

        //Create the object registry
        if($config->cache_enabled)
        {
            $this->_registry = new ObjectRegistryCache();
            $this->_registry->setNamespace($config->cache_namespace);
        }
        else $this->_registry = new ObjectRegistry();

        //Create the object identifier
        $this->__object_identifier = $this->getIdentifier('object.manager');

        //Manually register the library loader
        $config = new ObjectConfig(array(
            'class_loader'      => $config->class_loader,
            'object_manager'    => $this,
            'object_identifier' => new ObjectIdentifier('lib:object.locator.library')
        ));

        $this->registerLocator(new ObjectLocatorLibrary($config));

        //Register self and set a 'manager' alias
        $this->setObject('object.manager', $this);
        $this->registerAlias('object.manager', 'manager');
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $object An optional ObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'class_loader'     => null,
            'cache_enabled'    => false,
            'cache_namespace'  => 'nooku'
        ));
    }

    /**
     * Clone
     *
     * Prevent creating clones of this class
     */
    final private function __clone()
    {
        throw new \Exception("An instance of ".get_called_class()." cannot be cloned.");
    }

    /**
     * Force creation of a singleton
     *
     * @param  array  $config An optional array with configuration options.
     * @return ObjectManager
     */
    final public static function getInstance($config = array())
    {
        static $instance;

        if ($instance === NULL)
        {
            if (!$config instanceof ObjectConfig) {
                $config = new ObjectConfig($config);
            }

            $instance = new self($config);
        }

        return $instance;
    }

    /**
     * Get an identifier object based on an object identifier.
     *
     * Accepts various types of parameters and returns a valid identifier. Parameters can either be an
     * object that implements ObjectInterface, or a ObjectIdentifier object, or valid identifier
     * string. Function recursively resolves identifier aliases and returns the aliased identifier.
     *
     * If no identifier is passed the object identifier of this object will be returned.
     *
     * @param mixed $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @return ObjectIdentifier
     * @throws ObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function getIdentifier($identifier = null)
    {
        if(isset($identifier))
        {
            if (!is_string($identifier))
            {
                if ($identifier instanceof ObjectInterface) {
                    $identifier = $identifier->getIdentifier();
                }

                if(is_array($identifier)) {
                    $identifier = new ObjectIdentifier($identifier);
                }
            }

            //Get the identifier object
            if (!$result = $this->_registry->find($identifier))
            {
                if (is_string($identifier)) {
                    $result = new ObjectIdentifier($identifier);
                } else {
                    $result = $identifier;
                }

                $this->_registry->set($result);
            }
        }
        else $result = $this->__object_identifier;

        return $result;
    }

    /**
     * Get the identifier class
     *
     * @param mixed $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @param bool  $fallback   Use fallbacks when locating the class. Default is TRUE.
     * @return string|false  Returns the class name or false if the class could not be found.
     */
    public function getClass($identifier, $fallback = true)
    {
        $identifier = $this->getIdentifier($identifier);
        return $this->_locate($identifier, $fallback);
    }

    /**
     * Set the identifier class
     *
     * @param mixed  $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @param string $class      The class name
     * @return string
     */
    public function setClass($identifier, $class)
    {
        if(!$this->isRegistered($identifier))
        {
            $identifier = $this->getIdentifier($identifier);
            $identifier->setClass($class);

            //Re-set the registry
            $this->_registry->set($identifier);
        }

        return $this;
    }

    /**
     * Get an object instance based on an object identifier
     *
     * If the object implements the ObjectSingleton interface the object will be automatically registered in the
     * object registry.
     *
     * If the object implements the ObjectInstantiable interface the manager will delegate object instantiation
     * to the object itself.
     *
     * @param	string|object	$identifier  An ObjectIdentifier or identifier string
     * @param	array  			$config     An optional associative array of configuration settings.
     * @return	ObjectInterface  Return object on success, throws exception on failure
     * @throws  ObjectExceptionInvalidIdentifier   If the identifier is not valid
     * @throws	ObjectExceptionInvalidObject	  If the object doesn't implement the ObjectInterface
     * @throws  ObjectExceptionNotFound           If object cannot be loaded
     * @throws  ObjectExceptionNotInstantiated    If object cannot be instantiated
     * @return  object  Return object on success, throws exception on failure
     */
    public function getObject($identifier, array $config = array())
    {
        $identifier = $this->getIdentifier($identifier);

        if (!$this->isRegistered($identifier))
        {
            //Instantiate the object
            $instance = $this->_instantiate($identifier, $config);

            //Mix the object
            $instance = $this->_mixin($identifier, $instance);

            //Decorate the object
            $instance = $this->_decorate($identifier, $instance);

            //Auto register the object
            if($identifier->isMultiton()) {
                $this->setObject($identifier, $instance);
            }
        }
        else $instance = $this->_registry->get($identifier);

        return $instance;
    }

    /**
     * Register an object instance for a specific object identifier
     *
     * @param string|object	 $identifier  The identifier string or identifier object
     * @param ObjectInterface $object     An object that implements ObjectInterface
     * @return ObjectManager
     * @throws ObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function setObject($identifier, ObjectInterface $object)
    {
        $identifier = $this->getIdentifier($identifier);
        $this->_registry->set($identifier, $object);

        return $this;
    }

    /**
     * Set the configuration options for an identifier
     *
     * @param mixed  $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @param array $config      An associative array of configuration options
     * @param  boolean  $merge  If TRUE the data in $config will be merged instead of replaced. Default TRUE.
     * @return ObjectManager
     * @throws ObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function setConfig($identifier, $config = array(), $merge = true)
    {
        $identifier = $this->getIdentifier($identifier);
        $identifier->setConfig($config, $merge);

        return $this;
    }

    /**
     * Get the object configuration
     *
     * @param mixed  $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @return ObjectConfig
     * @throws ObjectExceptionInvalidIdentifier  If the identifier is not valid
     */
    public function getConfig($identifier = null)
    {
        $config = $this->getIdentifier($identifier)->getConfig();
        return $config;
    }

    /**
     * Get the class registry object
     *
     * @return ObjectRegistryInterface
     */
    public function getRegistry()
    {
        return $this->_registry;
    }

    /**
     * Register a mixin for an identifier
     *
     * The mixin is mixed when the identified object is first instantiated see {@link get} The mixin is also mixed with
     * with the represented by the identifier if the object is registered in the object manager. This mostly applies to
     * singletons but can also apply to other objects that are manually registered.
     *
     * @param mixed $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @param mixed $mixin      An ObjectIdentifier, identifier string or object implementing ObjectMixinInterface
     * @param array $config     Configuration for the mixin
     * @return ObjectManagerInterface
     * @throws ObjectExceptionInvalidIdentifier If the identifier is not valid
     * @see Object::mixin()
     */
    public function registerMixin($identifier, $mixin, array $config = array())
    {
        $identifier = $this->getIdentifier($identifier);
        $identifier->addMixin($mixin, $config);

        //If the identifier already exists mixin the mixin
        if ($this->isRegistered($identifier))
        {
            $mixer = $this->_registry->get($identifier);
            $this->_mixin($identifier, $mixer);
        }

        return $this;
    }

    /**
     * Register a decorator  for an identifier
     *
     * The object is decorated when it's first instantiated see {@link get} The object represented by the identifier is
     * also decorated if the object is registered in the object manager. This mostly applies to singletons but can also
     * apply to other objects that are manually registered.
     *
     * @param mixed $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @param mixed $decorator  An ObjectIdentifier, identifier string or object implementing ObjectDecoratorInterface
     * @param array $config     Configuration for the decorator
     * @return ObjectManager
     * @throws ObjectExceptionInvalidIdentifier If the identifier is not valid
     * @see Object::decorate()
     */
    public function registerDecorator($identifier, $decorator, array $config = array())
    {
        $identifier = $this->getIdentifier($identifier);
        $identifier->addDecorator($decorator);

        //If the identifier already exists decorate it
        if ($this->isRegistered($identifier))
        {
            $delegate = $this->_registry->get($identifier);
            $this->_decorate($identifier, $delegate);
        }

        return $this;
    }

    /**
     * Register an object locator
     *
     * @param mixed $identifier An ObjectIdentifier, identifier string or object implementing ObjectLocatorInterface
     * @return ObjectManager
     * @throws ObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function registerLocator($identifier, array $config = array())
    {
        if(!$identifier instanceof ObjectLocatorInterface)
        {
            $config['class_loader'] = $this->getClassLoader();
            $locator = $this->getObject($identifier, $config);

            if(!$locator instanceof ObjectLocatorInterface)
            {
                throw new \UnexpectedValueException(
                    'Locator: '.get_class($locator).' does not implement ObjectLocatorInterface'
                );
            }
        }
        else $locator = $identifier;

        //Add the locator
        $this->_locators[$locator->getType()] = $locator;

        return $this;
    }

    /**
     * Get a registered object locator based on his type
     *
     * @param string $type The locator type
     * @return ObjectLocatorInterface|null  Returns the object locator or NULL if it cannot be found.
     */
    public function getLocator($type)
    {
        $result = null;

        if(isset($this->_locators[$type])) {
            $result = $this->_locators[$type];
        }

        return $result;
    }

    /**
     * Get the registered class locators
     *
     * @return array
     */
    public function getLocators()
    {
        return $this->_locators;
    }

    /**
     * Register an alias for an identifier
     *
     * @param mixed  $identifier The class identifier or identifier object
     * @param string $alias      The alias
     * @return ObjectManagerInterface
     * @throws ObjectExceptionInvalidIdentifier If the identifier is not valid
     */
    public function registerAlias($identifier, $alias)
    {
        $identifier = $this->getIdentifier($identifier);
        $alias      = $this->getIdentifier($alias);

        //Register the alias for the identifier
        $this->_registry->alias($identifier, (string) $alias);

        //Merge alias configuration into the identifier
        $identifier->getConfig()->append($alias->getConfig());

        return $this;
    }

    /**
     * Get the aliases for an identifier
     *
     * @param mixed $identifier An ObjectIdentifier, identifier string or object implementing ObjectInterface
     * @return array   An array of aliases
     */
    public function getAliases($identifier)
    {
        return array_search((string) $identifier, $this->_registry->getAliases());
    }

    /**
     * Get the class loader
     *
     * @return ClassLoaderInterface
     */
    public function getClassLoader()
    {
        return $this->_loader;
    }

    /**
     * Set the class loader
     *
     * @param ClassLoaderInterface $loader
     * @return ObjectManagerInterface
     */
    public function setClassLoader(ClassLoaderInterface $loader)
    {
        $this->_loader = $loader;
        return $this;
    }

    /**
     * Check if an object instance was registered for the identifier
     *
     * @param mixed $identifier An object that implements the ObjectInterface, an ObjectIdentifier or valid identifier string
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public function isRegistered($identifier)
    {
        try
        {
            $object = $this->_registry->get($this->getIdentifier($identifier));

            //If the object implements ObjectInterface we have registered an object
            if($object instanceof ObjectInterface) {
                $result = true;
            } else {
                $result = false;
            }

        } catch (ObjectExceptionInvalidIdentifier $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * Check if the object is a multiton
     *
     * @param mixed $identifier An object that implements the ObjectInterface, an ObjectIdentifier or valid identifier string
     * @return boolean Returns TRUE if the object is a singleton, FALSE otherwise.
     */
    public function isMultiton($identifier)
    {
        try {
            $result = $this->getIdentifier($identifier)->isMultiton();
        } catch (ObjectExceptionInvalidIdentifier $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * Check if the object is a singleton
     *
     * @param mixed $identifier An object that implements the ObjectInterface, an ObjectIdentifier or valid identifier string
     * @return boolean Returns TRUE if the object is a singleton, FALSE otherwise.
     */
    public function isSingleton($identifier)
    {
        try {
            $result = $this->getIdentifier($identifier)->isSingleton();
        } catch (ObjectExceptionInvalidIdentifier $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * Perform the actual mixin of all registered mixins for an object
     *
     * @param  ObjectIdentifier $identifier
     * @param  ObjectMixable    $mixer
     * @return ObjectMixable    The mixed object
     */
    protected function _mixin(ObjectIdentifier $identifier, $mixer)
    {
        if ($mixer instanceof ObjectMixable)
        {
            $mixins = $identifier->getMixins();

            foreach ($mixins as $key => $value)
            {
                if (is_numeric($key)) {
                    $mixer->mixin($value);
                } else {
                    $mixer->mixin($key, $value);
                }
            }
        }

        return $mixer;
    }

    /**
     * Perform the actual decoration of all registered decorators for an object
     *
     * @param ObjectIdentifier  $identifier
     * @param ObjectDecoratable $delegate
     * @return ObjectDecorator  The decorated object
     */
    protected function _decorate(ObjectIdentifier $identifier, $delegate)
    {
        if ($delegate instanceof ObjectDecoratable)
        {
            $decorators = $identifier->getDecorators();

            foreach ($decorators as $key => $value)
            {
                if (is_numeric($key)) {
                    $delegate = $delegate->decorate($value);
                } else {
                    $delegate = $delegate->decorate($key, $value);
                }
            }
        }

        return $delegate;
    }

    /**
     * Configure an identifier
     *
     * @param ObjectIdentifier $identifier
     * @param array $config
     * @return ObjectConfig
     */
    protected function _configure(ObjectIdentifier $identifier, array $data = array())
    {
        //Prevent config settings from being stored in the identifier
        $config = clone $identifier->getConfig();

        //Merge the config data
        $config->append($data);

        //Set the service container and identifier
        $config->object_manager    = $this;
        $config->object_identifier = $identifier;

        return $config;
    }

    /**
     * Get an instance of a class based on a class identifier
     *
     * @param ObjectIdentifier $identifier
     * @param bool             $fallback   Use fallbacks when locating the class. Default is TRUE.
     * @return  string  Return the identifier class or FALSE on failure.
     */
    protected function _locate(ObjectIdentifier $identifier, $fallback = true)
    {
        $class = $identifier->getClass();

        //Set the basepath
        $this->getClassLoader()->setBasepath($identifier->domain);

        //If the class is FALSE we have tried to locate it already, do not locate it again.
        if(empty($class) && $class !== false)
        {
            $class = $this->_locators[$identifier->getType()]->locate($identifier, $fallback);

            //If we are falling back set the class in the identifier.
            if($fallback) {
                $this->setClass($identifier, $class);
            }
        }

        return $class;
    }

    /**
     * Get an instance of a class based on a class identifier
     *
     * @param   ObjectIdentifier $identifier
     * @param   array            $config    An optional associative array of configuration settings.
     * @throws	ObjectExceptionInvalidObject	  If the object doesn't implement the ObjectInterface
     * @throws  ObjectExceptionNotFound           If object cannot be loaded
     * @throws  ObjectExceptionNotInstantiated    If object cannot be instantiated
     * @return  object  Return object on success, throws exception on failure
     */
    protected function _instantiate(ObjectIdentifier $identifier, array $config = array())
    {
        $result = null;

        //Get the class name and set it in the identifier
        $class = $this->_locate($identifier);

        if($class && class_exists($class))
        {
            if (!array_key_exists(__NAMESPACE__.'\ObjectInterface', class_implements($identifier->class, false)))
            {
                throw new ObjectExceptionInvalidObject(
                    'Object: '.$identifier->class.' does not implement ObjectInterface'
                );
            }

            //Configure the identifier
            $config = $this->_configure($identifier, $config);

            // Delegate object instantiation.
            if (array_key_exists(__NAMESPACE__.'\ObjectInstantiable', class_implements($identifier->class, false))) {
                $result = call_user_func(array($identifier->class, 'getInstance'), $config, $this);
            } else {
                $result = new $identifier->class($config);
            }

            //Thrown an error if no object was instantiated
            if (!is_object($result))
            {
                throw new ObjectExceptionNotInstantiated(
                    'Cannot instantiate object from identifier: ' . $identifier->class
                );
            }
        }
        else throw new ObjectExceptionNotFound('Cannot load object from identifier: '. $identifier);

        return $result;
    }
}