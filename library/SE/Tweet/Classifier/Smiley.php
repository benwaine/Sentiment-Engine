<?php
namespace SE\Tweet\Classifier;
/**
 * A basic classifier that looks for the presence of smiley face glyphs in text.
 * The class uses these glyphs to classify text into positive or negative category.
 *
 * @package    Tweet
 * @subpackage Classifier
 * @author     Ben Waine <ben@ben-waine.co.uk>
 */
class Smiley extends Classifier
{

    /**
     * Happy Face Glyf
     *
     * @var string
     */
    const HAPPYFACE = ':)';

    /**
     * Sad Face Glyf
     * 
     * @var string
     */
    const SADFACE = ':(';

    const CLASSIFIER_TYPE = 1;

    /**
     * Classifies the subject based on the presence of smiley glyfs in the text.
     *
     * @param IClassifiable $subject
     *
     * @return integer
     */
    public static function classify(IClassifiable $subject)
    {
        $text = $subject->getText();

        if(strpos($text, self::SADFACE) && strpos($text, self::HAPPYFACE))
        {
            $classification = self::CLASSIFICATION_RESULT_NEUTRAL;
        }
        elseif(strpos($text, self::HAPPYFACE))
        {
            $classification = self::CLASSIFICATION_RESULT_POSITIVE;
        }
        elseif(strpos($text, self::SADFACE))
        {
            $classification = self::CLASSIFICATION_RESULT_NEGATIVE;
        }
        else
        {
            $classification = self::CLASSIFICATION_RESULT_NEUTRAL;
        }
        
        return $classification;
    }
}

