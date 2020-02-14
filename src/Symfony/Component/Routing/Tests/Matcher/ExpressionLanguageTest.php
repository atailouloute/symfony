<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests\Matcher;

use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\Matcher\ExpressionLanguageProvider;
use Symfony\Component\Routing\RequestContext;

class ExpressionLanguageTest extends TestCase
{
    private $context;
    private $expressionLanguage;

    public function setUp(): void
    {
        $this->context = new RequestContext();
        $this->context->setParameter('_functions', [
            // function with one arg
            'env' => static function (string $arg) {
                return [
                    'APP_ENV' => 'test',
                    'PHP_VERSION' => '7.2',
                ][$arg] ?? null;
            },
            // function with multiple args
            'sum' => static function ($a, $b) {
                return $a + $b;
            },
            // function with no arg
            'foo' => static function () {
                return 'bar';
            },
        ]);

        $this->expressionLanguage = new ExpressionLanguage();
        $this->expressionLanguage->registerProvider(new ExpressionLanguageProvider(['env', 'sum', 'foo']));
    }

    /**
     * @dataProvider compileProvider
     */
    public function testCompile(string $expression, string $expected)
    {
        $this->assertSame($expected, $this->expressionLanguage->compile($expression));
    }

    public function compileProvider(): iterable
    {
        return [
            ['env("APP_ENV")', '(($context->getParameter(\'_functions\')[\'env\'])("APP_ENV"))'],
            ['sum(1, 2)', '(($context->getParameter(\'_functions\')[\'sum\'])(1, 2))'],
            ['foo()', '(($context->getParameter(\'_functions\')[\'foo\'])())'],
        ];
    }

    /**
     * @dataProvider evaluateProvider
     */
    public function testEvaluate(string $expression, $expected)
    {
        $this->assertSame($expected, $this->expressionLanguage->evaluate($expression, ['context' => $this->context]));
    }

    public function evaluateProvider(): array
    {
        return [
            ['env("APP_ENV")', 'test'],
            ['env("PHP_VERSION")', '7.2'],
            ['env("unknown_env_variable")', null],
            ['sum(1, 2)', 3],
            ['foo()', 'bar'],
        ];
    }
}
