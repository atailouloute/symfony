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
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * Defines some ExpressionLanguage functions.
 *
 * @author Ahmed TAILOULOUTE <ahmed.tailouloute@gmail.com>
 */
class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    /** @var RequestContext */
    private $context;

    public function __construct(RouterInterface $router)
    {
        $this->context = $router->getContext();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction(
                'env',
                function ($str, $default = 'null') {
                    if (false === $this->context->hasParameter('get_env')) {
                        throw new \LogicException('You cannot use function "env" as no "get_env" is not available.');
                    }

                    return sprintf('(($context->getParameter(\'get_env\'))(%s) ?? %s)', $str, $default);
                },
                function ($arguments, $str, $default = 'null') {
                    if (false === $this->context->hasParameter('get_env')) {
                        throw new \LogicException('You cannot use function "env" as no "get_env" is not available.');
                    }

                    return ($this->context->getParameter('get_env'))($str) ?? $default;
                }
            ),
        ];
    }
}
