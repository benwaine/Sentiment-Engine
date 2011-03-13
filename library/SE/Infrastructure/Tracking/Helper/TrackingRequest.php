<?php
namespace SE\Infrastructure\Tracking\Helper;
/**
 * An action helper to parse the single atom entry posted to the tracking
 * collection.
 *
 * @package    Tracking
 * @subpackage Helper
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class TrackingRequest extends \Zend_Controller_Action_Helper_Abstract
{

    /**
     * Method used to invoke the helper directly via the helper broker.
     *
     * @param string $entry Entry is Atom Format
     *
     * @return array
     */
    public function Direct($entry)
    {
        return $this->TrackingRequest($entry);
    }

    /**
     * Take an XML string containing an ATOM Entry and parses it into a PHP array
     * of values used by service classes to create and persist tracking entries.
     *
     * @param string $entry XML ATOM entry
     *
     * @return array
     */
    public function TrackingRequest($entry)
    {

        $entity = new \SimpleXMLElement($entry);

        $children = $entity->children();

        $parsedEntity = array();

        foreach ($children as $child)
        {
            //echo $child->getName() . "  " ;

            switch ($child->getName())
            {
                case 'title':
                    $parsedEntity['title'] = $child->__toString();
                    break;

                case 'id':
                    $parsedEntity['id'] = $child->__toString();
                    break;

                case 'updated':
                    $parsedEntity['updated'] = $child->__toString();
                    break;

                case 'author':

                    $author = $child->children();

                    if ($author->count() > 0)
                    {
                        foreach ($author as $auth)
                        {
                            $parsedEntity['author'][] = $auth->__toString();
                        }
                    }
                    break;

                case 'content':
                    $parsedEntity['content'] = $this->contentHandler($child);
                    break;
            }
        }
        
        return $parsedEntity;
    }

    /**
     * Handles the content of the entry
     *
     * @return array
     */
    private function contentHandler(\SimpleXMLElement $element)
    {
        $type = $element->attributes();

        if($type->__toString() != 'application/vnd.tse+xml')
        {
            throw new \InvalidArgumentException('TSE Media Type Required');
        }

        $child = $element->children();


        // Check the contenjt is a tracking request.
        if($child->getName() != "trackingrequest")
        {
            throw new \InvalidArgumentException('NON Tracking Request XML encountered');
        }

        $term = $child->children();

        if($term->getName() == "term")
        {
            return array('tracking_request' => $term->__toString());
        }
        else
        {
            throw InvalidArgumentException('No tracking term specified');
        }

    }

}

