<?php

namespace OpenVPN\Interfaces;

interface GeneratorInterface
{
    /**
     * Generate config by parameters in memory
     *
     * @return string
     */
    public function generate(): string;
}
