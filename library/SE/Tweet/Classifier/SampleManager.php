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
     * An Array of lambda function executed on a sample after it has been
     * classfied. FIFO order applied.
     *
     * @var array
     */
    protected $postProcesses;

    /**
     * An array of lambda functions executed on a sample before it is classified.
     * FIFO order is applied.
     *
     * @var array
     */
    protected $preProcesses;


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
     * Add a function tp the pre processing stack.
     *
     * @param function $fn Function to execute on sample.
     */
    public function addPreProcess($fn)
    {
        $this->preProcesses[] = $fn;
    }

    /**
     * Add a function to the post processing stack.
     *
     * @param functiom $fn Function to carry out on the sample.
     *
     * @return void 
     */
    public function addPostProcess($fn)
    {
        $this->postProcesses[] = $fn;
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
        $this->preProcessSample($item);

        $classRes = $this->classifier->classify($item);
       
        switch($classRes)
        {
            case Classifier::CLASSIFICATION_RESULT_POSITIVE:
           
                if($this->sentPos <= $this->sentPosLimit)
                {
                    $this->addTweetToClassificationSet($item, $classRes);
                    $this->postProcessSample($item);
                    $this->sentPos++;
                }

                break;
            case Classifier::CLASSIFICATION_RESULT_NEGATIVE:
               
                if($this->sentNeg <= $this->sentNegLimit)
                {
                    $this->addTweetToClassificationSet($item, $classRes);
                    $this->postProcessSample($item);
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
        
        return;
    }

    /**
     * Executes all function in the pre process stack on the sample.
     *
     * @return void
     */
    private function preProcessSample($tweet)
    {
        if(count($this->preProcesses) < 1)
        {
            return;
        }

        foreach($this->preProcesses as $fn)
        {
            $fn($tweet);
        }
    }

    /**
     * Executes all functions in the post process stack on the sample.
     *
     * @param Entity\Tweet $tweet
     *
     * @return void
     */
    private function postProcessSample(Entity\Tweet $tweet)
    {
        if(count($this->postProcesses) < 1)
        {
            return;
        }

        foreach($this->postProcesses as $fn)
        {
            $fn($tweet);
        }
    }

}

