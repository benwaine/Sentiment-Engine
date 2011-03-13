<?php
require_once 'SimplePie/simplepie.inc';
require_once 'SimplePie/AtomPubItem.php';

/**
 * Parses Atom Pub Entries.
 *
 * @package    SimplePie
 * @subpackage
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class AtomPubParser
{
    /**
     * Parses standard Input for entry items.
     * 
     * @return SimplePie
     */
    public static function parse()
    {
        $data = file_get_contents('php://input');

        // SimplePie expects the feed element to be the top element
        // This could probably be improved

        if (strpos($data, '<feed') === false)
        {
            $data = str_replace('<entry', '<feed xmlns="' . SIMPLEPIE_NAMESPACE_ATOM_10 . '"><entry', $data);
            $data = str_replace('</entry>', '</entry></feed>', $data);
        }

        $feed = new SimplePie();
        $feed->set_item_class('SimplePie_AtomPubItem');
        $feed->set_raw_data($data);
        $feed->init();
        
        if ($feed->error())
        {
            $this->bad_request($feed->error());
        }

        return $feed;
    }

}
