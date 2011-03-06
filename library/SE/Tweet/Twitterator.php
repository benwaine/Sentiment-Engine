<?php
namespace SE\Tweet;
use Construct\Iterator\Stream;
/**
 * Twitterator - Provides a means of searching Twitter using it's streams API.
 *
 * @package    Tweet
 * @subpackage Iterator
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Twitterator extends Stream\Factory
{
    /**
     * Parameters to track (search).
     *
     * @var array
     */
    protected $trackParams;

    /**
     * Parameters to use for locations.
     *
     * @var array
     */
    protected $locationParams;

    /**
     * Initiates the data string construction and returns the result.
     *
     * @return string
     */
    public function getData()
    {

        $this->setData();

        return $this->data;
    }

    /**
     * Constructs a Data string from parameters set with addTrack and addLocation
     * methods.
     *
     * @return void
     */
    public function setData()
    {
        $data = "";

        if(!empty($this->trackParams))
        {
            $data .= "track=";

            $data .= implode(',', $this->trackParams);
        }

        if(!empty($this->locationParams))
        {
            if(!empty($data))
            {
                $data .= '&';
            }

            $data .= 'locations=';

            $data .= $this->locationParams;
        }

        $this->data = $data;
    }

    /**
     * Add a search term to the list of terms used.
     *
     * @param string $term
     *
     * @return void
     */
    public function addTrack($term)
    {
        if(strlen($term) > 60)
        {
            throw new \InvalidArgumentException('Track params must be no greater than 60 character long');
        }

        $this->trackParams[] = $term;
    }

    /**
     * Adds a bounding box to the locations section of the query string.
     *
     * @param double $swLon South West Longitude
     * @param double $swLat South West Latitude
     * @param double $neLon North East Longitude
     * @param double $neLat North East Latitude
     *
     * @return void
     */
    public function addLocationBox(array $box)
    {
        if(count($this->locationParams) >= 25)
        {
            throw new \InvalidArgumentException('Only 25 bounding boxes are permitted');
        }
        $this->locationParams = implode(',', $box);
        var_dump($this->locationParams);
    }

    /**
     * Gets a stream iterator.
     *
     * @param function $emitFunction Accepts a lambda function if an object should be emitted.
     *
     * @return object
     */
    public function getStreamIterator($function)
    {
       $stream = $this->createStream();

       return new Stream\EmitObject($stream, $function);

    }

}

