<?php

namespace classes;

use classes\DbConnector;
use PDO;

class Quiz {

    private $quizid;
    private $quiztitle;
    private $quizresources;
    private $score;
    private $tokenstatus;

    public function __construct($quizid, $quiztitle, $quizresources, $score, $tokenstatus) {
        $this->quizid = $quizid;
        $this->quiztitle = $quiztitle;
        $this->quizresources = $quizresources;
        $this->score = $score;
        $this->tokenstatus = $tokenstatus;
    }

    public function saveQuiz($quiz, $questions, $options, $correctAnswers) {
        try {
            $pdo = DbConnector::getConnection();

            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO quiz (quiztitle, quizresources, score) VALUES (:quiz_title, :quiz_resources, :score)");
            $quizTitle = $quiz->getQuizTitle();
            $quizResources = $quiz->getQuizResources();
            $score = $quiz->generateScore();

            $stmt->bindParam(':quiz_title', $quizTitle);
            $stmt->bindParam(':quiz_resources', $quizResources);
            $stmt->bindParam(':score', $score);

            $stmt->execute();

            $quizId = $pdo->lastInsertId();

            foreach ($questions as $index => $question) {
                $stmt = $pdo->prepare("INSERT INTO questions (quizid, question_text) VALUES (:quiz_id, :question_text)");
                $stmt->bindParam(':quiz_id', $quizId);
                $stmt->bindParam(':question_text', $question);
                $stmt->execute();

                $questionId = $pdo->lastInsertId();

                foreach ($options[$index] as $optionIndex => $option) {
                    $isCorrect = ($correctAnswers[$index] == $optionIndex + 1) ? 1 : 0;
                    $stmt = $pdo->prepare("INSERT INTO options (questionid, option_text, is_correct) VALUES (:question_id, :option_text, :is_correct)");

                    $questionIdParam = $questionId;
                    $optionTextParam = $option;
                    $isCorrectParam = $isCorrect;

                    $stmt->bindParam(':question_id', $questionIdParam);
                    $stmt->bindParam(':option_text', $optionTextParam);
                    $stmt->bindParam(':is_correct', $isCorrectParam);
                    $stmt->execute();
                }
            }

            // Commit the transaction
            $pdo->commit();
        } catch (PDOException $e) {
            // Rollback the transaction in case of an error
            $pdo->rollBack();
            echo "Error: " . $e->getMessage();
        } finally {
            // Close the database connection
            $pdo = null;
        }
    }

    public function getQuizId() {
        return $this->quizid;
    }

    public function generateScore() {
        
    }

    public function getQuizTitle() {
        return $this->quiztitle;
    }

    public function getQuizResources() {
        return $this->quizresources;
    }

    public function manageTokenStatus() {
        // Implement logic to manage token status
    }

}
