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
            $name = $child->getName();

            switch ($name)
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

                case 'link':

                    $parsedEntity['link'][] = $this->parseLink($child);

                    break;
            }
        }

        // Name SPaced APP protocol
        $children = $entity->children('app', true);
        foreach ($children as $child)
        {
            switch($child->getName())
            {
                case 'control':
                    $parsedEntity['draft'] = $this->parseAppControl($child);
                    break;
            }
        }

        // If no draft control present must be added
        if(!array_key_exists('draft', $parsedEntity))
        {
            $parsedEntity['draft'] = false;
        }


        return $parsedEntity;
    }

    /**
     * Parses the APP control 'draft'
     *
     * @return array
     */
    private function parseAppControl(\SimpleXMLElement $element)
    {
        $control = array();

        foreach($element->children('app', true) as $child)
        {
            $control[$child->getName()] = $child->__toString();
        }

        if(!array_key_exists('draft', $control))
        {
            throw \InvalidArgumentException('Malformed APP Draft Control');
        }

        return array('draft' => ($control['draft'] == 'yes') ? true : false);
    }

    private function parseLink(\SimpleXMLElement $element)
    {
        $atrs = array();

        foreach($element->attributes() as $a => $b)
        {
                $atrs[$a] = $b->__toString();

        }

        if(!array_key_exists('rel', $atrs) || !\array_key_exists('href', $atrs))
        {
            throw new \InvalidArgumentException('Malformed Link Tag in XML');
        }
        
        return array($atrs['rel'] => $atrs['href']);
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


        // Check the content is a tracking request.
        if($child->getName() != "trackingrequest")
        {
            throw new \InvalidArgumentException('NON Tracking Request XML encountered');
        }

        $term = 
        
        $content = array();

        foreach($child->children() as $c)
        {
            switch($c->getName())
            {
                case 'term':
                    $content['tracking_request'] = $c->__toString();
                    break;

                case 'id':
                    $content['id'] = $c->__toString();
                    break;
            }
        }
    
        return $content;
    }

}

