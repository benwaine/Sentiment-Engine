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

        foreach ($words as $word)
        {
            $probs = $this->wordProbability($word);
            $wordProbsPos[$word] = $probs['p'];
            $wordProbsNeg[$word] = $probs['n'];
        }

        $sumProbs['p'] = $this->bayes($wordProbsPos);
        $sumProbs['n'] = $this->bayes($wordProbsNeg);

        var_dump($sumProbs);
        var_dump($text);
        

    }

    private function bayes($probArray)
    {
        $count = count($probArray);
        $i = 0;
        foreach($probArray as $key => $value)
        {
            if($i == 0)
            {
                $pn = $value;
            }
            elseif($value == 0)
            {
                continue;
            }
            else
            {
                $pn = $pn * $value;
            }
        
            $i++;
        }

        $i = 0;

        foreach($probArray as $key => $value)
        {
            if($i == 0)
            {
                $ps = 1 - $value;
            }
            elseif($value == 0)
            {
              continue;
            }
            else
            {
                $ps = $ps * (1 - $value);
            }

            $i++;
        }

        $prob = $pn / ($pn + $ps);
        
        return $prob;

      
    }

    private function cleanTweet(Entity\Tweet $tweet)
    {
        $text = $tweet->getText();

        $words = \explode(' ', $text);

        foreach($words as &$word)
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

        if(is_array($results))
        {
            if(array_key_exists(0, $results))
            {
                $wordOb = $results[0];
                return array('p' => $wordOb->getPositive() / $this->classificationSet->getPositiveSampleSize(),
                             'n' => $wordOb->getNegative() / $this->classificationSet->getNegativeSampleSize());
            }
            else
            {
                return array('p' => 0, 'n' => 0);
            }
        }
    }

}

