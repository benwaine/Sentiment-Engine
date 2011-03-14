<?php
use Construct\Controller;
/**
 * The Tracking Resource
 * Manages the requests for sample gathering and filter training.
 *
 * @package    API
 * @subpackage Tracking
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Api_TrackingController extends Controller\Rest
{
    /**
     * Get Tracking List
     *
     * @return void
     */
    public function getAction()
    {  
        $start   = $this->_request->getParam('start', 1);
        $perPage = $this->_request->getParam('offset', 10);
        $sort    = SE\Entity\Repository\TrackingItemRepository::DATE_ORDER;

        $items = $this->container->trackingservice->getTrackingItems($start, $perPage, $sort);

        $this->view->items = $items;

        $this->render('get');
    }

    /**
     * Handels a tracking request.
     *
     * @return void
     */
    public function postAction()
    {
        try
        {
            $trackingReq = $this->_helper->TrackingRequest($this->_request->getRawBody());
            $this->container->trackingservice->addTrackingItem($trackingReq['content']);
        }
        catch(InvalidArgumentException $e)
        {
            $this->sendAlteredResponse(400, $e->getMessage());
        }
    }

}

