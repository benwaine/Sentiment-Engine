<?php
namespace SE\Tweet\Classifier;
/**
 * Dictaes the interface required by a classiyer on a classifiable object.
 *
 * @package    Tweet
 * @subpackage Classifier
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
interface IClassifiable
{
    /**
     * Supplies text to be classified.
     */
    public function getText();
}

