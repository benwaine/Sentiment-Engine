<?php
namespace SE\Tweet\Classifier;
/**
 * Interface govorning Tweet Classifiers
 *
 * @package    Tweet
 * @subpackage Classifier
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
interface IClassify
{
    /**
     * Classify the subject and return a result
     *
     * @param IClassifiable $subject Subject to classify
     */
    public static function classify(IClassifiable $subject);
}
