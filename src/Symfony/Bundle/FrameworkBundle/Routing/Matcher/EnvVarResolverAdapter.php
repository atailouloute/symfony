<?php

namespace Symfony\Bundle\FrameworkBundle\Routing\Matcher;

use Symfony\Component\DependencyInjection\EnvVarResolver;
use Symfony\Component\Routing\Matcher\EnvVarResolverInterface;

class EnvVarResolverAdapter implements EnvVarResolverInterface
{
    private $resolver;

    public function __construct(EnvVarResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function getEnv(string $name)
    {
        return $this->resolver->getEnv($name);
    }
}
