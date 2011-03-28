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
    public function indexAction()
    {  

        $start   = $this->_request->getParam('start', 0);
        $perPage = $this->_request->getParam('offset', 10);
        $sort    = SE\Entity\Repository\TrackingItemRepository::DATE_ORDER;

        $items = $this->container->trackingservice->getTrackedItems($start, $perPage, $sort);

        $this->view->items = $items;

        $this->view->title = 'Tracked Terms';
        $this->view->selfLink = $this->view->apiEndPoint . '/tracking';

        $this->render('tracking-fulfillment/index', null, true);
    }

}

