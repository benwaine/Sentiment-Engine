<?php
namespace SE\Entity;
use SE\Tweet\Classifier;
/**
 * Represents a word in a Classifier.
 *
 * @package    Classifier
 * @subpackage Entity
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Word
{
    /**
     * Word Id
     *
     * @var integer
     */
    protected $id;

    /**
     * The word.
     *
     * @var string
     */
    protected $word;

    /**
     * Probability: Word is in positive samples.
     *
     * @var double
     */
    protected $positive;

    /**
     * Probability: Word is in negative samples.
     *
     * @var double
     */
    protected $negative;

    /**
     * Total number of appearences in set
     *
     * @var integer
     */
    protected $appearences;

    /**
     * Classification set the word is part of.
     *
     * @var ClassificationSet
     */
    protected $classificationSet;


    /**
     * Get the word ID.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the word ID.
     *
     * @param integer $id ID
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the word.
     *
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * Set the word.
     *
     * @param string $word Tweet word.
     *
     * @return void
     */
    public function setWord($word)
    {
        $this->word = $word;
    }


    /**
     * Set probability word appears in positive samples.
     *
     * @return double
     */
    public function getPositive()
    {
        return $this->positive;
    }

    /**
     * Set probability word appears in positive samples
     *
     * @param double $positive
     *
     * @return void
     */
    public function setPositive($positive)
    {
        $this->positive = $positive;
    }

    /**
     * Adds a positive appearence.
     *
     * @return void
     */
    public function addPositiveAppearence()
    {
        $this->positive++;
    }

    /**
     * Get probability word appears in negative samples.
     *
     * @return double.
     */
    public function getNegative()
    {
        return $this->negative;
    }

    /**
     * Set the probability word appears in negative samples.
     *
     * @param double $negative
     *
     * @return void
     */
    public function setNegative($negative)
    {
        $this->negative = $negative;
    }

    /**
     * Adds a negative appearence.
     *
     * @return void
     */
    protected function addNegativeAppearence()
    {
        $this->negative++;
    }

    /**
     * Get total appearences for this word in sample.
     *
     * @return integer
     */
    public function getAppearences()
    {
        return $this->appearences;
    }

    /**
     * Set total appearences for this word in sample.
     *
     * @param integer $appearences
     *
     * @return void
     */
    public function setAppearences($appearences)
    {
        $this->appearences = $appearences;
    }

    /**
     * Increments the word appearence count.
     *
     * @return void
     */
    public function addAppearence($sentiment)
    {
        if($sentiment == Classifier\Classifier::CLASSIFICATION_RESULT_POSITIVE)
        {
            $this->addPositiveAppearence();
        }
        elseif($sentiment == Classifier\Classifier::CLASSIFICATION_RESULT_NEGATIVE)
        {
            $this->addNegativeAppearence();
        }

        $this->appearences++;
    }

    /**
     * Get Classification Set
     *
     * @return ClassificationSet
     */
    public function getClassificationSet()
    {
        return $this->classificationSet;
    }

    /**
     * Set the ClassificationSet
     *
     * @param ClassificationSet $classificationSet
     *
     * @return void
     */
    public function setClassificationSet($classificationSet)
    {
        $this->classificationSet = $classificationSet;
    }



}
