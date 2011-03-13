<?php

/**
 * Atom Entry Parser
 *
 * @package    Atom
 * @subpackage Entry
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Entry
{

    private $id;
    private $title;
    private $author;
    private $updated;
    private $categories;
    private $content;
    private $entry;

    public function __construct($entry)
    {
        $this->entry = $entry;

        $this->parse();
    }

    private function parse()
    {
        $dom = new DomDocument($this->entry);

        foreach ($dom->childNodes as $child)
        {
            switch ($child->nodeName)
            {
                case 'id':
                    $this->id = $child->nodeValue;
                    break;
                case 'id':
                    break;
                case 'id':
                    break;
                case 'id':
                    break;
                case 'id':
                    break;
                case 'id':
                    break;
            }
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getEntry()
    {
        return $this->entry;
    }

}

?>
