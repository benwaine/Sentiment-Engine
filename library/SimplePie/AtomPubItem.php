<?php

define('SIMPLEPIE_NAMESPACE_ATOMPUB', 'http://www.w3.org/2007/app');

class SimplePie_AtomPubItem extends SimplePie_Item
{

    /**
     * Constructor
     */
    function WP_SimplePieAtomPub_Item($feed, $data)
    {
        parent::SimplePie_Item($feed, $data);
    }

    /**
     * Get the status of the entry
     *
     * @return bool True if the item is a draft, false otherwise
     */
    function get_draft_status()
    {
        $draft = false;
        if (($control = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOMPUB, 'control')) && !empty($control[0]['child'][SIMPLEPIE_NAMESPACE_ATOMPUB]['draft'][0]['data']))
        {
            $draft = ('yes' == $control[0]['child'][SIMPLEPIE_NAMESPACE_ATOMPUB]['draft'][0]['data']);
        }
        return $draft;
    }

    function get_content()
    {
        //var_dump($this->get_item_tags());
        if ($return = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'content'))
        {
            var_dump($return);
            var_dump($this->sanitize($return[0]['data'], SimplePie_Misc::atom_10_content_construct_type($return[0]['attribs']), $this->get_base($return[0])));
            die;
        }
    }

    /**
     * Get the GMT timestamp of the entry
     *
     * @param string $format date() format
     * @return int|string|null
     */
    function get_gmdate($format = 'j F Y, g:i a')
    {
        return gmdate($format, $this->get_date('U'));
    }

    /**
     * Get the updated timestamp of the entry
     *
     * AtomPub needs the distinction between "created" and "updated".
     * @param string $format date() format
     * @return string|int|null
     */
    function get_updated($format = 'j F Y, g:i a')
    {
        if ($updated = $this->get_item_tags(SIMPLEPIE_NAMESPACE_ATOM_10, 'updated'))
        {
            $return = date('Y-m-d H:i:s', $updated[0]['data']);
        }
        else
        {
            return null;
        }

        if ($return)
        {
            $parser = SimplePie_Parse_Date::get();
            $parsed = $parser->parse($return);
            $date_format = (string) $date_format;
            switch ($date_format)
            {
                case '':
                    return $this->sanitize($return, SIMPLEPIE_CONSTRUCT_TEXT);

                case 'U':
                    return $parsed;

                default:
                    return date($date_format, $parsed);
            }
        }
        else
        {
            return null;
        }
    }

    /**
     * Get the updated GMT timestamp of the entry
     *
     * @param string $format date() format
     * @return int|string|null
     */
    function get_gmupdated($format = 'j F Y, g:i a')
    {
        return gmdate($format, $this->get_updated('U'));
    }

}