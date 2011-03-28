<?php
namespace SE\Tweet\Classifier;

use SE\Entity;
use Doctrine\ORM;

/**
 * Classifies a tweet based on Bayes formular.
 *
 * @package    Tweet
 * @subpackage Classifier
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Bayes extends Classifier
{

    /**
     * The Doctrine2 entity Manager
     *
     * @var EntityManager
     */
    private $entityManager;
    /**
     * The classification set to use to base the bayes classification on.
     *
     * @var ClassificationSet
     */
    private $classificationSet;

    /**
     * Initialises an instance of the Bayes Classifier.
     *
     * @param ORM\EntityManager $entityManager
     *
     * @return void
     */
    public function __construct(ORM\EntityManager $entityManager, Entity\ClassificationSet $classificationSet)
    {
        $this->entityManager = $entityManager;
        $this->classificationSet = $classificationSet;
    }

    /**
     * Classifies a Tweet According to Bayes CLassifier.
     *
     * @param Tweet $tweet
     *
     * return int
     */
    public function classify(IClassifiable $tweet)
    {
        $this->cleanTweet($tweet);

        $text = $tweet->getText();
        $words = explode(' ', $text);

        $wordProbsPos = array();
        $wordProbsNeg = array();

        $wordPresent = array();

        foreach ($words as $key => $word)
        {
            // Double words disguarded
            if (in_array($word, $wordPresent))
            {
                unset($words[$key]);
            }
            else
            {
                $wordPresent[] = $word;
            }

            $probs = $this->wordProbability($word);
            $wordProbsPos[$word] = $probs['p'];
            $wordProbsNeg[$word] = $probs['n'];
        }

        $sumProbs['p'] = $this->bayes($wordProbsPos);
        $sumProbs['n'] = $this->bayes($wordProbsNeg);

        return $sumProbs;
    }

    private function bayes($probArray)
    {
        $count = count($probArray);
        $i = 0;
        $wordPresent = array();

        // Clean The Array
        foreach ($probArray as $k => $v)
        {
            // 0 values disguarded
            if ($v == 0)
            {
                unset($probArray[$k]);
            }

            // Double words disguarded
            if (in_array($k, $wordPresent))
            {
                unset($probArray[$k]);
            }
            else
            {
                $wordPresent[] = $k;
            }
        }

        $i = 0;

        foreach ($probArray as $key => $value)
        {
            if ($i == 0)
            {
                $pn = $value;
            }
            else
            {
                $pn = $pn * $value;
            }

            $i++;
        }

        $i = 0;
        foreach ($probArray as $key => $value)
        {
            if ($i == 0)
            {
                $ps = 1 - $value;
            }
            else
            {
                $ps = $ps * (1 - $value);
            }

            $i++;
        }

        if (isset($pn) && isset($ps))
        {
            $prob = $pn / ($pn + $ps);
            return $prob;
        }
        else
        {
            return false;
        }
    }

    private function cleanTweet(Entity\Tweet $tweet)
    {
        $text = $tweet->getText();

        $words = \explode(' ', $text);

        foreach ($words as &$word)
        {
            $word = str_replace(array(': ', ', ', '; ', '.', "\'", 'RT', '!', '\"'), '', $word);
            $word = mb_strtolower($word, 'UTF8');
        }

        $tweet->setText(implode(' ', $words));
    }

    /**
     * Returns the probabilities that a word appears in both positive or Negative
     * tweets from the loaded sample.
     *
     * @param string $word Word to analyse
     *
     * @return array
     */
    private function wordProbability($word)
    {
        $query = $this->entityManager->createQuery('SELECT w FROM SE\Entity\Word w WHERE w.word = ?1 AND w.classificationSet = ?2');
        $query->setParameter(1, $word);
        $query->setParameter(2, $this->classificationSet->getID());

        $results = $query->getResult();

        if (is_array($results))
        {
            if (array_key_exists(0, $results))
            {
                $wordOb = $results[0];

                $wordAppearsInPositive = $wordOb->getPositive() / $this->classificationSet->getPositiveSampleSize();
                $wordAppearsInNegative = $wordOb->getNegative() / $this->classificationSet->getNegativeSampleSize();

                // Probability Word is Positive 
                $psw = $wordAppearsInPositive / ($wordAppearsInPositive + $wordAppearsInNegative);

                // Probability Word is Negative
                $nsw = $wordAppearsInNegative / ($wordAppearsInNegative + $wordAppearsInPositive);


                return array('p' => $psw,
                             'n' => $nsw);
            }
            else
            {
                return array('p' => 0, 'n' => 0);
            }
        }
    }

}

