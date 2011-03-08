<?php
namespace Construct\Controller;
/**
 * An extension of the Zend_Rest_Controller
 */
class Rest extends \Zend_Rest_Controller
{
    /**
     * Init Mehtod - Disables layout.
     * 
     * @return void
     */
    public function init()
    {
        //$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * Delete Method
     *
     * @return void
     */
    public function deleteAction()
    {
        $this->_response->setHttpResponseCode(405);
        $this->_response->setBody('METHOD NOT IMPLEMENTED');
        $this->_response->sendResponse();
    }

    /**
     * Get Method
     *
     * @return void
     */
    public function getAction()
    {
        $this->_response->setHttpResponseCode(405);
        $this->_response->setBody('METHOD NOT IMPLEMENTED');
        $this->_response->sendResponse();
    }

    /**
     * Head Method
     *
     * @return void
     */
    public function headAction()
    {
        $this->_response->setHttpResponseCode(405);
        $this->_response->setBody('METHOD NOT IMPLEMENTED');
        $this->_response->sendResponse();
    }

    /**
     * Index / List Method
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_forward('get');
    }

    /**
     * Post Method
     *
     * @return void
     */
    public function postAction()
    {
        $this->_response->setHttpResponseCode(405);
        $this->_response->setBody('METHOD NOT IMPLEMENTED');
        $this->_response->sendResponse();
    }

    /**
     * Put Method
     *
     * @return void
     */
    public function putAction()
    {
        $this->_response->setHttpResponseCode(405);
        $this->_response->setBody('METHOD NOT IMPLEMENTED');
        $this->_response->sendResponse();
    }

}

