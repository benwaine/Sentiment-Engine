<?php
namespace SE\Entity;
/**
 * Description of Tweet
 *
 * @package    Tweet
 * @subpackage Entity
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Tweet
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
   protected $tweet;

   /**
    * Date Tweet
    *
    * @var DateTime
    */
   protected $date;


   /**
    * Instantaites an instance of Tweet
    *
    * @param array $initVals Initial Tweet Values
    *
    * @return void
    */
   public function __construct($initVals = null)
   {

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
   public function getTweet()
   {
       return $this->tweet;
   }

   /**
    * Set the Tweet Text.
    *
    * @param string $tweet Tweet Text
    */
   public function setTweet($tweet)
   {
       $this->tweet = $tweet;
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



}

