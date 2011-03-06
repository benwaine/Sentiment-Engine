<?php
namespace SE\Tweet\Utility\Exception;
/**
 * Thrown when the Twitter API throws a 502. This hapens fairly regularly
 * despite the fact nothing is wrong.
 *
 * @package    Tweet
 * @subpackage Utility
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class APIGoneAway extends \RuntimeException
{
   
}

