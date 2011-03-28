<?php

use Construct\Controller;

/**
 * Deals with the Tracking Fulfillment process.
 *
 * @package    Tracking
 * @subpackage FulFillment
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Api_TrackingFulfillmentController extends Controller\Rest
{

    /**
     * The tracking service. 
     *
     * @var SE\Infrastructure\Tracking\Service
     */
    protected $trackingService;

    /**
     * Init Method
     */
    public function init()
    {
        parent::init();
        $this->trackingService = $this->container->trackingservice;
    }

    /**
     * Lists All unfulfilled tracking requests.
     * 
     * @return void
     */
    public function indexAction()
    {
        $pendingItems = $this->trackingService->getPendingTrackingItems();

        $this->view->items = $pendingItems;
        $this->title = "Tracking Fulfillment Queue";
        $this->selfLink = $this->view->apiEndPoint . "/tracking-fulfillment";

        $this->render('index');
    }

    /**
     * Gets a specific unfulfilled tracking request.
     *
     * @return void
     */
    public function getAction()
    {
        $id = $this->_request->getParam('id');

        $item = $this->trackingService->getTrackingItem($id);

        if (is_null($item))
        {
            $this->sendAlteredResponse(404, 'No Tracking Term for This ID');
        }
        else
        {
            $this->view->item = $item;
            $this->render('tracking-fulfillment');
        }
    }

    /**
     * Creates a new tracking request.
     *
     * @return void
     */
    public function postAction()
    {
        try
        {
            $trackingReq = $this->_helper->TrackingRequest($this->_request->getRawBody());

            $item = $this->trackingService->addTrackingItem($trackingReq['content']);

            $this->view->item = $item;

            $this->_response->setHttpResponseCode(201);
            $this->_response->setHeader('Etag', md5($item->getHashString()));
            $this->_response->setHeader('Content-Type', 'application/atom+xml;charset="utf-8"');

            // - Causing additional markup to be generated
            // $this->_response->setHeader('Location', $this->view->apiEndPoint . '/fulfillment/' . $item->getId());

            $this->render('tracking-fulfillment');
        }
        catch (InvalidArgumentException $e)
        {
            $this->sendAlteredResponse(400, $e->getMessage());
        }
        catch (SE\Infrastructure\Tracking\Exception $e)
        {
            $this->sendAlteredResponse(403, 'Term is already tracked');
        }
    }

    /**
     * Changes a tracking request.
     *
     * @return void
     */
    public function putAction()
    {
        try
        {
            $trackingReq = $this->_helper->TrackingRequest($this->_request->getRawBody());
        }
        catch (\InvalidArgumentException $e)
        {
            $this->sendAlteredResponse(400, 'Malformed APP Envelope');
        }

        try
        {
            $this->trackingService->changeFulfillmentStatus($trackingReq);
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

    /**
     * Deletes a fulfilled tracking request.
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
                $this->trackingService->changeFulfillmentStatus(array('content' => array('id' => $id)), true);
                $this->sendAlteredResponse(204);
            }
            catch (SE\Infrastructure\Tracking\Exception $e)
            {
                $this->sendAlteredResponse(403, 'No Tracking Request Resource With This ID');
            }
            
        }
    }

}

