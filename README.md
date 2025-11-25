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

## Laravel Integration

All quiz generator classes now extend Laravel's `Controller` base class, making them ready to use in Laravel applications. Each controller provides:

- **`generate()`**: Returns an HTTP Response with the generated quiz content
- **`download()`**: Returns an HTTP Response with appropriate headers for file download
- **`saveToFile($filename)`**: Saves the quiz to a file (for standalone/CLI usage)

### Example Laravel Routes

```php
// routes/web.php or routes/api.php
use Waterloobae\QuizConverter\CanvasQuizGenerator;
use Waterloobae\QuizConverter\MoodleQuizGenerator;
use Waterloobae\QuizConverter\D2LQuizGenerator;
use Waterloobae\QuizConverter\H5PQuizGenerator;
use Waterloobae\QuizConverter\QTIQuizGenerator;

Route::post('/quiz/canvas/generate', function(Request $request) {
    $generator = new CanvasQuizGenerator();
    $generator->setQuestions($request->input('questions'));
    return $generator->generate();
});

Route::post('/quiz/canvas/download', function(Request $request) {
    $generator = new CanvasQuizGenerator();
    $generator->setQuestions($request->input('questions'));
    return $generator->download();
});

Route::post('/quiz/moodle/download', function(Request $request) {
    $generator = new MoodleQuizGenerator();
    $generator->setQuestions($request->input('questions'));
    return $generator->download();
});

Route::post('/quiz/d2l/download', function(Request $request) {
    $generator = new D2LQuizGenerator();
    $generator->setQuestions($request->input('questions'));
    return $generator->download();
});

Route::post('/quiz/h5p/download', function(Request $request) {
    $generator = new H5PQuizGenerator();
    $generator->setQuestions($request->input('questions'));
    return $generator->download();
});

Route::post('/quiz/qti/download', function(Request $request) {
    $generator = new QTIQuizGenerator();
    $generator->setQuestions($request->input('questions'))
              ->setVersion($request->input('version', '2.1'));
    return $generator->download();
});
```

## Usage Examples

All quiz generators now extend Laravel's `Controller` class and can be used in Laravel applications as controllers.

### Canvas Quiz

**As a Laravel Controller:**
```php
use Waterloobae\QuizConverter\CanvasQuizGenerator;

// In a controller method
public function generateCanvasQuiz(Request $request) {
    $questions = $request->input('questions');
    
    $generator = new CanvasQuizGenerator();
    $generator->setQuestions($questions);
    
    // Returns XML as HTTP response
    return $generator->generate();
    
    // Or download as file
    return $generator->download();
}
```

**Standalone Usage:**
```php
use Waterloobae\QuizConverter\CanvasQuizGenerator;

$questions = [
    [
        'text' => 'What is 2 + 2?',
        'choices' => ['3', '4', '5'],
        'correct_index' => 1
    ]
];

$generator = new CanvasQuizGenerator();
$generator->setQuestions($questions);
$generator->saveToFile('canvas_quiz.xml');
```

### Moodle Quiz

**As a Laravel Controller:**
```php
use Waterloobae\QuizConverter\MoodleQuizGenerator;

// In a controller method
public function generateMoodleQuiz(Request $request) {
    $questions = $request->input('questions');
    
    $generator = new MoodleQuizGenerator();
    $generator->setQuestions($questions);
    
    // Returns XML as HTTP response
    return $generator->generate();
    
    // Or download as file
    return $generator->download();
}
```

**Standalone Usage:**
```php
use Waterloobae\QuizConverter\MoodleQuizGenerator;

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

$generator = new MoodleQuizGenerator();
$generator->setQuestions($questions);
$generator->saveToFile('moodle_quiz.xml');
```

### D2L Quiz

**As a Laravel Controller:**
```php
use Waterloobae\QuizConverter\D2LQuizGenerator;

// In a controller method
public function generateD2LQuiz(Request $request) {
    $questions = $request->input('questions');
    
    $generator = new D2LQuizGenerator();
    $generator->setQuestions($questions);
    
    // Returns XML as HTTP response
    return $generator->generate();
    
    // Or download as file
    return $generator->download();
}
```

**Standalone Usage:**
```php
use Waterloobae\QuizConverter\D2LQuizGenerator;

$questions = [
    [
        'text' => 'What is 2 + 2?',
        'choices' => ['3', '4', '5'],
        'correct_index' => 1,
        'solution' => '2 + 2 equals 4.'
    ]
];

$generator = new D2LQuizGenerator();
$generator->setQuestions($questions);
$generator->saveToFile('d2l_quiz.xml');
```

### H5P Quiz

**As a Laravel Controller:**
```php
use Waterloobae\QuizConverter\H5PQuizGenerator;

// In a controller method
public function generateH5PQuiz(Request $request) {
    $questions = $request->input('questions');
    
    $generator = new H5PQuizGenerator();
    $generator->setQuestions($questions);
    
    // Returns H5P file as download
    return $generator->download();
}
```

**Standalone Usage:**
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

$generator = new H5PQuizGenerator();
$generator->setQuestions($questions);
$generator->generate();
```

### QTI Quiz (Multiple Versions)

**As a Laravel Controller:**
```php
use Waterloobae\QuizConverter\QTIQuizGenerator;

// In a controller method
public function generateQTIQuiz(Request $request) {
    $questions = $request->input('questions');
    $version = $request->input('version', '2.1'); // Default to 2.1
    
    $generator = new QTIQuizGenerator();
    $generator->setQuestions($questions)
              ->setVersion($version);
    
    // Returns XML as HTTP response
    return $generator->generate();
    
    // Or download as file
    return $generator->download();
}
```

**Standalone Usage:**
```php
use Waterloobae\QuizConverter\QTIQuizGenerator;

$questions = [
    [
        'text' => 'What is 2 + 2?',
        'choices' => ['3', '4', '5'],
        'correct_index' => 1,
        'solution' => '2 + 2 equals 4.'
    ]
];

// QTI 2.1 (default)
$generator = new QTIQuizGenerator();
$generator->setQuestions($questions)->setVersion('2.1');
$generator->saveToFile('qti_2.1_quiz.xml');

// QTI 1.2
$generator = new QTIQuizGenerator();
$generator->setQuestions($questions)->setVersion('1.2');
$generator->saveToFile('qti_1.2_quiz.xml');

// QTI 2.2
$generator = new QTIQuizGenerator();
$generator->setQuestions($questions)->setVersion('2.2');
$generator->saveToFile('qti_2.2_quiz.xml');

// QTI 3.0
$generator = new QTIQuizGenerator();
$generator->setQuestions($questions)->setVersion('3.0');
$generator->saveToFile('qti_3.0_quiz.xml');
```

## Installation

### For Laravel Projects

```bash
composer require waterloobae/quizconverter
```

### Standalone Installation

```bash
composer install
```

## Requirements

- PHP 7.4 or higher
- ext-zip (for H5P package generation)
- ext-json (for JSON operations)
- Composer for dependency management
- Laravel 9.x, 10.x, or 11.x (when using as Laravel controllers)

## Dependencies

- **Laravel Dependencies:**
  - illuminate/http: HTTP response handling
  - illuminate/routing: Controller base class
- **H5P Dependencies:**
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
