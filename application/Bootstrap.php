<?php

/**
 * Bootstraps the application
 *
 * @packge Bootstrap
 * @author Ben Waine <ben@ben-waine.co.uk>
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function _initAutoloaderNamespaces()
    {
        require_once APPLICATION_PATH . '/../library/Doctrine/Common/ClassLoader.php';

        $autoloader = \Zend_Loader_Autoloader::getInstance();
        $fmmAutoloader = new \Doctrine\Common\ClassLoader('Bisna');
        $autoloader->pushAutoloader(array($fmmAutoloader, 'loadClass'), 'Bisna');

        $appAutoloader = new \Doctrine\Common\ClassLoader('SE');
        $autoloader->pushAutoloader(array($appAutoloader, 'loadClass'), 'SE');

        $appAutoloader = new \Doctrine\Common\ClassLoader('Construct');
        $autoloader->pushAutoloader(array($appAutoloader, 'loadClass'), 'Construct');
    }

    public function _initModules()
    {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');

        $front->setControllerDirectory(array(
            'default' => APPLICATION_PATH . '/controllers',
            'api' => APPLICATION_PATH . '/modules/api/controllers'
        ));
    }

    /**
     * Set up custom routes.
     *
     * @return void
     */
    public function _initRoutes()
    {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');

        $restRoute = new Zend_Rest_Route($front, array(), array('api'));

        $front->getRouter()->addRoute('rest', $restRoute);
    }

}

