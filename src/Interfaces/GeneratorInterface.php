<?php

namespace OpenVPN\Interfaces;

interface GeneratorInterface
{
    /**
     * Generate config by parameters in memory
     *
     * @param string $type Type of generated config: raw (default), json
     *
     * @return array|string|null
     */
    public function generate(string $type = 'raw');
}
