<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright      Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link           git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

use Nooku\Library;
use Nooku\Component\Application;

/**
 * Http Dispatcher
 *
 * @author  Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package Component\Application
 */
class ApplicationDispatcherHttp extends Application\DispatcherHttp
{
    /**
     * The site identifier.
     *
     * @var string
     */
    protected $_site;

    /**
     * Constructor.
     *
     * @param Library\ObjectConfig $config An optional Library\ObjectConfig object with configuration options.
     */
    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        //Set the site name
        if (empty($config->site)) {
            $this->_site = $this->getSite();
        } else {
            $this->_site = $config->site;
        }

        $this->loadConfig();

        $this->addCommandCallback('before.run', 'loadLanguage');
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param    Library\ObjectConfig $config An optional Library\ObjectConfig object with configuration options.
     *
     * @return    void
     */
    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'base_url' => '/administrator',
            'site'     => null,
            'options'  => array(
                'config_file' => JPATH_ROOT . '/config/config.php',
                'language'    => null,
                'theme'       => 'default'
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Run the application
     *
     * @param Library\DispatcherContextInterface $context A dispatcher context object
     */
    protected function _actionRun(Library\DispatcherContextInterface $context)
    {
        //Set the site error reporting
        $this->getObject('exception.handler')->setErrorLevel($this->getCfg('debug_mode'));

        define('JPATH_FILES', JPATH_SITES . '/' . $this->getSite() . '/files');
        define('JPATH_CACHE', $this->getCfg('cache_path', JPATH_ROOT . '/cache'));

        // Set timezone to user's setting, falling back to global configuration.
        $timezone = new \DateTimeZone($context->user->get('timezone', $this->getCfg('timezone')));
        date_default_timezone_set($timezone->getName());

        //Route the request
        $this->route();
    }

    /**
     * Route the request
     *
     * @param Library\DispatcherContextInterface $context A dispatcher context object
     */
    protected function _actionRoute(Library\DispatcherContextInterface $context)
    {
        $url = clone $context->request->getUrl();

        //Parse the route
        $this->getRouter()->parse($url);

        //Set the request
        $context->request->query->add($url->query);

        //Forward the request
        $component = substr($context->request->query->get('option', 'cmd', 'com_dashboard'), 4);
        $this->forward($component);
    }

    /**
     * Load the configuration
     *
     * @return    void
     */
    public function loadConfig()
    {
        // Check if the site exists
        if ($this->getObject('com:sites.model.sites')->fetch()->find($this->getSite())) {
            //Load the application config settings
            JFactory::getConfig()->loadArray($this->getConfig()->options->toArray());

            //Load the global config settings
            require_once($this->getConfig()->options->config_file);
            JFactory::getConfig()->loadObject(new JConfig());

            //Load the site config settings
            require_once(JPATH_SITES . '/' . $this->getSite() . '/config/config.php');
            JFactory::getConfig()->loadObject(new JSiteConfig());

        } else throw new Library\ControllerExceptionResourceNotFound('Site :' . $this->getSite() . ' not found');
    }

    /**
     * Load the user session or create a new one
     *
     * @return    void
     */
    public function getUser()
    {
        if (!$this->_user instanceof Library\UserInterface) {
            $user    = parent::getUser();
            $session = $user->getSession();

            //Re-create the session if we changed sites
            if ($user->isAuthentic() && ($session->site != $this->getSite())) {
                //@TODO : Fix this
                //if(!$this->getObject('com:users.controller.session')->add()) {
                //    $session->destroy();
                //}
            }
        }

        return parent::getUser();
    }

    /**
     * Get the application languages.
     *
     * @return ApplicationDatabaseRowsetLanguages
     */
    public function loadLanguage(Library\DispatcherContextInterface $context)
    {
        $languages = $this->getObject('application.languages');
        $language  = null;

        // If a language was specified it has priority.
        if ($iso_code = $this->getConfig()->options->language) {
            $result = $languages->find(array('iso_code' => $iso_code));
            if (count($result) == 1) {
                $language = $result->top();
            }
        }

        // Otherwise use user language setting.
        if (!$language && $iso_code = $context->user->get('language')) {
            $result = $languages->find(array('iso_code' => $iso_code));
            if (count($result) == 1) {
                $language = $result->top();
            }
        }

        // If language still not set, use the primary.
        if (!$language) {
            $language = $languages->getPrimary();
        }

        $languages->setActive($language);

        $translator = $this->getObject('translator', array('locale' => $language->iso_code));

        // Load Framework translations.
        $source = 'lib.' . $translator->getLocale();

        if (!$translator->getCatalogue()->isLoaded($source))
        {
            if (($file = $translator->find(JPATH_ROOT . '/library/resources/language/')) && !$translator->load($file, true))
            {
                throw new \RuntimeException('Unable to load framework translations');
            }

            $translator->getCatalogue()->setLoaded($source);
        }

        // Load application translations.
        $translator->import('application');

        // TODO: Remove this.
        JFactory::getConfig()->setValue('config.language', $language->iso_code);
    }

    /**
     * Get the application router.
     *
     * @param  array $options An optional associative array of configuration options.
     *
     * @return    \ApplicationRouter
     */
    public function getRouter(array $options = array())
    {
        $router = $this->getObject('com:application.router', $options);

        return $router;
    }

    /**
     * Gets a configuration value.
     *
     * @param    string $name    The name of the value to get.
     * @param    mixed  $default The default value
     *
     * @return    mixed    The user state.
     */
    public function getCfg($name, $default = null)
    {
        return JFactory::getConfig()->getValue('config.' . $name, $default);
    }

    /**
     * Get the theme
     *
     * @return string The theme name
     */
    public function getTheme()
    {
        return $this->getConfig()->options->theme;
    }

    /**
     * Gets the name of site
     *
     * This function tries to get the site name based on the information present in the request. If no site can be found
     * it will return 'default'.
     *
     * @param  boolean $reparse Reparse the site name from the request url
     *
     * @return string  The site name
     */
    public function getSite($reparse = false)
    {
        if (!$this->_site || $reparse) {
            // Check URL host
            $uri = clone($this->getRequest()->getUrl());

            $host = $uri->getHost();
            if (!$this->getObject('com:sites.model.sites')->fetch()->find($host)) {
                // Check folder
                $base = $this->getRequest()->getBaseUrl()->getPath();
                $path = trim(str_replace($base, '', $uri->getPath()), '/');
                if (!empty($path)) {
                    $site = array_shift(explode('/', $path));
                } else {
                    $site = 'default';
                }

                //Check if the site can be found, otherwise use 'default'
                if (!$this->getObject('com:sites.model.sites')->fetch()->find($site)) {
                    $site = 'default';
                }

            } else $site = $host;

            $this->_site = $site;
        }

        return $this->_site;
    }
}
