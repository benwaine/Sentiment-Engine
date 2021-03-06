<?php
namespace SE\Tweet\Utility;

use Doctrine\DBAL;

/**
 * Class manages quick DB access to word rows in the Database.
 */
class WordStorage
{

    /**
     * Connection to the database.
     * 
     * @var Doctrine\DBAL\Connection
     */
    private $conn;

    public function __construct(DBAL\Connection $conn)
    {
        $this->conn = $conn;
    }

    public function getWord($classificationId, $word)
    {

        $sql = "SELECT *
                FROM classification_set_word w
                WHERE
                w.classification_set_word_word = ?
                AND
                w.classification_set_word_set_id = ?
                ";

        $stmt = $this->conn->executeQuery($sql, array($word, $classificationId));

        $word = $stmt->fetch();

        $stmt->closeCursor();

        return $word;
    }

    public function insertWord(array $word)
    {
        $sql = "INSERT INTO classification_set_word
                  ( classification_set_word_set_id
                  ,classification_set_word_word
                  ,classification_set_word_positive
                  ,classification_set_word_negative
                  ,classification_set_word_appearences)
                  VALUES (:setID, :word, :positive, :negative, :appear )";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':setID', $word['classification_set_word_set_id']);
        $stmt->bindParam(':word', $word['classification_set_word_word']);
        $stmt->bindParam(':positive', $word['classification_set_word_positive']);
        $stmt->bindParam(':negative', $word['classification_set_word_negative']);
        $stmt->bindParam(':appear', $word['classification_set_word_appearences']);

        $stmt->execute();
    }

    public function updateWord(array $word)
    {
        
        $sql = "UPDATE classification_set_word
                SET
                classification_set_word_set_id = :setID
               ,classification_set_word_word = :word
               ,classification_set_word_positive = :positive
               ,classification_set_word_negative = :negative
               ,classification_set_word_appearences = :appear
               WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':setID', $word['classification_set_word_set_id']);
        $stmt->bindParam(':word', $word['classification_set_word_word']);
        $stmt->bindParam(':positive', $word['classification_set_word_positive']);
        $stmt->bindParam(':negative', $word['classification_set_word_negative']);
        $stmt->bindParam(':appear', $word['classification_set_word_appearences']);
        $stmt->bindParam(':id', $word['id']);

        $stmt->execute();
    }

    public function insertWordArray(array $words)
    {
        $wc = count($words);

        if ($wc > 400)
        {
            $workArrays = array_chunk($words, 400);
        }
        else
        {
            $workArrays = array($words);
        }
        
        foreach ($workArrays as $wa)
        {

            $sqlStmt = "INSERT INTO classification_set_word
                  ( classification_set_word_set_id
                  ,classification_set_word_word
                  ,classification_set_word_positive
                  ,classification_set_word_negative
                  ,classification_set_word_appearences)
                  VALUES ";


            foreach ($wa as $word)
            {
                $sqlStmt .= "(?, ?, ?, ?, ?),";
            }

            $sqlStmt = substr($sqlStmt, 0, -1);

            $stmt = $this->conn->prepare($sqlStmt);

            $bindCount = 0;

            foreach ($wa as $key => $word)
            {
                if(!is_array($word))
                {
                    var_dump($word);
                }
                
                $stmt->bindParam(++$bindCount, $word['classification_set_word_set_id']);
                $stmt->bindParam(++$bindCount, $word['classification_set_word_word']);
                $stmt->bindParam(++$bindCount, $word['classification_set_word_positive']);
                $stmt->bindParam(++$bindCount, $word['classification_set_word_negative']);
                $stmt->bindParam(++$bindCount, $word['classification_set_word_appearences']);
            }

            $stmt->execute();
        }
    }
}

