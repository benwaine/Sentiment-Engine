<?php
namespace SE\Infrastructure;
/**
 * The TSE Service Container.
 * Utilises Symfony's dependancy injection container to provide instances of the
 * various service classes utilized by TSE.
 *
 * @package    Infrastructure
 * @subpackage DI Container
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class ServiceContainer
{

    public static function getContainer($configOptions)
    {

        $sc = new \sfServiceContainerBuilder($configOptions);

        
       
    }

}

