<?php

/**
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

// Zend Framework cannot deal with Resources using namespaces
//namespace Bisna\Application\Resource;

use Bisna\Application\Container;

/**
 * Zend Application Resource Doctrine class
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link www.doctrine-project.org
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 */
class Bisna_Application_Resource_Doctrine extends \Zend_Application_Resource_ResourceAbstract
{
    /**
     * Initializes Doctrine Context.
     *
     * @return Core\Application\Container\DoctrineContainer
     */
    public function init()
    {
        $config = $this->getOptions();
        
        // Bootstrapping Doctrine autoloaders
        $this->registerAutoloaders($config);
        
        // Starting Doctrine container
        $container = new Container\DoctrineContainer($config);

        // Add to Zend Registry
        \Zend_Registry::set('doctrine', $container);

        return $container;
    }

    /**
     * Register Doctrine autoloaders
     *
     * @param array Doctrine global configuration
     */
    private function registerAutoloaders(array $config = array())
    {
        $autoloader = \Zend_Loader_Autoloader::getInstance();
        $doctrineIncludePath = isset($config['includePath'])
            ? $config['includePath'] : APPLICATION_PATH . '/../library/Doctrine';

        require_once $doctrineIncludePath . '/Common/ClassLoader.php';

        $symfonyAutoloader = new \Doctrine\Common\ClassLoader('Symfony');
        $autoloader->pushAutoloader(array($symfonyAutoloader, 'loadClass'), 'Symfony');
        
        $doctrineExtensionsAutoloader = new \Doctrine\Common\ClassLoader('DoctrineExtensions');
        $autoloader->pushAutoloader(array($doctrineExtensionsAutoloader, 'loadClass'), 'DoctrineExtensions');


        $doctrineAutoloader = new \Doctrine\Common\ClassLoader('Doctrine');
        $autoloader->pushAutoloader(array($doctrineAutoloader, 'loadClass'), 'Doctrine');
    }
}
