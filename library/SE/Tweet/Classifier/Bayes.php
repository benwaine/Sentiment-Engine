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
        $this->entityManager     = $entityManager;
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
        $text = $tweet->getText();
        $words = explode(' ', $text);

        foreach($words as $word)
        {
            $probs = $this->wordProbability($word);

            var_dump($probs);
        }
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
        $id = md5($this->classificationSet->getId() . $word);

        $query = $this->entityManager->createQuery(
                'SELECT count(w.id) FROM SE\Entity\Word w');

        $wordCount = $query->getSingleScalarResult();

        $wordOb = $this->entityManager->find('SE\Entity\Word', $id);

        if(!is_null($wordOb))
        {
            return array('p' => $wordOb->getPositive() / $wordCount, 'n' => $wordOb->getNegative() / $wordCount);
        }
        else
        {
            return array('p' => 0, 'n' => 0);
        }

        
    }

}

