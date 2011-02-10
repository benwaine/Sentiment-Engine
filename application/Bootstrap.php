<?php

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

}

