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
     * Get Tracking
     */
    public function getAction()
    {
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
        }
        catch(InvalidArgumentException $e)
        {
            $this->sendAlteredResponse(400, $e->getMessage());
        }
    }

}

