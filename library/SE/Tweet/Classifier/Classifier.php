<?php
namespace SE\Tweet\Classifier;
/**
 * Abstract Classifier
 *
 * @package    Tweet
 * @subpackage Classifier
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
abstract class Classifier
{
    /**
     * Result returned in the event of a negative classification.
     *
     * @var integer
     */
    const CLASSIFICATION_RESULT_NEGATIVE = 1;

    /**
     * Result returned in the event of a possitive classification
     *
     * @var integer
     */
    const CLASSIFICATION_RESULT_POSITIVE = 2;

    /**
     * Result returned in the event of a nutral / unclassifiable subject.
     *
     * @var integer
     */
    const CLASSIFICATION_RESULT_NEUTRAL = 3;

    /**
     * Classifies the subject.
     *
     * @param IClassifiable $subject Subject to classify.
     *
     * @return integer
     */
    abstract public function classify(IClassifiable $subject);


}
