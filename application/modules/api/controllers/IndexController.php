<?php
use Construct\Controller;
/**
 * Service Discovery Point
 *
 * @package    API
 * @subpackage Endpoint
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Api_IndexController extends Controller\Rest
{
    /**
     * Service discovery occurs at this well known endpoint.
     *
     * @return void
     */
    public function getAction()
    {
        $this->render('get');
    }
}

