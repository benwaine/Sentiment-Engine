<?php
namespace SE\Entity;

use Doctrine\Common\Collections;

/**
 * An entity comprised of classified tweets.
 *
 * @package    Entity
 * @subpackage Tweet
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class ClassificationSet
{
    /**
     * Type of the classification set. Corpus: Used to train classifiers.
     *
     * @var integer
     */
    const TYPE_CORPUS = 1;

    /**
     * Type of classification Set. Experiment.
     *
     * @var integer
     */
    const TYPE_EXPERIEMNT = 2;

    /**
     * ID of the classification set.
     *
     * @var integer
     */
    protected $id;
    /**
     * Tweets which make up the classification set.
     *
     * @var ArrayCollection
     */
    protected $classifiedTweets;
    /**
     * Type of the classification set. Should be a class constant.
     *
     * @var integer
     */
    protected $type;
    /**
     * DateTime of the classification set.
     *
     * @var DateTime
     */
    protected $date;
    /**
     * Words in the classification set.
     *
     * @var Common\ArrayCollection
     */
    protected $words;

    /**
     * Initialises an instance of CLassification Set.
     *
     * @return void
     */
    public function __construct()
    {
        $this->tweets = new Collections\ArrayCollection();
    }

    /**
     * Get the ID of the Classification set.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the ID of the experiment set.
     *
     * @param integer $id Set ID.
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the tweets that compose the classification set.
     *
     * @return Collections\ArrayCollection
     */
    public function getTweets()
    {
        return $this->classifiedTweets;
    }

    /**
     * Add a tweet to the experiment set
     *
     * @param Entity\TweetClassification $tweet A classified Tweet to add to the experiemnt.
     *
     * return void
     */
    public function addTweet(TweetClassification $tweet)
    {
        $tweet->setClassificationSet($this);
        $this->tweets[] = $tweet;
    }

    /**
     * Get the classification Type.
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the classification type.
     *
     * @param integer $type Set type.
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get the date the classification set was created.
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets the date the classication took place.
     *
     * @param DateTime $date
     *
     * @return void
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Adds a word to a classification set.
     *
     * @param Word $word
     *
     * @return void
     */
    public function addWord($word)
    {
        $word->setClassificationSet($this);
        $this->words[] = $word;
    }

    /**
     * Get words.
     *
     * @return Collection\Array
     */
    public function getWords()
    {
        return $this->words;
    }

    /**
     * Gets the probability of a word apearing in a positive tweet from sample.
     *
     * @param string $word Word to classify
     *
     * @return double
     */
    public function probabilityOfPositive($word)
    {
        $words = $this->getWords()->toArray();
        $allWords = count($words);

        $key = md5($this->getId() . $word);

        if (isset($words[$key]))
        {
            $prob = $sword->getPositive() / $allWords;
            return $prob;
        }
        else
        {
            return 0;
        }
    }

    /**
     * Gets the probability of a word apearing in a positive tweet from sample.
     *
     * @param string $word Word to Classify
     *
     * @return double
     */
    public function probabilityOfNegative($word)
    {
        $words = $this->getWords();
        $allWords = count($words);
        $key = md5($this->getId() . $word);

        if (isset($words[$key]))
        {
            $prob = $sword->getNegative() / $allWords;
            return $prob;
        }
        else
        {
            return 0;
        }

        return $prob;
    }

}