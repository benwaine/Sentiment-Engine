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
        $id = $this->_request->getParam('id');

        if (is_null($id))
        {
            $this->sendAlteredResponse(400, 'ID Must Be Specified When Deleting a Tracking Request');
        }
        else
        {
            try
            {
                $inAr = array();
                $inAr['content']['samplingrequest']['id'] = $id;
                $this->trackingService->changeSamplingStatus($inAr, true);
                $this->sendAlteredResponse(204);
            }
            catch (SE\Infrastructure\Tracking\Exception $e)
            {
                $this->sendAlteredResponse(403, 'No Tracking Request Resource With This ID');
            }

        }
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

        $this->view->title = "Sampling Fulfillment Queue";
        $items = $this->trackingService->getTrackedItems($start, $perPage, $sort);
        $this->view->items = $items;
        $this->render('index');
    }

    /**
     * Changes the status of a sampling request
     * App Draft indicates a sampling request is available.
     *
     * @return void
     */
    public function putAction()
    {
        try
        {
            $parser = $this->container->atomentryparser;
            $entry = $parser->parse($this->_request->getRawBody());
        }
        catch (\InvalidArgumentException $e)
        {
            $this->sendAlteredResponse(400, 'Malformed APP Envelope');
        }

        try
        {
            $this->trackingService->changeSamplingStatus($entry);
        }
        catch (\InvalidArgumentException $e)
        {
            $this->sendAlteredResponse(400, 'No ID passed in content');
        }
        catch (SE\Infrastructure\Tracking\Exception $e)
        {
            $this->sendAlteredResponse(403, 'No Tracking Request Resource With This ID');
        }
    }

}

