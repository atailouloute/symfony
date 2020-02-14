<?php

namespace Symfony\Component\DependencyInjection;

use Symfony\Component\DependencyInjection\Exception\EnvNotFoundException;
use Symfony\Component\DependencyInjection\Exception\ParameterCircularReferenceException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class EnvVarResolver
{
    protected $resolving = [];
    private $envCache = [];
    private $getEnv;

    private $processors;
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag, $processors = null)
    {
        $this->parameterBag = $parameterBag;
        $this->processors = $processors ?? new ServiceLocator([]);
    }

    /**
     * Fetches a variable from the environment.
     *
     * @param string $name The name of the environment variable
     *
     * @return mixed The value to use for the provided environment variable name
     *
     * @throws EnvNotFoundException When the environment variable is not found and has no default value
     */
    public function getEnv($name)
    {
        if (isset($this->resolving[$envName = "env($name)"])) {
            throw new ParameterCircularReferenceException(array_keys($this->resolving));
        }
        if (isset($this->envCache[$name]) || \array_key_exists($name, $this->envCache)) {
            return $this->envCache[$name];
        }

        if (!$this->getEnv) {
            $this->getEnv = new \ReflectionMethod($this, __FUNCTION__);
            $this->getEnv->setAccessible(true);
            $this->getEnv = $this->getEnv->getClosure($this);
        }

        if (false !== $i = strpos($name, ':')) {
            $prefix = substr($name, 0, $i);
            $localName = substr($name, 1 + $i);
        } else {
            $prefix = 'string';
            $localName = $name;
        }

        $processor = $this->processors->has($prefix) ? $this->processors->get($prefix) : new EnvVarProcessor($this->parameterBag);

        $this->resolving[$envName] = true;
        try {
            return $this->envCache[$name] = $processor->getEnv($prefix, $localName, $this->getEnv);
        } finally {
            unset($this->resolving[$envName]);
        }
    }
}
