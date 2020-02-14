<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Matcher;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * Defines some ExpressionLanguage functions.
 *
 * @author Ahmed TAILOULOUTE <ahmed.tailouloute@gmail.com>
 */
class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    private $functions;

    /**
     * @param string[] $functions
     */
    public function __construct(array $functions)
    {
        $this->functions = $functions;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        foreach ($this->functions as $function) {
            yield new ExpressionFunction(
                $function,
                static function (...$args) use ($function) {
                    return sprintf('(($context->getParameter(\'_functions\')[%s])(%s))', var_export($function, true), implode(', ', $args));
                },
                function ($values, ...$args) use ($function) {
                    $context = $values['context'];

                    return ($context->getParameter('_functions')[$function])(...$args);
                }
            );
        }
    }
}
