<?php

use Construct\Controller\Rest as Rest;

/**
 * Controls the sampling process.
 * This process produces Datapoints for presentation layer to display.
 *
 * @package    Sampling
 * @subpackage Fulfillment
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Api_SamplingFulfillmentController extends Rest
{

    /**
     * The tracking service. 
     *
     * @var SE\Infrastructure\Tracking\Service
     */
    protected $trackingService;

    /**
     * Initialises the controller.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->trackingService = $this->container->trackingservice;
        $this->view->selfLink = $this->view->apiEndPoint . "/sampling-fulfillment";

    }

    /**
     * Registers a job as available to another worker node.
     *
     * @return void
     */
    public function deleteAction()
    {

    }

    /**
     * Gets a sampling request
     *
     * @return void
     */
    public function getAction()
    {

        $this->view->title = "Sampling Fulfillment Request";
        $id = $this->_request->getParam('id');

        $item = $this->trackingService->getTrackingItem($id);

        if (is_null($item))
        {
            $this->sendAlteredResponse(404, 'No Tracking Term for This ID');
        }
        else
        {
            $this->view->item = $item;
            $this->render('sampling-fulfillment');
        }
    }

    /**
     * Shows the list of sampling requests
     * (tracked terms with a viable classification set)
     *
     * @return void
     */
    public function indexAction()
    {
        $start = $this->_request->getParam('start', 0);
        $perPage = $this->_request->getParam('offset', 10);
        $sort = SE\Entity\Repository\TrackingItemRepository::DATE_ORDER;

        $this->view->items = $items;
        $this->view->title = "Sampling Fulfillment Queue";
        $items = $this->container->trackingservice->getTrackedItems($start, $perPage, $sort);

        $this->render('index');
    }

    /**
     * Creates a new sampling request
     *
     * @return void
     */
    public function postAction()
    {
        
    }

    /**
     * Changes the status of a sampling request
     * App Draft indicates a sampling request is available.
     *
     * @return void
     */
    public function putAction()
    {
        $parser = $this->container->atomentryparser;
        $entry = $parser->parse($this->_request->getRawBody());;
        var_dump($entry);
        die;

    }

}

