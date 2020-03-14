<?php

namespace OpenVPN\Laravel;

use OpenVPN\Generator;
use OpenVPN\Interfaces\ConfigInterface;
use OpenVPN\Interfaces\GeneratorInterface;

class GeneratorWrapper
{
    /**
     * Get object of generator
     *
     * @param \OpenVPN\Interfaces\ConfigInterface|null $config
     *
     * @return \OpenVPN\Interfaces\GeneratorInterface
     */
    public function getGenerator(ConfigInterface $config = null): GeneratorInterface
    {
        return new Generator($config);
    }
}
