# Quiz-Converter

A comprehensive PHP-based quiz conversion tool that generates quiz files in multiple LMS (Learning Management System) formats. This tool converts quiz questions into standardized formats compatible with various educational platforms.

## Features

### Core Capabilities
- **Multiple Question Types Support**:
  - Multiple Choice Questions (MCQ)
  - Numeric/Fill-in-the-Blank Questions
  - Questions with solutions/explanations
  
- **LaTeX/Math Support**: 
  - Built-in LaTeX rendering support for mathematical expressions
  - MathJax integration for H5P quizzes
  - Automatic LaTeX wrapping for Moodle format

- **Customizable Quiz Settings**:
  - Configurable pass percentage
  - Progress tracking (dots/textual)
  - Navigation controls (forward/backward)
  - Randomization options
  - Retry and solution display controls

### Supported Features by Format

#### Canvas Quiz Generator
- QTI-compliant XML generation
- Single-choice multiple choice questions
- Score processing (100 points per correct answer)
- HTML text support

#### Moodle Quiz Generator
- Multiple choice questions with feedback
- Numeric questions with tolerance
- LaTeX formula support with automatic wrapping
- Solution/explanation feedback for each answer
- HTML-formatted question text

#### D2L (Desire2Learn) Quiz Generator
- XML format for D2L platform
- Multiple choice questions
- Correct/incorrect answer marking
- Solution/explanation support
- Simple, clean XML structure

#### H5P Quiz Generator
- Interactive HTML5 quiz packages (.h5p files)
- Multiple choice questions (H5P.MultiChoice library)
- Fill-in-the-blank/Numeric questions (H5P.Blanks library)
- MathJax support for mathematical notation
- Intro page configuration
- Progress tracking with dots
- Result page with retry and solution buttons
- Configurable pass percentage (default: 50%)
- Solution display with explanations

#### QTI Quiz Generator
- **Multi-version QTI support**:
  - QTI 1.2 (IMS QTI ASI v1.2)
  - QTI 2.1 (default)
  - QTI 2.2 (IMS QTI v2.2)
  - QTI 3.0 (IMS QTI v3.0)
- Standards-compliant assessment format
- Choice interactions with single selection
- Response declarations
- Solution support
- Widely compatible across LMS platforms

## Quiz Formats Converted To

This converter generates quizzes in the following formats:

1. **Canvas LMS** (XML) - `canvas_quiz.xml`
   - Format: QTI-compatible XML
   - Structure: questestinterop with assessment and item elements

2. **Moodle** (XML) - `moodle_quiz.xml`
   - Format: Moodle XML format
   - Question types: multichoice, numerical
   - Full HTML and LaTeX support

3. **D2L/Brightspace** (XML) - `d2l_quiz.xml`
   - Format: D2L-specific XML
   - Simple structure with questions and answers

4. **H5P** (H5P Package) - `quiz.h5p`
   - Format: ZIP archive with JSON content
   - Interactive HTML5 content
   - Includes: content.json, h5p.json metadata
   - Libraries: QuestionSet, MultiChoice, Blanks, MathDisplay

5. **QTI (Question & Test Interoperability)** (XML) - `qti_quiz.xml`
   - Format: IMS QTI standard (versions 1.2, 2.1, 2.2, 3.0)
   - Universal format compatible with most modern LMS platforms
   - Includes: Canvas, Blackboard, Moodle, Sakai, and others

## Usage Examples

### Canvas Quiz
```php
$questions = [
    [
        'text' => 'What is 2 + 2?',
        'choices' => ['3', '4', '5'],
        'correct_index' => 1
    ]
];

$generator = new CanvasQuizGenerator($questions);
$generator->saveToFile('canvas_quiz.xml');
```

### Moodle Quiz
```php
$questions = [
    [
        "type" => "multiple_choice",
        "question" => "What is the capital of France?",
        "answers" => [
            ["text" => "Paris", "correct" => true],
            ["text" => "Berlin", "correct" => false]
        ],
        "solution" => "Paris is the capital of France."
    ],
    [
        "type" => "numeric",
        "question" => "Solve: $5 + 7$",
        "answer" => 12,
        "solution" => "The correct answer is 12."
    ]
];

$generator = new MoodleQuizGenerator($questions);
$generator->generateXML();
```

### D2L Quiz
```php
$questions = [
    [
        'text' => 'What is 2 + 2?',
        'choices' => ['3', '4', '5'],
        'correct_index' => 1,
        'solution' => '2 + 2 equals 4.'
    ]
];

$generator = new D2LQuizGenerator($questions);
$generator->saveToFile('d2l_quiz.xml');
```

### H5P Quiz
```php
use Waterloobae\QuizConverter\H5PQuizGenerator;

$questions = [
    [
        "type" => "multiple_choice",
        "text" => "What is \\(x^2\\) when \\(x = 2\\)?",
        "choices" => [
            ["text" => "\\(4\\)", "correct" => true],
            ["text" => "\\(8\\)", "correct" => false]
        ],
        "solution" => "Since \\(x = 2\\), we calculate \\(2^2 = 4\\)."
    ],
    [
        "type" => "blank",
        "text" => "Solve for \\(x\\) in \\(x + 3 = 5\\)",
        "answer" => 2,
        "solution" => "Subtract 3 from both sides."
    ]
];

$generator = new H5PQuizGenerator($questions);
$generator->generate();
```

### QTI Quiz (Multiple Versions)
```php
// QTI 2.1 (default)
$generator = new QTIQuizGenerator($questions, '2.1');
$generator->saveToFile('qti_2.1_quiz.xml');

// QTI 1.2
$generator = new QTIQuizGenerator($questions, '1.2');
$generator->saveToFile('qti_1.2_quiz.xml');

// QTI 2.2
$generator = new QTIQuizGenerator($questions, '2.2');
$generator->saveToFile('qti_2.2_quiz.xml');

// QTI 3.0
$generator = new QTIQuizGenerator($questions, '3.0');
$generator->saveToFile('qti_3.0_quiz.xml');
```

## Installation

```bash
composer install
```

## Requirements

- PHP 7.4 or higher
- ext-zip (for H5P package generation)
- ext-json (for JSON operations)
- Composer for dependency management

## Dependencies

- h5p/h5p-core: Core H5P functionality
- h5p/h5p-editor: H5P editor support
- h5p/h5p-multi-choice: Multiple choice question type
- h5p/h5p-question-set: Question set functionality
- h5p/h5p-arithmetic-quiz: Arithmetic quiz support
- h5p/h5p-math-display: Mathematical expression rendering

## Output Files

- **Canvas**: `canvas_quiz.xml` - QTI-compatible XML
- **Moodle**: `moodle_quiz.xml` - Moodle XML format
- **D2L**: `d2l_quiz.xml` - D2L XML format
- **H5P**: `quiz.h5p` - H5P package (ZIP archive)
- **QTI**: `qti_quiz.xml` - IMS QTI standard XML

## Notes

- H5P files are ZIP archives containing JSON and metadata
- All generators support HTML special character escaping
- QTI format provides the most universal compatibility
- Math expressions in H5P and Moodle use LaTeX notation
- H5P viewer implementation available in `public/index-old.html`

## License

MIT License
