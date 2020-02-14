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
    /** @var EnvVarResolverInterface */
    protected $envVarResolver;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new ExpressionFunction(
                'env',
                static function ($str, $default = 'null') {
                    return sprintf('($envVarResolver->getEnv(%s) ?? %s)', $str, $default);
                },
                function ($arguments, $str, $default = null) {
                    if (null === $this->envVarResolver) {
                        throw new \LogicException('You cannot use function "env" as no EnvVarResolver is provided.');
                    }

                    return $this->envVarResolver->getEnv($str) ?? null;
                }
            ),
        ];
    }

    public function setEnvVarResolver(EnvVarResolverInterface $envVarResolver)
    {
        $this->envVarResolver = $envVarResolver;
    }
}
