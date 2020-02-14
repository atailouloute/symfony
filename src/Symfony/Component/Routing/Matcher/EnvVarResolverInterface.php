<?php

namespace Symfony\Component\Routing\Matcher;

interface EnvVarResolverInterface
{
    public function getEnv(string $name);
}
