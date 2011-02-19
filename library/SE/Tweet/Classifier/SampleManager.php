<?php
namespace SE\Tweet\Classifier;
use SE\Entity;
/**
 * Manager the process of creating an experimental set.
 *
 * @package    Tweet
 * @subpackage Classifier
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class SampleManager
{

    /**
     * A classifier to classify tweets with.
     *
     * @var Iclassify
     */
    protected $classifier;
    /**
     * A result set to store the results in.
     *
     * @var ClassificationSet
     */
    protected $classificationSet;
    /**
     * Number of positve tweets in the sample.
     *
     * @var integer
     */
    protected $sentPos;
    /**
     * Number of negative tweets in the sample.
     *
     * @var integer
     */
    protected $sentNeg;

    /**
     * The size of positive sample required.
     *
     * @var integer
     */
    protected $sentPosLimit;

    /**
     * The size of the negative sample required
     *
     * @varinteger
     */
    protected $sentNegLimit;


    /**
     * Initialises an instance of the Sample Manager class.
     *
     * @param Iclassify         $classifier
     * @param ClassificationSet $classificationSet
     * @param integer           $posLimit
     * @param integer           $negLimit
     *
     * @return void
     */
    public function __construct(Classifier $classifier, Entity\ClassificationSet $classificationSet, $posLimit, $negLimit)
    {
        if(!is_int($posLimit) || !is_int($negLimit))
        {
            throw new \InvalidArgumentException('Limit values must be integers');
        }

        $this->sentPos           = 0;
        $this->sentNeg           = 0;
        $this->sentNegLimit      = $negLimit;
        $this->sentPosLimit      = $posLimit;
        $this->classifier        = $classifier;
        $this->classificationSet = $classificationSet;

    }

    /**
     * Checks to see if the quotas supplied have been met.
     *
     * @return boolean
     */
    public function isComplete()
    {
        if(($this->sentPos <= $this->sentPosLimit) || ($this->sentNeg <= $this->sentNegLimit))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Add a classifiable item to the sample.
     * The item is sampled and the added to the result set providing the type
     * quota isn't reached for the classification type.
     *
     * @param IClassifiable $item Classifiable item.
     *
     * @return void
     */
    public function addToSample(IClassifiable $item)
    {
        $classRes = $this->classifier->classify($item);
        //print $classRes;
        switch($classRes)
        {
            case Classifier::CLASSIFICATION_RESULT_POSITIVE:
           
                if($this->sentPos <= $this->sentPosLimit)
                {
                    $this->addTweetToClassificationSet($item, $classRes);
                    $this->sentPos++;
                }

                break;
            case Classifier::CLASSIFICATION_RESULT_NEGATIVE:
               
                if($this->sentNeg <= $this->sentNegLimit)
                {
                    $this->addTweetToClassificationSet($item, $classRes);
                    $this->sentNeg++;
                }

                break;

            case Classifier::CLASSIFICATION_RESULT_NEUTRAL:
            default:
                return;
                break;
        }

    }

    /**
     * Adds a tweet and its classification result to the result set.
     *
     * @return void
     */
    private function addTweetToClassificationSet(Entity\Tweet $tweet, $result)
    {
        $classification = new Entity\TweetClassification($tweet);
        $classification->setClassificationType(1);
        $classification->setClassificationResult($result);
        $classification->setClassificationTime(new \DateTime());

        $tweet->addClassification($classification);
        $this->classificationSet->addTweet($classification);

        print $classification->getClassificationResult();
        return;
    }

}

