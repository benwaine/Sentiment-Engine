<?php
namespace Construct;
/**
 * Exposes a string which can be hashed to provide an ETAG.
 *
 * @package Construct
 * @author  Ben Waine <ben@ben-waine.co.uk>
 */
interface HashState
{
    public function getHashString();

}
