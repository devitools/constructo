<?php

declare(strict_types=1);

namespace Constructo\Test\_;

use Constructo\Support\Reflective\Notation;
use PHPUnit\Framework\TestCase;

use function Constructo\Notation\adaify;
use function Constructo\Notation\camelify;
use function Constructo\Notation\cobolify;
use function Constructo\Notation\dotify;
use function Constructo\Notation\format;
use function Constructo\Notation\kebabify;
use function Constructo\Notation\lowerify;
use function Constructo\Notation\macroify;
use function Constructo\Notation\pascalify;
use function Constructo\Notation\sentencify;
use function Constructo\Notation\snakify;
use function Constructo\Notation\titlelify;
use function Constructo\Notation\trainify;
use function Constructo\Notation\upperify;

final class NotationFunctionsTest extends TestCase
{
    public function testSnakifyConvertsUpperCaseToSnakeCase(): void
    {
        $this->assertEquals('camel_case', snakify('camelCase'));
        $this->assertEquals('snake_case', snakify('SnakeCase'));
        $this->assertEquals('multiple_words_here', snakify('MultipleWordsHere'));
    }

    public function testSnakifyHandlesAlreadySnakeCaseStrings(): void
    {
        $this->assertEquals('already_snake', snakify('already_snake'));
    }

    public function testSnakifyHandlesEmptyString(): void
    {
        $this->assertEquals('', snakify(''));
    }

    public function testSnakifyNumber(): void
    {
        $this->assertEquals('super_line_1', snakify('superLine1'));
        $this->assertEquals('line_122', snakify('line122'));
        $this->assertEquals('123_line_122', snakify('123Line122'));
        $this->assertEquals('1_line', snakify('1Line'));

        $this->assertEquals('super_line1', snakify('superLine1', false));
        $this->assertEquals('line122', snakify('line122', false));
        $this->assertEquals('123_line122', snakify('123Line122', false));
        $this->assertEquals('1_line', snakify('1Line', false));
    }

    public function testCamelifyConvertsToLowerCamelCase(): void
    {
        $this->assertEquals('camelCase', camelify('camel_case'));
        $this->assertEquals('snakeCase', camelify('snake_case'));
        $this->assertEquals('multipleWordsHere', camelify('multiple_words_here'));
        $this->assertEquals('already', camelify('already'));
    }

    public function testPascalifyConvertsToUpperCamelCase(): void
    {
        $this->assertEquals('CamelCase', pascalify('camel_case'));
        $this->assertEquals('SnakeCase', pascalify('snake_case'));
        $this->assertEquals('MultipleWordsHere', pascalify('multiple_words_here'));
    }

    public function testKebabifyConvertsToKebabCase(): void
    {
        $this->assertEquals('camel-case', kebabify('camelCase'));
        $this->assertEquals('snake-case', kebabify('SnakeCase'));
        $this->assertEquals('already-kebab', kebabify('already-kebab'));
    }

    public function testFormatWithDifferentNotations(): void
    {
        $this->assertEquals('camel_case', format('camelCase', Notation::SNAKE));
        $this->assertEquals('camelCase', format('camel_case', Notation::CAMEL));
        $this->assertEquals('CamelCase', format('camel_case', Notation::PASCAL));
        $this->assertEquals('camel-case', format('camelCase', Notation::KEBAB));
        $this->assertEquals('CAMEL_CASE', format('camelCase', Notation::MACRO));
        $this->assertEquals('Camel_Case', format('camelCase', Notation::ADA));
        $this->assertEquals('camel.case', format('camelCase', Notation::DOT));
        $this->assertEquals('CAMEL-CASE', format('camelCase', Notation::COBOL));
        $this->assertEquals('camel case', format('camelCase', Notation::LOWER));
        $this->assertEquals('CAMEL CASE', format('camelCase', Notation::UPPER));
        $this->assertEquals('Camel Case', format('camelCase', Notation::TITLE));
        $this->assertEquals('Camel case', format('camelCase', Notation::SENTENCE));
        $this->assertEquals('original', format('original', Notation::NONE));
    }

    public function testUpperifyConvertsToUpperCase(): void
    {
        $this->assertEquals('CAMEL CASE', upperify('camelCase'));
        $this->assertEquals('SNAKE CASE', upperify('snake_case'));
        $this->assertEquals('ALREADY UPPER', upperify('ALREADY UPPER'));
    }

    public function testLowerifyConvertsToLowerCase(): void
    {
        $this->assertEquals('camel case', lowerify('camelCase'));
        $this->assertEquals('snake case', lowerify('snake_case'));
        $this->assertEquals('already lower', lowerify('already lower'));
    }

    public function testTitlelifyConvertsToTitleCase(): void
    {
        $this->assertEquals('Camel Case', titlelify('camelCase'));
        $this->assertEquals('Snake Case', titlelify('snake_case'));
        $this->assertEquals('Already Title', titlelify('Already Title'));
    }

    public function testTitlelifyConvertsToTrainCase(): void
    {
        $this->assertEquals('Camel-Case', trainify('camelCase'));
        $this->assertEquals('My-Name-Is-Bond', trainify('myNameIsBond'));
    }

    public function testAdaifyConvertsToAdaCase(): void
    {
        $this->assertEquals('Camel_Case', adaify('camelCase'));
        $this->assertEquals('Snake_Case', adaify('snake_case'));
        $this->assertEquals('Multiple_Words_Here', adaify('multipleWordsHere'));
    }

    public function testMacroifyConvertsToMacroCase(): void
    {
        $this->assertEquals('CAMEL_CASE', macroify('camelCase'));
        $this->assertEquals('SNAKE_CASE', macroify('snake_case'));
        $this->assertEquals('MULTIPLE_WORDS_HERE', macroify('multipleWordsHere'));
    }

    public function testCobolifyConvertsToCobolCase(): void
    {
        $this->assertEquals('CAMEL-CASE', cobolify('camelCase'));
        $this->assertEquals('SNAKE-CASE', cobolify('snake_case'));
        $this->assertEquals('MULTIPLE-WORDS-HERE', cobolify('multipleWordsHere'));
    }

    public function testDotifyConvertsToDotCase(): void
    {
        $this->assertEquals('camel.case', dotify('camelCase'));
        $this->assertEquals('snake.case', dotify('snake_case'));
        $this->assertEquals('multiple.words.here', dotify('multipleWordsHere'));
    }

    public function testSentencifyConvertsToSentenceCase(): void
    {
        $this->assertEquals('Camel case', sentencify('camelCase'));
        $this->assertEquals('Snake case', sentencify('snake_case'));
        $this->assertEquals('Multiple words here', sentencify('multipleWordsHere'));
    }
}
