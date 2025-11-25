<?php

namespace Waterloobae\QuizConverter;

use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use SimpleXMLElement;

class D2LQuizGenerator extends Controller {
    private $questions;

    public function __construct() {
        $this->questions = [];
    }

    public function setQuestions(array $questions) {
        $this->questions = $questions;
        return $this;
    }

    public function generateXML() {
        $xml = new SimpleXMLElement('<quiz />');

        foreach ($this->questions as $index => $question) {
            $questionElem = $xml->addChild('question');
            $questionElem->addAttribute('type', 'multiple_choice');

            $textElem = $questionElem->addChild('text', htmlspecialchars($question['text']));

            $answersElem = $questionElem->addChild('answers');
            foreach ($question['choices'] as $choiceIndex => $choice) {
                $answerElem = $answersElem->addChild('answer', htmlspecialchars($choice));
                if ($choiceIndex == $question['correct_index']) {
                    $answerElem->addAttribute('correct', 'true');
                } else {
                    $answerElem->addAttribute('correct', 'false');
                }
            }

            // Add solution if provided
            if (isset($question['solution'])) {
                $solutionElem = $questionElem->addChild('solution', htmlspecialchars($question['solution']));
            }
        }

        return $xml->asXML();
    }

    public function saveToFile($filename) {
        file_put_contents($filename, $this->generateXML());
    }

    public function download() {
        $xml = $this->generateXML();
        return response($xml, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="d2l_quiz.xml"');
    }

    public function generate() {
        $xml = $this->generateXML();
        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}
