<?php

namespace Waterloobae\QuizConverter;

use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use SimpleXMLElement;

class QTIQuizGenerator extends Controller {
    private $questions;
    private $version;

    public function __construct() {
        $this->questions = [];
        $this->version = '2.1';
    }

    public function setQuestions(array $questions) {
        $this->questions = $questions;
        return $this;
    }

    public function setVersion(string $version) {
        $this->version = $version;
        return $this;
    }

    public function generateXML() {
        $namespace = $this->getNamespace();
        $xml = new SimpleXMLElement("<assessmentTest xmlns='{$namespace}' />");

        foreach ($this->questions as $index => $question) {
            $item = $xml->addChild('assessmentItem');
            $item->addAttribute('identifier', 'Q' . ($index + 1));
            $item->addAttribute('title', 'Question ' . ($index + 1));
            $item->addAttribute('adaptive', 'false');
            $item->addAttribute('timeDependent', 'false');

            $body = $item->addChild('itemBody');
            $body->addChild('p', htmlspecialchars($question['text']));

            $choiceInteraction = $body->addChild('choiceInteraction');
            $choiceInteraction->addAttribute('responseIdentifier', 'RESPONSE' . ($index + 1));
            $choiceInteraction->addAttribute('shuffle', 'false');
            $choiceInteraction->addAttribute('maxChoices', '1');

            foreach ($question['choices'] as $choiceIndex => $choice) {
                $simpleChoice = $choiceInteraction->addChild('simpleChoice', htmlspecialchars($choice));
                $simpleChoice->addAttribute('identifier', 'C' . ($choiceIndex + 1));
            }

            $responseDeclaration = $item->addChild('responseDeclaration');
            $responseDeclaration->addAttribute('identifier', 'RESPONSE' . ($index + 1));
            $responseDeclaration->addAttribute('cardinality', 'single');
            $responseDeclaration->addAttribute('baseType', 'identifier');

            $correctResponse = $responseDeclaration->addChild('correctResponse');
            $correctResponse->addChild('value', 'C' . ($question['correct_index'] + 1));

            // Add solution if provided
            if (isset($question['solution'])) {
                $item->addChild('solution', htmlspecialchars($question['solution']));
            }
        }

        return $xml->asXML();
    }

    private function getNamespace() {
        switch ($this->version) {
            case '1.2':
                return 'http://www.imsglobal.org/xsd/ims_qtiasiv1p2';
            case '2.2':
                return 'http://www.imsglobal.org/xsd/imsqti_v2p2';
            case '3.0':
                return 'http://www.imsglobal.org/xsd/imsqti_v3p0';
            default:
                return 'http://www.imsglobal.org/xsd/imsqti_v2p1';
        }
    }

    public function saveToFile($filename) {
        file_put_contents($filename, $this->generateXML());
    }

    public function getVersion() {
        return $this->version;
    }

    public function download() {
        $xml = $this->generateXML();
        return response($xml, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="qti_quiz.xml"');
    }

    public function generate() {
        $xml = $this->generateXML();
        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}
