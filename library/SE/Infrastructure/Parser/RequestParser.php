<?php
namespace SE\Infrastructure\Parser;
/**
 * Parses the various forms of Atom Feed and Entry used by the API.
 *
 * @package    Infrastructure
 * @subpackage Parser
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
abstract class RequestParser
{
    /**
     * Dom Document
     *
     * @var DomDocument
     */
    protected $dom;

    /**
     * Initialises an instance of the Request Parser Class
     *
     * @return void
     */
    public function __construct(\DomDocument $dom)
    {
        $this->dom = $dom;
        $this->dom->preserveWhiteSpace = false;
    }


    /**
     * Parse the request structure into an Array.
     *
     * @return array
     */
    public abstract function parse($string);

    /**
     * Recurivley parses an xml structure into an array. Does NOT
     * parse attricutes.
     *
     * @param DOMElement $element Element to parse
     * @param array      $array Array to parse into
     *
     * @return array
     */
    protected function recurseToArray($element, array $array)
    {
        $name = $element->nodeName;
        $value = $element->nodeValue;
        $type = $element->nodeType;
        $len = $element->childNodes->length;

        if ($element->hasChildNodes())
        {
            foreach ($element->childNodes as $node)
            {
                if ($node->nodeType == XML_TEXT_NODE)
                {
                    return $node->nodeValue;
                }
                else
                {
                    if ($node->nodeName == 'link' && !is_null($node->attributes->getNamedItem('rel')))
                    {
                        $array[$node->nodeName][$node->attributes->getNamedItem('rel')->nodeValue] = $node->attributes->getNamedItem('href')->nodeValue;
                    }
                    else
                    {
                        $array[$node->nodeName] = $this->recurseToArray($node, array());
                    }
                }
            }
        }
        else
        {
            return $element->nodeValue;
        }

        return $array;
    }

}

