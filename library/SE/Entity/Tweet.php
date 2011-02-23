<?php
namespace SE\Entity;
use SE\Tweet\Classifier;
use Doctrine\Common\Collections;
/**
 * Description of Tweet
 *
 * @package    Tweet
 * @subpackage Entity
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Tweet implements Classifier\IClassifiable
{
   /**
    * Tweet ID From Twiter
    *
    * @var string
    */
   protected $id;

   /**
    * The tweet ID assigned by Twitter.
    *
    * @var string
    */
   protected $tweetId;

   /**
    * Tweet Author User Name
    *
    * @var string
    */
   protected $user;

   /**
    * Text Of Tweet
    *
    * @var string
    */
   protected $text;

   /**
    * Date Tweet
    *
    * @var DateTime
    */
   protected $date;

   /**
    * Tweet Classifications
    *
    * @var ArrayCollection
    */
   protected $classifications;

   /**
    * Language Of Tweet.
    *
    * @var string
    */
   protected $language;


   /**
    * Instantaites an instance of Tweet
    *
    * @param array $initVals Initial Tweet Values
    *
    * @return void
    */
   public function __construct($initVals = null)
   {
        $this->classifications = new Collections\ArrayCollection();
   }


   /**
    * Gets the Tweet ID.
    *
    * @return string
    */
   public function getId()
   {
       return $this->id;
   }

   /**
    * Sets the tweet ID.
    *
    * @param string $id ID String
    *
    * @return void
    */
   public function setId($id)
   {
       $this->id = $id;
   }

   /**
    * Get the Author.
    *
    * @return string
    */
   public function getUser()
   {
       return $this->user;
   }

   /**
    * Set the Author
    *
    * @param string $user Author name.
    *
    * @return void
    */
   public function setUser($user)
   {
       $this->user = $user;
   }

   /**
    * Get the text of the tweet.
    *
    * @return string
    */
   public function getText()
   {
       return $this->text;
   }

   /**
    * Set the Tweet Text.
    *
    * @param string $tweet Tweet Text
    */
   public function setText($tweet)
   {
       $this->text = $tweet;
   }

   /**
    * Get the date the tweet was created.
    *
    * @return DateTime
    */
   public function getDate()
   {
       return $this->date;
   }

   /**
    * Set the date the Tweet was created.
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
    * Get the TweetID.
    *
    * @return string
    */
   public function getTweetId()
   {
       return $this->tweetId;
   }

   /**
    * Set the TweetID.
    *
    * @param string $tweetId TweetID.
    *
    * @return void
    */
   public function setTweetId($tweetId)
   {
       $this->tweetId = $tweetId;
   }

   /**
    * Get the Tweets langauge.
    *
    * @return string
    */
   public function getLanguage()
   {
       return $this->language;
   }

   /**
    * Sets the Tweet langauge.
    *
    * @param string $language
    *
    * @return void
    */
   public function setLanguage($language)
   {
       $this->language = $language;
   }


   /**
    * Tweet Classifications
    *
    * @return TweetClassification
    */
   public function getClassifications()
   {
       return $this->classifications;
   }

   /**
    * Adds a classification to the Tweet
    *
    * @return void
    */
   public function addClassification($classification)
   {
        $this->classifications[] = $classification;
   }



}

