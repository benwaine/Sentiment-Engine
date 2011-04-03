<?php
use Construct\Controller\Rest as Rest;
/**
 * Allows access to the data collected by the sampling process.
 *
 * @package    Tracking
 * @subpackage DataPoints
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Api_DataPointController extends Rest
{
    /**
     * Tracking Service
     *
     * @var SE\Infrastructure\Tracking\Service
     */
    protected $trackingService;

    /**
     * Initialiss the resources required by the controller.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->trackingService = $this->container->trackingservice;
    }

    /**
     * Shows a paginated feed of data points
     *
     * @return void
     */
    public function getAction()
    {
        $id       = $this->_request->getParam('id');
        $start    = $this->_request->getParam('start', 1);
        $pageSize = $this->_request->getParam('page-size', 10);
        
        if(is_null($id))
        {
            $this->sendAlteredResponse(404, 'Term Not Found');
        }

        $items = $this->trackingService->getDataPoints($id, $start, $pageSize);

        $this->view->items = $items;
        $this->view->title = "Data Points for the term: " . ucfirst($items[0]->getTerm()->getTerm());
        $this->view->selfLink = $this->view->apiEndPoint . '/datapoints';
        $this->render('get');

    }
}
