<?php
namespace SE\Infrastructure\Parser;
use SE\Infrastructure\Parser\RequestParser as ParentParser;

/**
 * Parses a feed into an array
 *
 * @package    Parser
 * @subpackage Feed
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class FeedParser extends ParentParser
{
    /**
     * Implementation of the abstract method parse.
     *
     * @param string $feed
     *
     * @return array
     */
    public function parse($feed)
    {
        $this->dom->loadXml($feed);

        $elements = $this->dom->getElementsByTagName('feed');

        $mAr = array();

        foreach ($elements as $elm)
        {
            $mAr[] = $this->recurseToArray($elm, array());
        }

        return $mAr;
    }
}

