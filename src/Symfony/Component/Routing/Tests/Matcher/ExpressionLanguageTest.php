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

class ExpressionLanguageTest extends TestCase
{
    private $envVarResolver;

    public function setUp()
    {
//        $this->envVarResolver = $this->getMockBuilder(EnvVarResolverInterface::class)->getMock();
//        $this->envVarResolver->method('getEnv')
//            ->willReturnMap([
//                ['APP_ENV', 'test'],
//                ['PHP_VERSION', '7.2'],
//            ]);
    }

    /**
     * @dataProvider provider
     */
    public function testEnv(string $expression, $expected): void
    {
        $expressionLanguage = new ExpressionLanguage();
        $expressionLanguageProvider = new ExpressionLanguageProvider();
        $expressionLanguageProvider->setEnvVarResolver($this->envVarResolver);
        $expressionLanguage->registerProvider($expressionLanguageProvider);

        $this->assertEquals($expected, $expressionLanguage->evaluate($expression));
    }

    public function provider(): array
    {
        return [
            ['env("APP_ENV")', 'test'],
            ['env("PHP_VERSION")', '7.2'],
            ['env("unknown_env_variable")', null],
            ['env("unknown_env_variable", "default")', 'default'],
        ];
    }
}
