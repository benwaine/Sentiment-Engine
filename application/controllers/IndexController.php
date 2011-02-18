<?php
/**
 * Index Controller
 *
 * @package    Sky
 * @subpackage Controller
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class IndexController extends Zend_Controller_Action
{

    /**
     * Init Method
     * 
     * @return void
     */
    public function init()
    {
        
        /* Initialize action controller here */
        $bootstrap         = $this->getInvokeArg('bootstrap');
        $doctrineContainer = $bootstrap->getResource('doctrine');
        $entityManager     = $doctrineContainer->getEntityManager();

        $tweet = new SE\Entity\Tweet();
        $tweet->setTweet('Hello');

        $entityManager->persist($tweet);

        $entityManager->flush();
        
    }

    /**
     * Index Action
     *
     * @return void
     */
    public function indexAction()
    {
        // action body

        
    }


}

