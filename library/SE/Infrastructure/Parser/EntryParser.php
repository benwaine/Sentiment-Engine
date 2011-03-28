<?php
namespace SE\Infrastructure\Parser;
use SE\Infrastructure\Parser\RequestParser as ParentParser;
/**
 * Description of EntryParser
 *
 * @author Ben
 */
class EntryParser extends ParentParser
{
    /**
     * Implementation of the abstract method parse.
     *
     * @param string $feed
     *
     * @return array
     */
    public function parse($entry)
    {
        $this->dom->loadXml($entry);

        $elements = $this->dom->getElementsByTagName('entry');

        $mAr = array();

        foreach ($elements as $elm)
        {
            $mAr = $this->recurseToArray($elm, array());
        }

        return $mAr;
    }
}

