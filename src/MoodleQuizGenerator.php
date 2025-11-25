<?php

namespace Waterloobae\QuizConverter;

use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use DOMDocument;

class MoodleQuizGenerator extends Controller
{
    private $questions;
    private $xml;

    public function __construct()
    {
        $this->questions = [];
        $this->xml = new DOMDocument("1.0", "UTF-8");
        $this->xml->formatOutput = true;
    }

    public function setQuestions(array $questions)
    {
        $this->questions = $questions;
        return $this;
    }

    public function generateXML()
    {
        $quiz = $this->xml->createElement("quiz");
        $this->xml->appendChild($quiz);

        foreach ($this->questions as $question) {
            if ($question['type'] === 'multiple_choice') {
                $quiz->appendChild($this->generateMultipleChoice($question));
            } elseif ($question['type'] === 'numeric') {
                $quiz->appendChild($this->generateNumericQuestion($question));
            }
        }

        return $this->xml->saveXML();
    }

    public function saveToFile($filename = "moodle_quiz.xml")
    {
        file_put_contents($filename, $this->generateXML());
        return $filename;
    }

    private function generateMultipleChoice($question)
    {
        $questionNode = $this->xml->createElement("question");
        $questionNode->setAttribute("type", "multichoice");

        // Question Name
        $nameNode = $this->xml->createElement("name");
        $textNode = $this->xml->createElement("text", htmlspecialchars($question['question']));
        $nameNode->appendChild($textNode);
        $questionNode->appendChild($nameNode);

        // Question Text
        $questionTextNode = $this->xml->createElement("questiontext");
        $questionTextNode->setAttribute("format", "html");
        $textNode = $this->xml->createElement("text", htmlspecialchars($this->wrapLatex($question['question'])));
        $questionTextNode->appendChild($textNode);
        $questionNode->appendChild($questionTextNode);

        // Answers
        foreach ($question['answers'] as $answer) {
            $answerNode = $this->xml->createElement("answer");
            $answerNode->setAttribute("fraction", $answer['correct'] ? "100" : "0");

            $textNode = $this->xml->createElement("text", htmlspecialchars($this->wrapLatex($answer['text'])));
            $answerNode->appendChild($textNode);

            // Feedback (Solution)
            if (!empty($question['solution'])) {
                $feedbackNode = $this->xml->createElement("feedback");
                $feedbackTextNode = $this->xml->createElement("text", htmlspecialchars($this->wrapLatex($question['solution'])));
                $feedbackNode->appendChild($feedbackTextNode);
                $answerNode->appendChild($feedbackNode);
            }

            $questionNode->appendChild($answerNode);
        }

        return $questionNode;
    }

    private function generateNumericQuestion($question)
    {
        $questionNode = $this->xml->createElement("question");
        $questionNode->setAttribute("type", "numerical");

        // Question Name
        $nameNode = $this->xml->createElement("name");
        $textNode = $this->xml->createElement("text", htmlspecialchars($question['question']));
        $nameNode->appendChild($textNode);
        $questionNode->appendChild($nameNode);

        // Question Text
        $questionTextNode = $this->xml->createElement("questiontext");
        $questionTextNode->setAttribute("format", "html");
        $textNode = $this->xml->createElement("text", htmlspecialchars($this->wrapLatex($question['question'])));
        $questionTextNode->appendChild($textNode);
        $questionNode->appendChild($questionTextNode);

        // Correct Answer
        $answerNode = $this->xml->createElement("answer");
        $answerNode->setAttribute("fraction", "100");

        $textNode = $this->xml->createElement("text", $question['answer']);
        $answerNode->appendChild($textNode);

        // Solution Feedback
        if (!empty($question['solution'])) {
            $feedbackNode = $this->xml->createElement("feedback");
            $feedbackTextNode = $this->xml->createElement("text", htmlspecialchars($this->wrapLatex($question['solution'])));
            $feedbackNode->appendChild($feedbackTextNode);
            $answerNode->appendChild($feedbackNode);
        }

        $questionNode->appendChild($answerNode);

        return $questionNode;
    }

    private function wrapLatex($text)
    {
        return str_replace(["$", "\\("], ["\\(", "\\["], $text);
    }

    public function download()
    {
        $xml = $this->generateXML();
        return response($xml, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="moodle_quiz.xml"');
    }

    public function generate()
    {
        $xml = $this->generateXML();
        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}